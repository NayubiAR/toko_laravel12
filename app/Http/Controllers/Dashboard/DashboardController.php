<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\CashFlow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Stats yang bisa dilihat semua role ──
        $todaySales = Sale::paid()->today()->sum('grand_total');
        $todayTransactions = Sale::paid()->today()->count();

        $data = [
            'todaySales'        => $todaySales,
            'todayTransactions' => $todayTransactions,
        ];

        // ── Stats tambahan untuk Admin & Owner ──
        if ($user->hasAnyRole(['admin', 'owner'])) {
            $data['totalProducts']   = Product::active()->count();
            $data['lowStockCount']   = Product::active()->lowStock()->count();
            $data['totalCustomers']  = Customer::active()->count();
            $data['monthSales']      = Sale::paid()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('grand_total');

            // Penjualan 7 hari terakhir untuk chart
            $data['weeklySales'] = Sale::paid()
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(grand_total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Top 5 produk terlaris bulan ini
            $data['topProducts'] = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->join('products', 'products.id', '=', 'sale_items.product_id')
                ->where('sales.payment_status', 'paid')
                ->whereMonth('sales.created_at', now()->month)
                ->whereYear('sales.created_at', now()->year)
                ->select(
                    'products.name',
                    DB::raw('SUM(sale_items.quantity) as total_qty'),
                    DB::raw('SUM(sale_items.subtotal) as total_revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            // Produk stok rendah
            $data['lowStockProducts'] = Product::active()
                ->lowStock()
                ->with('category')
                ->orderBy('stock')
                ->limit(10)
                ->get();
        }

        return view('dashboard.index', $data);
    }
}