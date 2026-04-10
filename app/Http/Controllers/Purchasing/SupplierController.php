<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Support\Helpers\InvoiceNumberGenerator;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('products')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->paginate(15);

        return view('purchasing.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('purchasing.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:1000',
            'city'           => 'nullable|string|max:100',
            'bank_name'      => 'nullable|string|max:100',
            'bank_account'   => 'nullable|string|max:50',
            'bank_holder'    => 'nullable|string|max:255',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $validated['code'] = InvoiceNumberGenerator::supplier();
        $validated['is_active'] = true;

        Supplier::create($validated);

        return redirect()->route('purchasing.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('purchasing.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:1000',
            'city'           => 'nullable|string|max:100',
            'bank_name'      => 'nullable|string|max:100',
            'bank_account'   => 'nullable|string|max:50',
            'bank_holder'    => 'nullable|string|max:255',
            'notes'          => 'nullable|string|max:1000',
            'is_active'      => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $supplier->update($validated);

        return redirect()->route('purchasing.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return back()->with('error', 'Supplier tidak bisa dihapus karena masih memiliki ' . $supplier->products()->count() . ' produk terkait.');
        }

        $supplier->delete();

        return redirect()->route('purchasing.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}