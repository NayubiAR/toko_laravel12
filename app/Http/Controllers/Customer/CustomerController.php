<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\Helpers\InvoiceNumberGenerator;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::withCount('sales')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->tier, function ($query, $tier) {
                $query->where('tier', $tier);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Stats
        $totalMembers = Customer::active()->count();
        $totalPoints = Customer::active()->sum('points');
        $tierCounts = Customer::active()
            ->selectRaw('tier, COUNT(*) as count')
            ->groupBy('tier')
            ->pluck('count', 'tier');

        return view('customers.index', compact('customers', 'totalMembers', 'totalPoints', 'tierCounts'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20|unique:customers,phone',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string|max:1000',
            'gender'     => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
        ]);

        $validated['code'] = InvoiceNumberGenerator::customer();
        $validated['member_since'] = now()->toDateString();
        $validated['tier'] = 'bronze';
        $validated['points'] = 0;
        $validated['total_spent'] = 0;
        $validated['is_active'] = true;

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Member baru berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'sales' => function ($q) {
                $q->with('items')->latest()->limit(20);
            },
            'pointHistories' => function ($q) {
                $q->latest()->limit(20);
            },
        ]);

        $totalTransactions = $customer->sales()->count();
        $totalSpent = $customer->total_spent;

        return view('customers.show', compact('customer', 'totalTransactions', 'totalSpent'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string|max:1000',
            'gender'     => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', "Data member \"{$customer->name}\" berhasil diperbarui.");
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->count() > 0) {
            return back()->with('error', 'Member tidak bisa dihapus karena memiliki riwayat transaksi.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "Member \"{$customer->name}\" berhasil dihapus.");
    }
}