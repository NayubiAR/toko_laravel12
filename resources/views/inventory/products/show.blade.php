@extends('components.layouts.app')

@section('title', $product->name)
@section('subtitle', 'SKU: ' . $product->sku)

@section('content')
<div class="max-w-5xl">

    {{-- Back Button --}}
    <a href="{{ route('inventory.products.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali ke Daftar Produk
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Product Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Product Card --}}
            <div class="stat-card p-6">
                <div class="flex flex-col sm:flex-row gap-6">
                    {{-- Image --}}
                    <div class="w-full sm:w-40 h-40 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12.75A2.25 2.25 0 005.25 21z"/></svg>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">{{ $product->name }}</h2>
                                <p class="text-sm text-slate-400 font-mono">{{ $product->sku }}</p>
                            </div>
                            <div class="flex gap-2">
                                @if($product->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">Nonaktif</span>
                                @endif
                                @if($product->is_taxable)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">PPN</span>
                                @endif
                            </div>
                        </div>
                        @if($product->description)
                            <p class="text-sm text-slate-600 mb-4">{{ $product->description }}</p>
                        @endif
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-slate-400">Kategori</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $product->category?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Supplier</p>
                                <p class="text-sm font-semibold text-slate-700">{{ $product->supplier?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Satuan</p>
                                <p class="text-sm font-semibold text-slate-700">{{ strtoupper($product->unit) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Barcode</p>
                                <p class="text-sm font-semibold text-slate-700 font-mono">{{ $product->barcode ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Movement History --}}
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Riwayat Mutasi Stok</h3>
                @if($product->stockMovements->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Tanggal</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Tipe</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-center">Qty</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-center">Stok</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Oleh</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($product->stockMovements as $movement)
                            <tr>
                                <td class="py-3 text-slate-600">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $movement->type->isPositive() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $movement->type->label() }}
                                    </span>
                                </td>
                                <td class="py-3 text-center font-bold {{ $movement->quantity > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </td>
                                <td class="py-3 text-center text-slate-600">{{ $movement->stock_before }} → {{ $movement->stock_after }}</td>
                                <td class="py-3 text-slate-600">{{ $movement->user?->name ?? '—' }}</td>
                                <td class="py-3 text-slate-400 text-xs">{{ $movement->notes ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                    <p class="text-sm text-slate-400">Belum ada riwayat mutasi stok</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Right: Price & Stock Cards --}}
        <div class="space-y-6">
            {{-- Harga --}}
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Harga</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Harga Beli</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $product->formatted_buy_price }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Harga Jual</span>
                        <span class="text-lg font-bold text-blue-600">{{ $product->formatted_sell_price }}</span>
                    </div>
                    @if($product->wholesale_price)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Harga Grosir</span>
                        <span class="text-sm font-semibold text-slate-700">Rp {{ number_format($product->wholesale_price, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr class="border-slate-100">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Margin</span>
                        <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($product->profit, 0, ',', '.') }} ({{ $product->profit_margin }}%)</span>
                    </div>
                </div>
            </div>

            {{-- Stok --}}
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Stok</h3>
                <div class="text-center mb-4">
                    <p class="text-4xl font-bold {{ $product->stock <= 0 ? 'text-red-600' : ($product->is_low_stock ? 'text-amber-600' : 'text-emerald-600') }}">{{ $product->stock }}</p>
                    <p class="text-sm text-slate-400">{{ strtoupper($product->unit) }}</p>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500">Minimum Stok</span>
                    <span class="font-semibold text-slate-700">{{ $product->min_stock }}</span>
                </div>
                @if($product->is_low_stock)
                <div class="mt-3 p-3 rounded-lg {{ $product->stock <= 0 ? 'bg-red-50' : 'bg-amber-50' }}">
                    <p class="text-xs font-semibold {{ $product->stock <= 0 ? 'text-red-700' : 'text-amber-700' }}">
                        {{ $product->stock <= 0 ? 'Stok habis! Segera lakukan pembelian.' : 'Stok menipis, pertimbangkan untuk restock.' }}
                    </p>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('inventory.products.edit', $product) }}" class="flex-1 text-center px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">Edit</a>
                <form method="POST" action="{{ route('inventory.products.destroy', $product) }}" onsubmit="return confirm('Yakin ingin menghapus?')" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 border border-red-200 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-50 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection