@extends('components.layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Selamat datang, ' . Auth::user()->name)

@section('content')

    {{-- ═══════════════════════════════════════════
         STATS CARDS
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Penjualan Hari Ini --}}
        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">Hari Ini</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($todaySales, 0, ',', '.') }}</p>
            <p class="text-sm text-slate-500 mt-1">Penjualan hari ini</p>
        </div>

        {{-- Transaksi Hari Ini --}}
        <div class="stat-card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">Hari Ini</span>
            </div>
            <p class="text-2xl font-bold text-slate-800">{{ $todayTransactions }}</p>
            <p class="text-sm text-slate-500 mt-1">Total transaksi</p>
        </div>

        {{-- Stats hanya untuk Admin & Owner --}}
        @if(Auth::user()->hasAnyRole(['admin', 'owner']))
            {{-- Total Produk --}}
            <div class="stat-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                    </div>
                    @if($lowStockCount > 0)
                        <span class="text-xs font-semibold text-red-500 bg-red-50 px-2 py-1 rounded-lg">{{ $lowStockCount }} low stock</span>
                    @endif
                </div>
                <p class="text-2xl font-bold text-slate-800">{{ $totalProducts }}</p>
                <p class="text-sm text-slate-500 mt-1">Total produk aktif</p>
            </div>

            {{-- Total Member --}}
            <div class="stat-card p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-800">{{ $totalCustomers }}</p>
                <p class="text-sm text-slate-500 mt-1">Total member</p>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════
         CHARTS & TABLES (Admin & Owner)
    ═══════════════════════════════════════════ --}}
    @if(Auth::user()->hasAnyRole(['admin', 'owner']))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Sales Chart (7 hari terakhir) --}}
        <div class="lg:col-span-2 stat-card p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Penjualan 7 Hari Terakhir</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Total bulan ini: Rp {{ number_format($monthSales, 0, ',', '.') }}</p>
                </div>
            </div>
            <div id="salesChart" style="height: 280px;"></div>
        </div>

        {{-- Top Products --}}
        <div class="stat-card p-6">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Produk Terlaris</h3>
            <div class="space-y-3">
                @forelse($topProducts as $index => $product)
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold
                            {{ $index === 0 ? 'bg-amber-100 text-amber-700' : ($index === 1 ? 'bg-slate-100 text-slate-600' : 'bg-orange-50 text-orange-600') }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-slate-400">{{ $product->total_qty }} terjual</p>
                        </div>
                        <p class="text-sm font-semibold text-slate-800">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                        <p class="text-sm text-slate-400">Belum ada data penjualan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Low Stock Alert --}}
    @if($lowStockProducts->isNotEmpty())
    <div class="stat-card p-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">Peringatan Stok Rendah</h3>
            <span class="text-xs font-semibold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">{{ $lowStockProducts->count() }} produk</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left">
                        <th class="pb-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Produk</th>
                        <th class="pb-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Kategori</th>
                        <th class="pb-3 font-semibold text-slate-500 text-xs uppercase tracking-wider text-center">Stok</th>
                        <th class="pb-3 font-semibold text-slate-500 text-xs uppercase tracking-wider text-center">Min. Stok</th>
                        <th class="pb-3 font-semibold text-slate-500 text-xs uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($lowStockProducts as $product)
                        <tr>
                            <td class="py-3">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $product->sku }}</p>
                                </div>
                            </td>
                            <td class="py-3 text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                            <td class="py-3 text-center font-bold {{ $product->stock <= 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $product->stock }}</td>
                            <td class="py-3 text-center text-slate-500">{{ $product->min_stock }}</td>
                            <td class="py-3 text-center">
                                @if($product->stock <= 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Habis</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Menipis</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    {{-- ═══════════════════════════════════════════
         KASIR VIEW (Simple)
    ═══════════════════════════════════════════ --}}
    @if(Auth::user()->hasRole('kasir'))
    <div class="stat-card p-8 text-center">
        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Mulai Berjualan</h3>
        <p class="text-sm text-slate-500 mb-6">Klik tombol di bawah untuk membuka halaman kasir</p>
        <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
            Buka POS
        </a>
    </div>
    @endif

@endsection

@push('scripts')
@if(Auth::user()->hasAnyRole(['admin', 'owner']))
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari controller
        const salesData = @json($weeklySales ?? []);

        // Siapkan data untuk chart
        const categories = [];
        const amounts = [];
        const counts = [];

        // Generate 7 hari terakhir
        for (let i = 6; i >= 0; i--) {
            const date = new Date();
            date.setDate(date.getDate() - i);
            const dateStr = date.toISOString().split('T')[0];
            const dayName = date.toLocaleDateString('id-ID', { weekday: 'short' });

            categories.push(dayName);

            const found = salesData.find(s => s.date === dateStr);
            amounts.push(found ? parseFloat(found.total) : 0);
            counts.push(found ? found.count : 0);
        }

        const options = {
            series: [{
                name: 'Penjualan',
                data: amounts
            }],
            chart: {
                type: 'area',
                height: 280,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false },
            },
            colors: ['#3b82f6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2.5,
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: categories,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '12px',
                        fontWeight: 500,
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '12px',
                    },
                    formatter: function(val) {
                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                        if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                        return 'Rp ' + val;
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: { left: 8, right: 8 }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
        };

        const chart = new ApexCharts(document.querySelector("#salesChart"), options);
        chart.render();
    });
</script>
@endif
@endpush