<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Riwayat semua mutasi stok.
     */
    public function index(Request $request)
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->product_id, function ($query, $productId) {
                $query->where('product_id', $productId);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $products = Product::active()->orderBy('name')->get();
        $types = StockMovementType::cases();

        return view('inventory.stock.movements', compact('movements', 'products', 'types'));
    }

    /**
     * Form tambah stok / adjustment.
     */
    public function create(Request $request)
    {
        $products = Product::active()->orderBy('name')->get();
        $selectedProduct = null;

        if ($request->product_id) {
            $selectedProduct = Product::find($request->product_id);
        }

        return view('inventory.stock.adjustment', compact('products', 'selectedProduct'));
    }

    /**
     * Proses mutasi stok.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:in,out,adjustment,return,damaged',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $type = StockMovementType::from($validated['type']);
        $qty = (int) $validated['quantity'];

        // Tentukan apakah stok bertambah atau berkurang
        $isPositive = in_array($type, [StockMovementType::In, StockMovementType::Return]);
        $quantityChange = $isPositive ? $qty : -$qty;

        // Validasi: stok tidak boleh jadi negatif
        $stockAfter = $product->stock + $quantityChange;
        if ($stockAfter < 0) {
            return back()->withInput()->with('error', 
                "Stok tidak cukup. Stok saat ini: {$product->stock} {$product->unit}, dikurangi: {$qty}"
            );
        }

        DB::transaction(function () use ($product, $type, $qty, $quantityChange, $stockAfter, $validated) {
            $stockBefore = $product->stock;

            // Update stok produk
            $product->update(['stock' => $stockAfter]);

            // Catat mutasi
            StockMovement::create([
                'product_id'     => $product->id,
                'type'           => $type->value,
                'quantity'       => $quantityChange,
                'stock_before'   => $stockBefore,
                'stock_after'    => $stockAfter,
                'reference_type' => null,
                'reference_id'   => null,
                'cost_per_unit'  => $product->buy_price,
                'notes'          => $validated['notes'],
                'user_id'        => Auth::id(),
            ]);

            // Log aktivitas
            activity('stock')
                ->causedBy(Auth::user())
                ->performedOn($product)
                ->withProperties([
                    'type'         => $type->label(),
                    'quantity'     => $quantityChange,
                    'stock_before' => $stockBefore,
                    'stock_after'  => $stockAfter,
                ])
                ->log("Mutasi stok: {$type->label()}");
        });

        $typeLabel = $type->label();
        return redirect()->route('inventory.stock-movements.index')
            ->with('success', "Mutasi stok berhasil. {$typeLabel}: {$qty} {$product->unit} untuk \"{$product->name}\". Stok sekarang: {$stockAfter}");
    }
}