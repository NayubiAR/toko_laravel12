<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with(['user', 'customer'])
            ->withCount('items')
            ->when($request->search, function ($query, $search) {
                $query->where('invoice_number', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->when($request->method, function ($query, $method) {
                $query->where('payment_method', $method);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Summary
        $todayTotal = Sale::paid()->today()->sum('grand_total');
        $todayCount = Sale::paid()->today()->count();

        return view('sales.index', compact('sales', 'todayTotal', 'todayCount'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'user', 'customer', 'payments']);

        return view('sales.show', compact('sale'));
    }
}