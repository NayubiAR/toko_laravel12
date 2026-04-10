<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Support\Helpers\SkuGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'supplier'])
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->when($request->category, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->supplier, function ($query, $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($request->stock_status, function ($query, $status) {
                match ($status) {
                    'low'      => $query->lowStock()->where('stock', '>', 0),
                    'out'      => $query->outOfStock(),
                    'normal'   => $query->where('stock', '>', \Illuminate\Support\Facades\DB::raw('min_stock')),
                    default    => null,
                };
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();
        $suppliers  = Supplier::active()->orderBy('name')->get();

        // Stats
        $totalProducts = Product::active()->count();
        $lowStockCount = Product::active()->lowStock()->count();
        $outOfStockCount = Product::active()->outOfStock()->count();

        return view('inventory.products.index', compact(
            'products', 'categories', 'suppliers',
            'totalProducts', 'lowStockCount', 'outOfStockCount'
        ));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $suppliers  = Supplier::active()->orderBy('name')->get();

        return view('inventory.products.create', compact('categories', 'suppliers'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        // Auto-generate SKU berdasarkan kategori
        $category = Category::findOrFail($validated['category_id']);
        $validated['sku'] = SkuGenerator::generate($category->name);

        // Upload gambar
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_taxable'] = $request->boolean('is_taxable', true);

        Product::create($validated);

        return redirect()->route('inventory.products.index')
            ->with('success', "Produk \"{$validated['name']}\" berhasil ditambahkan dengan SKU: {$validated['sku']}");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'stockMovements' => function ($query) {
            $query->with('user')->latest()->limit(20);
        }]);

        return view('inventory.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        $suppliers  = Supplier::active()->orderBy('name')->get();

        return view('inventory.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        // Upload gambar baru (jika ada)
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_taxable'] = $request->boolean('is_taxable', true);

        $product->update($validated);

        return redirect()->route('inventory.products.index')
            ->with('success', "Produk \"{$product->name}\" berhasil diperbarui.");
    }

    public function destroy(Product $product)
    {
        // Soft delete agar data historis tetap ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('inventory.products.index')
            ->with('success', "Produk \"{$product->name}\" berhasil dihapus.");
    }
}