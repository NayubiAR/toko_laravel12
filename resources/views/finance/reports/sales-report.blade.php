@extends('components.layouts.app')

@section('title', 'Laporan Penjualan')
@section('subtitle', 'Analisis penjualan harian dan bulanan')

@section('content')

    {{-- Period Selector --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.reports.sales', ['period' => 'daily']) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ ($period ?? 'daily') === 'daily' ? 'bg-blue-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Harian</a>
            <a href="{{ route('finance.reports.sales', ['period' => 'monthly', 'year' => now()->year]) }}"
                class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ ($period ?? 'daily') === 'monthly' ? 'bg-blue-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Bulanan</a>
        </div>

        @if(($period ?? 'daily') === 'daily')
            <form method="GET" class="flex items-center gap-3">
                <input type="hidden" name="period" value="daily">
                <input type="date" name="date_from" value="{{ $dateFrom ?? now()->startOfMonth()->toDateString() }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <input type="date" name="date_to" value="{{ $dateTo ?? now()->toDateString() }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <button type="submit" class="px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">Filter</button>
            </form>
        @else
            <form method="GET" class="flex items-center gap-3">
                <input type="hidden" name="period" value="monthly">
                <select name="year" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ ($year ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        @endif
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="stat-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="text-sm text-slate-500">Total Pendapatan</p>
            </div>
        </div>
        <div class="stat-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($totalTransactions) }}</p>
                <p class="text-sm text-slate-500">Total Transaksi</p>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="stat-card p-6 mb-6">
        <h3 class="text-sm font-bold text-slate-800 mb-4">Grafik Penjualan</h3>
        <div id="salesReportChart" style="height: 320px;"></div>
    </div>

    {{-- Table --}}
    <div class="stat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">{{ $period === 'monthly' ? 'Bulan' : 'Tanggal' }}</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Transaksi</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Pendapatan</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Diskon</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Pajak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    @endphp
                    @forelse($salesData as $row)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            @if($period === 'monthly')
                                {{ $monthNames[$row->month] ?? $row->month }} {{ $year ?? now()->year }}
                            @else
                                {{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}
                                <span class="text-xs text-slate-400 ml-1">{{ \Carbon\Carbon::parse($row->date)->translatedFormat('l') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ $row->total_transactions }}</span></td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">Rp {{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-red-500">{{ $row->total_discount > 0 ? '-Rp '.number_format($row->total_discount, 0, ',', '.') : '—' }}</td>
                        <td class="px-6 py-4 text-right text-slate-600">Rp {{ number_format($row->total_tax, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <p class="text-sm text-slate-500">Tidak ada data penjualan untuk periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($salesData->isNotEmpty())
                <tfoot class="bg-slate-50">
                    <tr class="font-bold">
                        <td class="px-6 py-3 text-slate-800">Total</td>
                        <td class="px-6 py-3 text-center text-slate-800">{{ number_format($totalTransactions) }}</td>
                        <td class="px-6 py-3 text-right text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-red-500">-Rp {{ number_format($salesData->sum('total_discount'), 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-right text-slate-600">Rp {{ number_format($salesData->sum('total_tax'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const data = @json($salesData);
    const period = '{{ $period ?? "daily" }}';
    const monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    const categories = data.map(d => period === 'monthly' ? monthNames[d.month] : d.date);
    const revenues = data.map(d => parseFloat(d.total_revenue));
    const transactions = data.map(d => parseInt(d.total_transactions));

    new ApexCharts(document.querySelector("#salesReportChart"), {
        series: [
            { name: 'Pendapatan', type: 'area', data: revenues },
            { name: 'Transaksi', type: 'column', data: transactions }
        ],
        chart: { height: 320, fontFamily: 'Plus Jakarta Sans, sans-serif', toolbar: { show: false } },
        colors: ['#3b82f6', '#10b981'],
        fill: { type: ['gradient', 'solid'], gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } },
        stroke: { curve: 'smooth', width: [2.5, 0] },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
        dataLabels: { enabled: false },
        xaxis: { categories, labels: { style: { colors: '#94a3b8', fontSize: '11px' } } },
        yaxis: [
            { labels: { style: { colors: '#94a3b8' }, formatter: v => { if(v>=1e6) return (v/1e6).toFixed(1)+'jt'; if(v>=1e3) return (v/1e3).toFixed(0)+'rb'; return v; } } },
            { opposite: true, labels: { style: { colors: '#94a3b8' } } }
        ],
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { y: { formatter: (v, { seriesIndex }) => seriesIndex === 0 ? 'Rp ' + v.toLocaleString('id-ID') : v + ' transaksi' } },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px', fontWeight: 600 },
    }).render();
});
</script>
@endpush