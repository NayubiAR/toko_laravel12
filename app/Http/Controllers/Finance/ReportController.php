<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan penjualan harian/bulanan.
     */
    public function sales(Request $request)
    {
        $period = $request->period ?? 'daily';
        $month = $request->month ?? now()->format('Y-m');
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        if ($period === 'monthly') {
            // Laporan bulanan — grup per bulan dalam 1 tahun
            $year = $request->year ?? now()->year;

            $salesData = Sale::paid()
                ->whereYear('created_at', $year)
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(grand_total) as total_revenue'),
                    DB::raw('SUM(discount_amount) as total_discount'),
                    DB::raw('SUM(tax_amount) as total_tax')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $totalRevenue = $salesData->sum('total_revenue');
            $totalTransactions = $salesData->sum('total_transactions');

            return view('finance.reports.sales-report', compact(
                'period', 'year', 'salesData', 'totalRevenue', 'totalTransactions'
            ));

        } else {
            // Laporan harian — grup per hari dalam range tanggal
            $salesData = Sale::paid()
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total_transactions'),
                    DB::raw('SUM(grand_total) as total_revenue'),
                    DB::raw('SUM(discount_amount) as total_discount'),
                    DB::raw('SUM(tax_amount) as total_tax')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $totalRevenue = $salesData->sum('total_revenue');
            $totalTransactions = $salesData->sum('total_transactions');

            return view('finance.reports.sales-report', compact(
                'period', 'dateFrom', 'dateTo', 'salesData', 'totalRevenue', 'totalTransactions'
            ));
        }
    }

    /**
     * Laporan laba rugi.
     */
    public function profitLoss(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        // ── Pendapatan ──
        $salesRevenue = Sale::paid()
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->sum('grand_total');

        $otherIncome = CashFlow::where('type', 'income')
            ->where('category', 'other_income')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('amount');

        $totalIncome = $salesRevenue + $otherIncome;

        // ── HPP (Harga Pokok Penjualan) ──
        $hpp = SaleItem::whereHas('sale', function ($q) use ($dateFrom, $dateTo) {
                $q->paid()->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->select(DB::raw('SUM(buy_price * quantity) as total_hpp'))
            ->value('total_hpp') ?? 0;

        // ── Laba Kotor ──
        $grossProfit = $salesRevenue - $hpp;

        // ── Biaya Operasional ──
        $expenses = CashFlow::where('type', 'expense')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $purchaseExpense = $expenses->get('purchase')?->total ?? 0;
        $operationalExpense = $expenses->get('operational')?->total ?? 0;
        $salaryExpense = $expenses->get('salary')?->total ?? 0;
        $taxExpense = $expenses->get('tax')?->total ?? 0;
        $otherExpense = $expenses->get('other_expense')?->total ?? 0;

        $totalExpense = $operationalExpense + $salaryExpense + $taxExpense + $otherExpense;

        // ── Laba Bersih ──
        $netProfit = $grossProfit + $otherIncome - $totalExpense;

        // ── Top Products ──
        $topProducts = SaleItem::whereHas('sale', function ($q) use ($dateFrom, $dateTo) {
                $q->paid()->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            })
            ->select(
                'product_name',
                'product_sku',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('SUM((unit_price - buy_price) * quantity) as total_profit')
            )
            ->groupBy('product_name', 'product_sku')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return view('finance.reports.profit-loss', compact(
            'dateFrom', 'dateTo',
            'salesRevenue', 'otherIncome', 'totalIncome',
            'hpp', 'grossProfit',
            'operationalExpense', 'salaryExpense', 'taxExpense', 'otherExpense', 'totalExpense',
            'netProfit', 'topProducts'
        ));
    }
}