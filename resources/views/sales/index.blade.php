@extends('components.layouts.app')

@section('title', 'Riwayat Penjualan')
@section('subtitle', 'Daftar semua transaksi penjualan')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="stat-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($todayTotal, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500">Penjualan hari ini</p>
            </div>
        </div>
        <div class="stat-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $todayCount }}</p>
                <p class="text-sm text-slate-500">Transaksi hari ini</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice..."
                class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-52">
        </div>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            <option value="">Semua Status</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
        </select>
        <button type="submit" class="px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">Filter</button>
        @if(request()->hasAny(['search','date_from','date_to','status','method']))
            <a href="{{ route('sales.index') }}" class="text-sm text-slate-500 hover:text-red-500">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="stat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Invoice</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Kasir</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Customer</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Items</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Total</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Metode</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-slate-800 font-mono text-xs">{{ $sale->invoice_number }}</p>
                                <p class="text-xs text-slate-400">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $sale->user?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $sale->customer?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ $sale->items_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">{{ $sale->formatted_grand_total }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-semibold text-slate-600">{{ $sale->payment_method->label() }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $sale->payment_status->value === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($sale->payment_status->value === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ $sale->payment_status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sales.show', $sale) }}" class="p-2 rounded-lg text-slate-400 hover:text-blue-500 hover:bg-blue-50 transition-colors inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                            <p class="text-sm text-slate-500">Belum ada transaksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $sales->links() }}</div>
        @endif
    </div>

@endsection