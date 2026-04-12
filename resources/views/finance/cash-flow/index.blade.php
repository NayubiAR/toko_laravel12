@extends('components.layouts.app')

@section('title', 'Arus Kas')
@section('subtitle', 'Pencatatan pemasukan dan pengeluaran')

@section('content')

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stat-card p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Pemasukan</span>
            </div>
            <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        </div>

        <div class="stat-card p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"/></svg>
                </div>
                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg">Pengeluaran</span>
            </div>
            <p class="text-xl font-bold text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        </div>

        <div class="stat-card p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl {{ $balance >= 0 ? 'bg-blue-50' : 'bg-amber-50' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $balance >= 0 ? 'text-blue-500' : 'text-amber-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold {{ $balance >= 0 ? 'text-blue-600 bg-blue-50' : 'text-amber-600 bg-amber-50' }} px-2 py-0.5 rounded-lg">Saldo</span>
            </div>
            <p class="text-xl font-bold {{ $balance >= 0 ? 'text-blue-700' : 'text-amber-700' }}">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Pemasukan − Pengeluaran</p>
        </div>
    </div>

    {{-- Filter & Add --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..."
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-48">
            </div>
            <select name="type" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <option value="">Semua Tipe</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
            </select>
            <select name="category" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <option value="">Semua Kategori</option>
                <option value="sale" {{ request('category') === 'sale' ? 'selected' : '' }}>Penjualan</option>
                <option value="other_income" {{ request('category') === 'other_income' ? 'selected' : '' }}>Pendapatan Lain</option>
                <option value="purchase" {{ request('category') === 'purchase' ? 'selected' : '' }}>Pembelian</option>
                <option value="operational" {{ request('category') === 'operational' ? 'selected' : '' }}>Operasional</option>
                <option value="salary" {{ request('category') === 'salary' ? 'selected' : '' }}>Gaji</option>
                <option value="tax" {{ request('category') === 'tax' ? 'selected' : '' }}>Pajak</option>
                <option value="other_expense" {{ request('category') === 'other_expense' ? 'selected' : '' }}>Lainnya</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from', $dateFrom) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
            <input type="date" name="date_to" value="{{ request('date_to', $dateTo) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
            <button type="submit" class="px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">Filter</button>
            @if(request()->hasAny(['search', 'type', 'category', 'date_from', 'date_to']))
                <a href="{{ route('finance.cash-flow.index') }}" class="text-sm text-slate-500 hover:text-red-500">Reset</a>
            @endif
        </form>

        <a href="{{ route('finance.cash-flow.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Catat Manual
        </a>
    </div>

    {{-- Table --}}
    <div class="stat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Tipe</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Kategori</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Deskripsi</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Jumlah</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Oleh</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cashFlows as $cf)
                    @php
                        $categoryLabels = [
                            'sale' => 'Penjualan', 'other_income' => 'Pendapatan Lain',
                            'purchase' => 'Pembelian', 'operational' => 'Operasional',
                            'salary' => 'Gaji', 'tax' => 'Pajak', 'other_expense' => 'Lainnya',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-700 font-medium">{{ \Carbon\Carbon::parse($cf->date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($cf->type === 'income' || ($cf->type instanceof \App\Enums\CashFlowType && $cf->type->value === 'income'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Masuk</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Keluar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            @php
                                $catValue = $cf->category instanceof \App\Enums\CashFlowCategory ? $cf->category->value : $cf->category;
                            @endphp
                            {{ $categoryLabels[$catValue] ?? $catValue }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 max-w-[250px] truncate">{{ $cf->description }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ ($cf->type === 'income' || ($cf->type instanceof \App\Enums\CashFlowType && $cf->type->value === 'income')) ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ ($cf->type === 'income' || ($cf->type instanceof \App\Enums\CashFlowType && $cf->type->value === 'income')) ? '+' : '-' }}Rp {{ number_format($cf->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $cf->user?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($cf->reference_type === null)
                                <form method="POST" action="{{ route('finance.cash-flow.destroy', $cf) }}" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">Otomatis</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-slate-500">Belum ada data arus kas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($cashFlows->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $cashFlows->links() }}</div>
        @endif
    </div>

@endsection