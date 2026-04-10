@extends('components.layouts.app')

@section('title', 'Edit Produk')
@section('subtitle', $product->name . ' — ' . $product->sku)

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('inventory.products.update', $product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Dasar --}}
                <div class="stat-card p-6">
                    <h3 class="font-bold text-slate-800 mb-4">Informasi Produk</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Produk <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('name') border-red-300 @enderror">
                                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori <span class="text-red-400">*</span></label>
                                <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Supplier</label>
                                <select name="supplier_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                    <option value="">— Tidak ada —</option>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                            <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('barcode') border-red-300 @enderror">
                            @error('barcode')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Harga --}}
                <div class="stat-card p-6">
                    <h3 class="font-bold text-slate-800 mb-4">Harga</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Harga Beli <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                                <input type="number" name="buy_price" value="{{ old('buy_price', $product->buy_price) }}" required min="0"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('buy_price') border-red-300 @enderror">
                            </div>
                            @error('buy_price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Harga Jual <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                                <input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" required min="0"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('sell_price') border-red-300 @enderror">
                            </div>
                            @error('sell_price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Harga Grosir</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">Rp</span>
                                <input type="number" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" min="0"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stok --}}
                <div class="stat-card p-6">
                    <h3 class="font-bold text-slate-800 mb-4">Stok & Satuan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Stok Saat Ini</label>
                            <input type="number" value="{{ $product->stock }}" disabled
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-sm text-slate-500 cursor-not-allowed">
                            <p class="text-xs text-slate-400 mt-1">Gunakan Mutasi Stok untuk mengubah</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Min. Stok <span class="text-red-400">*</span></label>
                            <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" required min="0"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Satuan <span class="text-red-400">*</span></label>
                            <select name="unit" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                @foreach(['pcs' => 'Pcs', 'kg' => 'Kg', 'ltr' => 'Ltr', 'box' => 'Box', 'dus' => 'Dus', 'pack' => 'Pack', 'lusin' => 'Lusin', 'meter' => 'Meter', 'roll' => 'Roll'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('unit', $product->unit) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right --}}
            <div class="space-y-6">
                {{-- SKU Display --}}
                <div class="stat-card p-6">
                    <h3 class="font-bold text-slate-800 mb-2">SKU</h3>
                    <p class="text-lg font-mono font-bold text-blue-600">{{ $product->sku }}</p>
                    <p class="text-xs text-slate-400 mt-1">SKU tidak dapat diubah</p>
                </div>

                {{-- Image --}}
                <div class="stat-card p-6" x-data="{ preview: null }">
                    <h3 class="font-bold text-slate-800 mb-4">Gambar Produk</h3>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-blue-400 transition-colors">
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-48 object-contain rounded-lg mb-3">
                        </template>
                        <template x-if="!preview">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="w-full h-48 object-contain rounded-lg mb-3">
                            @else
                                <div class="py-8">
                                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12.75A2.25 2.25 0 005.25 21z"/></svg>
                                    <p class="text-xs text-slate-400">Belum ada gambar</p>
                                </div>
                            @endif
                        </template>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-500 hover:file:bg-blue-100"
                            @change="preview = URL.createObjectURL($event.target.files[0])">
                    </div>
                </div>

                {{-- Options --}}
                <div class="stat-card p-6">
                    <h3 class="font-bold text-slate-800 mb-4">Opsi</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-blue-500 focus:ring-blue-500/30">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Aktif</p>
                                <p class="text-xs text-slate-400">Produk bisa dijual di POS</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_taxable" value="0">
                            <input type="checkbox" name="is_taxable" value="1" {{ old('is_taxable', $product->is_taxable) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-blue-500 focus:ring-blue-500/30">
                            <div>
                                <p class="text-sm font-semibold text-slate-700">Kena PPN</p>
                                <p class="text-xs text-slate-400">Dikenakan pajak saat penjualan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('inventory.products.index') }}" class="flex-1 text-center px-4 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">Perbarui</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection