<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashFlowController extends Controller
{
    public function index(Request $request)
    {
        $cashFlows = CashFlow::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('date', '<=', $date);
            })
            ->latest('date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        // Summary
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        $totalIncome = CashFlow::where('type', 'income')
            ->whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $totalExpense = CashFlow::where('type', 'expense')
            ->whereBetween('date', [$dateFrom, $dateTo])->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('finance.cash-flow.index', compact(
            'cashFlows', 'totalIncome', 'totalExpense', 'balance', 'dateFrom', 'dateTo'
        ));
    }

    public function create()
    {
        return view('finance.cash-flow.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|in:income,expense',
            'category'    => 'required|in:sale,other_income,purchase,operational,salary,tax,other_expense',
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:500',
            'date'        => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();

        CashFlow::create($validated);

        $typeLabel = $validated['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()->route('finance.cash-flow.index')
            ->with('success', "{$typeLabel} berhasil dicatat: Rp " . number_format($validated['amount'], 0, ',', '.'));
    }

    public function destroy(CashFlow $cashFlow)
    {
        // Jangan hapus yang otomatis dari penjualan
        if ($cashFlow->reference_type !== null) {
            return back()->with('error', 'Arus kas otomatis dari transaksi tidak bisa dihapus.');
        }

        $cashFlow->delete();

        return back()->with('success', 'Arus kas berhasil dihapus.');
    }
}