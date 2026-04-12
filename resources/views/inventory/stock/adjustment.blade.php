@extends('components.layouts.app')

@section('title', 'Tambah Mutasi Stok')
@section('subtitle', 'Catat pergerakan stok barang')

@section('content')
<div class="max-w-2xl" x-data="stockForm()">

    <a href="{{ route('inventory.stock-movements.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali ke Riwayat
    </a>

    <form method="POST" action="{{ route('inventory.stock-movements.store') }}">
        @csrf

        <div class="stat-card p-6 space-y-5">
            <h3 class="font-bold text-slate-800">Form Mutasi Stok</h3>

            {{-- Pilih Produk --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Produk <span class="text-red-400">*</span></label>
                <select name="product_id" required x-model="selectedProductId" @change="updateProductInfo()"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('product_id') border-red-300 @enderror">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            data-stock="{{ $product->stock }}"
                            data-unit="{{ $product->unit }}"
                            data-sku="{{ $product->sku }}"
                            {{ (old('product_id', $selectedProduct?->id) == $product->id) ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }}) — Stok: {{ $product->stock }} {{ $product->unit }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Info Stok Saat Ini --}}
            <div x-show="currentStock !== null" x-cloak class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                <div class="flex items-center gap-4">
                    <div>
                        <p class="text-xs text-blue-600 font-semibold">Stok Saat Ini</p>
                        <p class="text-2xl font-bold text-blue-800" x-text="currentStock + ' ' + currentUnit"></p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-xs text-blue-600 font-semibold">SKU</p>
                        <p class="text-sm font-mono text-blue-800" x-text="currentSku"></p>
                    </div>
                </div>
            </div>

            {{-- Tipe Mutasi --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-3">Tipe Mutasi <span class="text-red-400">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @php
                        $typeOptions = [
                            ['value' => 'in', 'label' => 'Barang Masuk', 'desc' => 'Pembelian / restok', 'color' => 'emerald', 'icon' => 'M12 4.5v15m7.5-7.5h-15'],
                            ['value' => 'out', 'label' => 'Barang Keluar', 'desc' => 'Keluar non-penjualan', 'color' => 'red', 'icon' => 'M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75'],
                            ['value' => 'adjustment', 'label' => 'Penyesuaian', 'desc' => 'Koreksi stok opname', 'color' => 'blue', 'icon' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z'],
                            ['value' => 'return', 'label' => 'Retur', 'desc' => 'Barang kembali', 'color' => 'amber', 'icon' => 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3'],
                            ['value' => 'damaged', 'label' => 'Rusak/Expired', 'desc' => 'Barang tidak layak', 'color' => 'slate', 'icon' => 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79'],
                        ];
                    @endphp

                    @foreach($typeOptions as $opt)
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="{{ $opt['value'] }}" x-model="selectedType" class="hidden peer"
                                {{ old('type') === $opt['value'] ? 'checked' : '' }}>
                            <div class="p-3 rounded-xl border-2 border-slate-200 peer-checked:border-{{ $opt['color'] }}-500 peer-checked:bg-{{ $opt['color'] }}-50 transition-all hover:border-slate-300">
                                <svg class="w-5 h-5 text-{{ $opt['color'] }}-500 mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $opt['icon'] }}"/></svg>
                                <p class="text-sm font-semibold text-slate-800">{{ $opt['label'] }}</p>
                                <p class="text-[11px] text-slate-400">{{ $opt['desc'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah <span class="text-red-400">*</span></label>
                <div class="flex items-center gap-3">
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required x-model.number="quantity"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('quantity') border-red-300 @enderror"
                        placeholder="Masukkan jumlah">
                    <span class="text-sm font-semibold text-slate-500 whitespace-nowrap" x-text="currentUnit || 'unit'"></span>
                </div>
                @error('quantity')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Preview Stok Setelah --}}
            <div x-show="selectedProductId && quantity > 0 && selectedType" x-cloak
                class="p-4 rounded-xl border"
                :class="stockAfter >= 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100'">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold" :class="stockAfter >= 0 ? 'text-emerald-600' : 'text-red-600'">Preview Stok Setelah Mutasi</p>
                        <p class="text-2xl font-bold" :class="stockAfter >= 0 ? 'text-emerald-800' : 'text-red-800'" x-text="stockAfter + ' ' + currentUnit"></p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-slate-500" x-text="currentStock"></span>
                        <span class="text-sm font-bold mx-1" :class="isPositiveType ? 'text-emerald-600' : 'text-red-600'" x-text="isPositiveType ? '+ ' + quantity : '- ' + quantity"></span>
                        <span class="text-sm text-slate-500">= </span>
                        <span class="text-sm font-bold" :class="stockAfter >= 0 ? 'text-emerald-700' : 'text-red-700'" x-text="stockAfter"></span>
                    </div>
                </div>
                <p x-show="stockAfter < 0" class="text-xs text-red-600 mt-2 font-semibold">Stok tidak boleh negatif. Kurangi jumlah.</p>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                    placeholder="Contoh: Restok dari supplier PT Maju Jaya">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('inventory.stock-movements.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit"
                :disabled="!selectedProductId || !selectedType || quantity <= 0 || stockAfter < 0"
                class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-slate-300 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl transition-colors">
                Simpan Mutasi
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function stockForm() {
    return {
        selectedProductId: '{{ old('product_id', $selectedProduct?->id ?? '') }}',
        selectedType: '{{ old('type', '') }}',
        quantity: {{ old('quantity', 1) }},
        currentStock: null,
        currentUnit: '',
        currentSku: '',

        init() {
            if (this.selectedProductId) {
                this.updateProductInfo();
            }
        },

        updateProductInfo() {
            const select = document.querySelector('select[name="product_id"]');
            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                this.currentStock = parseInt(option.dataset.stock) || 0;
                this.currentUnit = option.dataset.unit || 'pcs';
                this.currentSku = option.dataset.sku || '';
            } else {
                this.currentStock = null;
                this.currentUnit = '';
                this.currentSku = '';
            }
        },

        get isPositiveType() {
            return ['in', 'return'].includes(this.selectedType);
        },

        get stockAfter() {
            if (this.currentStock === null || !this.selectedType) return 0;
            return this.isPositiveType
                ? this.currentStock + this.quantity
                : this.currentStock - this.quantity;
        }
    }
}
</script>
@endpush
@endsection