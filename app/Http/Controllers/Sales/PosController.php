<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Sales\CheckoutService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {}

    /**
     * Halaman POS utama.
     */
    public function index(Request $request)
    {
        $categories = Category::active()->orderBy('name')->get();
        $customers = Customer::active()->orderBy('name')->get();
        $taxRate = (float) Setting::get('tax_rate', 11);

        // Load produk (filter by search & kategori)
        $products = Product::active()
            ->where('stock', '>', 0)
            ->with('category')
            ->when($request->search, function ($q, $s) {
                $q->search($s);
            })
            ->when($request->category, function ($q, $c) {
                $q->where('category_id', $c);
            })
            ->orderBy('name')
            ->get();

        // Map untuk JSON di frontend (hindari arrow function di Blade)
        $productsJson = $products->map(function ($p) {
            return [
                'id'          => $p->id,
                'name'        => $p->name,
                'sku'         => $p->sku,
                'sell_price'  => (float) $p->sell_price,
                'stock'       => $p->stock,
                'unit'        => $p->unit,
                'category_id' => $p->category_id,
                'category'    => $p->category ? $p->category->name : null,
                'image'       => $p->image ? '/storage/' . $p->image : null,
            ];
        });

        return view('pos.index', compact('products', 'productsJson', 'categories', 'customers', 'taxRate'));
    }

    /**
     * Proses checkout.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.discount'   => 'nullable|numeric|min:0',
            'payment_method'     => 'required|in:cash,qris,bank_transfer,debit_card,credit_card',
            'paid_amount'        => 'required_if:payment_method,cash|numeric|min:0',
            'customer_id'        => 'nullable|exists:customers,id',
            'discount_percent'   => 'nullable|numeric|min:0|max:100',
            'notes'              => 'nullable|string|max:500',
        ]);

        try {
            $sale = $this->checkoutService->process(
                cartItems: $request->items,
                paymentData: [
                    'method'           => $request->payment_method,
                    'paid_amount'      => $request->paid_amount ?? 0,
                    'reference_number' => $request->reference_number,
                ],
                customerId: $request->customer_id,
                discountPercent: $request->discount_percent ?? 0,
                notes: $request->notes,
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'data'    => [
                    'id'             => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'grand_total'    => $sale->grand_total,
                    'paid_amount'    => $sale->paid_amount,
                    'change_amount'  => $sale->change_amount,
                    'payment_method' => $sale->payment_method->label(),
                    'payment_status' => $sale->payment_status->label(),
                    'points_earned'  => $sale->points_earned,
                    'items_count'    => $sale->items->count(),
                ],
            ]);

        } catch (InsufficientStockException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Checkout Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses transaksi.',
                'error'   => $e->getMessage(),
                'file'    => $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }
    }

    /**
     * API: Search produk untuk POS.
     */
    public function searchProducts(Request $request)
    {
        $products = Product::active()
            ->where('stock', '>', 0)
            ->when($request->q, fn($q, $s) => $q->search($s))
            ->when($request->category, fn($q, $c) => $q->where('category_id', $c))
            ->with('category')
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'name'       => $p->name,
                'sku'        => $p->sku,
                'sell_price' => $p->sell_price,
                'stock'      => $p->stock,
                'unit'       => $p->unit,
                'category'   => $p->category?->name,
                'image'      => $p->image ? asset('storage/' . $p->image) : null,
            ]);

        return response()->json($products);
    }
}