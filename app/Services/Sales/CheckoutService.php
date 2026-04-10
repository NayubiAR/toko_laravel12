<?php

namespace App\Services\Sales;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Events\SaleCompleted;
use App\Exceptions\InsufficientStockException;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting;
use App\Support\Helpers\InvoiceNumberGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * Proses checkout dari POS.
     *
     * @param array $cartItems      [['product_id' => 1, 'quantity' => 2, 'discount' => 0], ...]
     * @param array $paymentData    ['method' => 'cash', 'paid_amount' => 100000]
     * @param int|null $customerId  Member ID (nullable)
     * @param float $discountPercent Diskon total (%)
     * @param string|null $notes
     * @return Sale
     */
    public function process(
        array $cartItems,
        array $paymentData,
        ?int $customerId = null,
        float $discountPercent = 0,
        ?string $notes = null
    ): Sale {
        return DB::transaction(function () use ($cartItems, $paymentData, $customerId, $discountPercent, $notes) {

            // ── 1. Validasi stok ──
            $this->validateStock($cartItems);

            // ── 2. Hitung total ──
            $taxRate = (float) Setting::get('tax_rate', 11);
            $calculation = $this->calculateTotals($cartItems, $taxRate, $discountPercent, $customerId);

            // ── 3. Hitung pembayaran ──
            $paidAmount = (float) ($paymentData['paid_amount'] ?? 0);
            $method = PaymentMethod::from($paymentData['method']);
            $changeAmount = max(0, $paidAmount - $calculation['grand_total']);

            // Untuk non-cash, paid = grand_total
            if ($method !== PaymentMethod::Cash) {
                $paidAmount = $calculation['grand_total'];
                $changeAmount = 0;
            }

            // ── 4. Simpan Sale ──
            $salePaymentStatus = $method === PaymentMethod::Cash
                ? PaymentStatus::Paid
                : PaymentStatus::Pending;

            $sale = Sale::create([
                'invoice_number'  => InvoiceNumberGenerator::sale(),
                'user_id'         => Auth::id(),
                'customer_id'     => $customerId,
                'subtotal'        => $calculation['subtotal'],
                'discount_amount' => $calculation['discount_amount'],
                'discount_percent'=> $discountPercent,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $calculation['tax_amount'],
                'grand_total'     => $calculation['grand_total'],
                'paid_amount'     => $paidAmount,
                'change_amount'   => $changeAmount,
                'payment_method'  => $method->value,
                'payment_status'  => $salePaymentStatus->value,
                'points_earned'   => 0,
                'points_used'     => $calculation['points_used'] ?? 0,
                'points_discount' => $calculation['points_discount'] ?? 0,
                'notes'           => $notes,
            ]);

            // ── 5. Simpan Sale Items ──
            foreach ($calculation['items'] as $item) {
                SaleItem::create([
                    'sale_id'      => $sale->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku'  => $item['product_sku'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'buy_price'    => $item['buy_price'],
                    'discount'     => $item['discount'],
                    'tax_amount'   => $item['tax_amount'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            // ── 6. Simpan Payment ──
            // Catatan: tabel payments.status pakai 'success', bukan 'paid'
            $isPaymentSuccess = $method === PaymentMethod::Cash;

            Payment::create([
                'sale_id'          => $sale->id,
                'method'           => $method->value,
                'amount'           => $paidAmount,
                'reference_number' => $paymentData['reference_number'] ?? null,
                'status'           => $isPaymentSuccess ? 'success' : 'pending',
                'paid_at'          => $isPaymentSuccess ? now() : null,
            ]);

            // ── 7. Load relasi untuk event ──
            $sale->load('items.product', 'customer');

            // ── 8. Fire event (deduct stock, cash flow, loyalty) ──
            if ($salePaymentStatus === PaymentStatus::Paid) {
                event(new SaleCompleted($sale));
            }

            // ── 9. Log aktivitas ──
            activity('sale')
                ->causedBy(Auth::user())
                ->performedOn($sale)
                ->withProperties([
                    'invoice' => $sale->invoice_number,
                    'total'   => $sale->grand_total,
                    'items'   => $sale->items->count(),
                    'method'  => $method->label(),
                ])
                ->log('Transaksi baru');

            return $sale;
        });
    }

    /**
     * Validasi ketersediaan stok.
     */
    private function validateStock(array $cartItems): void
    {
        foreach ($cartItems as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->stock < $item['quantity']) {
                throw new InsufficientStockException(
                    "Stok \"{$product->name}\" tidak cukup. Tersedia: {$product->stock} {$product->unit}, diminta: {$item['quantity']}"
                );
            }
        }
    }

    /**
     * Hitung semua total: subtotal, diskon, pajak, grand total.
     */
    private function calculateTotals(array $cartItems, float $taxRate, float $discountPercent, ?int $customerId): array
    {
        $items = [];
        $subtotal = 0;

        // Hitung member discount
        $memberDiscount = 0;
        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $memberDiscount = $customer->discount_percent;
            }
        }

        // Total discount = manual + member tier
        $totalDiscountPercent = $discountPercent + $memberDiscount;

        foreach ($cartItems as $cartItem) {
            $product = Product::findOrFail($cartItem['product_id']);
            $qty = (int) $cartItem['quantity'];
            $itemDiscount = (float) ($cartItem['discount'] ?? 0);

            $lineTotal = ($product->sell_price * $qty) - $itemDiscount;
            $subtotal += $lineTotal;

            $items[] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'product_sku'  => $product->sku,
                'quantity'     => $qty,
                'unit_price'   => $product->sell_price,
                'buy_price'    => $product->buy_price,
                'discount'     => $itemDiscount,
                'tax_amount'   => 0, // Dihitung di bawah
                'subtotal'     => $lineTotal,
            ];
        }

        // Hitung diskon total
        $discountAmount = $subtotal * ($totalDiscountPercent / 100);
        $afterDiscount = $subtotal - $discountAmount;

        // Hitung pajak
        $taxAmount = $afterDiscount * ($taxRate / 100);

        // Grand total
        $grandTotal = $afterDiscount + $taxAmount;

        // Distribusi pajak per item (proporsional)
        foreach ($items as &$item) {
            $proportion = $subtotal > 0 ? ($item['subtotal'] / $subtotal) : 0;
            $item['tax_amount'] = round($taxAmount * $proportion, 2);
        }

        return [
            'items'           => $items,
            'subtotal'        => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'tax_amount'      => round($taxAmount, 2),
            'grand_total'     => round($grandTotal, 2),
        ];
    }
}