<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Stats untuk semua role (cache 5 menit) ──
        $todayStats = Cache::remember('dashboard.today_stats', 300, function () {
            return [
                'sales' => Sale::paid()->today()->sum('grand_total'),
                'transactions' => Sale::paid()->today()->count(),
            ];
        });

        $data = [
            'todaySales'        => $todayStats['sales'],
            'todayTransactions' => $todayStats['transactions'],
        ];

        // ── Stats untuk Admin & Owner (cache 10 menit) ──
        if ($user->hasAnyRole(['admin', 'owner'])) {

            $adminStats = Cache::remember('dashboard.admin_stats', 600, function () {
                return [
                    'totalProducts'   => Product::active()->count(),
                    'lowStockCount'   => Product::active()->lowStock()->count(),
                    'totalCustomers'  => Customer::active()->count(),
                    'monthSales'      => Sale::paid()
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('grand_total'),
                ];
            });

            $data['totalProducts']   = $adminStats['totalProducts'];
            $data['lowStockCount']   = $adminStats['lowStockCount'];
            $data['totalCustomers']  = $adminStats['totalCustomers'];
            $data['monthSales']      = $adminStats['monthSales'];

            // Penjualan 7 hari terakhir (cache 1 jam)
            $data['weeklySales'] = Cache::remember('dashboard.weekly_sales', 3600, function () {
                return Sale::paid()
                    ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(grand_total) as total'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
            });

            // Top 5 produk (cache 1 jam)
            $data['topProducts'] = Cache::remember('dashboard.top_products', 3600, function () {
                return DB::table('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->where('sales.payment_status', 'paid')
                    ->whereMonth('sales.created_at', now()->month)
                    ->whereYear('sales.created_at', now()->year)
                    ->select(
                        'sale_items.product_name as name',
                        DB::raw('SUM(sale_items.quantity) as total_qty'),
                        DB::raw('SUM(sale_items.subtotal) as total_revenue')
                    )
                    ->groupBy('sale_items.product_name')
                    ->orderByDesc('total_qty')
                    ->limit(5)
                    ->get();
            });

            // Produk stok rendah (cache 5 menit)
            $data['lowStockProducts'] = Cache::remember('dashboard.low_stock_products', 300, function () {
                return Product::active()
                    ->lowStock()
                    ->with('category:id,name')
                    ->orderBy('stock')
                    ->limit(10)
                    ->get(['id', 'name', 'sku', 'stock', 'min_stock', 'category_id']);
            });
        }

        return view('dashboard.index', $data);
    }
}