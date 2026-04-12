@extends('components.layouts.app')

@section('title', 'Mutasi Stok')
@section('subtitle', 'Riwayat pergerakan stok barang')

@section('content')

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-52">
            </div>
            {{-- Filter Tipe --}}
            <select name="type" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <option value="">Semua Tipe</option>
                @foreach($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
            {{-- Filter Produk --}}
            <select name="product_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                <option value="">Semua Produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
            {{-- Filter Tanggal --}}
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            <button type="submit" class="px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">Filter</button>
            @if(request()->hasAny(['search', 'type', 'product_id', 'date_from', 'date_to']))
                <a href="{{ route('inventory.stock-movements.index') }}" class="text-sm text-slate-500 hover:text-red-500">Reset</a>
            @endif
        </form>

        <a href="{{ route('inventory.stock-movements.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Mutasi
        </a>
    </div>

    {{-- Table --}}
    <div class="stat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Produk</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Tipe</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Qty</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Stok</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Oleh</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-slate-800 font-medium">{{ $movement->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $movement->created_at->format('H:i') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $movement->product?->name ?? '-' }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $movement->product?->sku ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $typeColors = [
                                    'in' => 'bg-emerald-100 text-emerald-700',
                                    'out' => 'bg-red-100 text-red-700',
                                    'adjustment' => 'bg-blue-100 text-blue-700',
                                    'return' => 'bg-amber-100 text-amber-700',
                                    'damaged' => 'bg-slate-100 text-slate-700',
                                    'transfer' => 'bg-violet-100 text-violet-700',
                                ];
                                $colorClass = $typeColors[$movement->type->value] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $colorClass }}">
                                {{ $movement->type->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-base {{ $movement->quantity > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-slate-500">{{ $movement->stock_before }}</span>
                            <svg class="w-3.5 h-3.5 inline-block mx-1 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            <span class="font-bold text-slate-800">{{ $movement->stock_after }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $movement->user?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-400 text-xs max-w-[200px] truncate">{{ $movement->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                            <p class="text-sm text-slate-500">Belum ada riwayat mutasi stok</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $movements->links() }}</div>
        @endif
    </div>

@endsection