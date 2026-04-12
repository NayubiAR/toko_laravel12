@extends('components.layouts.app')

@section('title', 'Laporan Laba Rugi')
@section('subtitle', 'Profit & Loss Statement')

@section('content')

    {{-- Date Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="flex items-center gap-3">
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            <span class="text-slate-400 text-sm">sampai</span>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            <button type="submit" class="px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">Filter</button>
        </form>
        <div class="text-sm text-slate-500">
            Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: P&L Statement --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Pendapatan --}}
            <div class="stat-card p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    Pendapatan
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-600">Penjualan</span>
                        <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($salesRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-600">Pendapatan Lain</span>
                        <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($otherIncome, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-800">Total Pendapatan</span>
                        <span class="text-base font-bold text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- HPP --}}
            <div class="stat-card p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                    Harga Pokok Penjualan (HPP)
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-600">Total HPP (buy_price x quantity)</span>
                        <span class="text-sm font-semibold text-red-600">-Rp {{ number_format($hpp, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-800">Laba Kotor</span>
                        <span class="text-base font-bold {{ $grossProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>
                    @if($salesRevenue > 0)
                        <p class="text-xs text-slate-400">Margin kotor: {{ number_format(($grossProfit / $salesRevenue) * 100, 1) }}%</p>
                    @endif
                </div>
            </div>

            {{-- Biaya Operasional --}}
            <div class="stat-card p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    Biaya Operasional
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-600">Operasional (listrik, sewa, dll)</span>
                        <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($operationalExpense, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-600">Gaji Karyawan</span>
                        <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($salaryExpense, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-600">Pajak</span>
                        <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($taxExpense, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-slate-600">Pengeluaran Lain</span>
                        <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($otherExpense, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-800">Total Biaya Operasional</span>
                        <span class="text-base font-bold text-red-600">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Laba Bersih --}}
            <div class="stat-card p-6 {{ $netProfit >= 0 ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-red-500' }}">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Laba Bersih</h3>
                        <p class="text-xs text-slate-400 mt-1">Laba Kotor + Pendapatan Lain − Biaya Operasional</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
                        </p>
                        @if($netProfit < 0)
                            <span class="text-xs font-semibold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">RUGI</span>
                        @else
                            <span class="text-xs font-semibold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">UNTUNG</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Top Products & Summary --}}
        <div class="space-y-6">

            {{-- Quick Summary --}}
            <div class="stat-card p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4">Ringkasan</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Pendapatan</span>
                            <span>Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>HPP</span>
                            <span>Rp {{ number_format($hpp, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $totalIncome > 0 ? min(100, ($hpp / $totalIncome) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Biaya Operasional</span>
                            <span>Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $totalIncome > 0 ? min(100, ($totalExpense / $totalIncome) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-slate-500 mb-1">
                            <span>Laba Bersih</span>
                            <span>{{ $totalIncome > 0 ? number_format(($netProfit / $totalIncome) * 100, 1) : 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="{{ $netProfit >= 0 ? 'bg-blue-500' : 'bg-red-500' }} h-2 rounded-full" style="width: {{ $totalIncome > 0 ? min(100, abs($netProfit / $totalIncome) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Products --}}
            <div class="stat-card p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4">Produk Terlaris</h3>
                <div class="space-y-3">
                    @forelse($topProducts as $index => $product)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold
                                {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-100 text-slate-600' : ($index === 2 ? 'bg-orange-50 text-orange-600' : 'bg-slate-50 text-slate-500')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate">{{ $product->product_name }}</p>
                                <p class="text-xs text-slate-400">{{ $product->total_qty }} terjual</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-800">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</p>
                                <p class="text-xs text-emerald-600">Profit: Rp {{ number_format($product->total_profit, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection