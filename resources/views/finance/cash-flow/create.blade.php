@extends('components.layouts.app')

@section('title', 'Catat Arus Kas')
@section('subtitle', 'Tambah pemasukan atau pengeluaran manual')

@section('content')
<div class="max-w-xl" x-data="{ type: '{{ old('type', 'expense') }}' }">

    <a href="{{ route('finance.cash-flow.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali
    </a>

    <form method="POST" action="{{ route('finance.cash-flow.store') }}">
        @csrf

        <div class="stat-card p-6 space-y-5">

            {{-- Tipe: Pemasukan / Pengeluaran --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-3">Tipe <span class="text-red-400">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="income" x-model="type" class="hidden peer" {{ old('type') === 'income' ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all text-center">
                            <svg class="w-6 h-6 text-emerald-500 mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                            <p class="text-sm font-bold text-slate-800">Pemasukan</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="expense" x-model="type" class="hidden peer" {{ old('type', 'expense') === 'expense' ? 'checked' : '' }}>
                        <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-center">
                            <svg class="w-6 h-6 text-red-500 mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"/></svg>
                            <p class="text-sm font-bold text-slate-800">Pengeluaran</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori <span class="text-red-400">*</span></label>
                <select name="category" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('category') border-red-300 @enderror">
                    <option value="">Pilih Kategori</option>
                    <template x-if="type === 'income'">
                        <optgroup label="Pemasukan">
                            <option value="other_income" {{ old('category') === 'other_income' ? 'selected' : '' }}>Pendapatan Lain</option>
                        </optgroup>
                    </template>
                    <template x-if="type === 'expense'">
                        <optgroup label="Pengeluaran">
                            <option value="purchase" {{ old('category') === 'purchase' ? 'selected' : '' }}>Pembelian Barang</option>
                            <option value="operational" {{ old('category') === 'operational' ? 'selected' : '' }}>Operasional (listrik, sewa, dll)</option>
                            <option value="salary" {{ old('category') === 'salary' ? 'selected' : '' }}>Gaji Karyawan</option>
                            <option value="tax" {{ old('category') === 'tax' ? 'selected' : '' }}>Pajak</option>
                            <option value="other_expense" {{ old('category') === 'other_expense' ? 'selected' : '' }}>Pengeluaran Lain</option>
                        </optgroup>
                    </template>
                </select>
                @error('category')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-slate-400 mt-1">Pemasukan dari penjualan dicatat otomatis oleh sistem POS</p>
            </div>

            {{-- Jumlah --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah <span class="text-red-400">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-semibold">Rp</span>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="1" required
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('amount') border-red-300 @enderror"
                        placeholder="0">
                </div>
                @error('amount')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal <span class="text-red-400">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('date') border-red-300 @enderror">
                @error('date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi <span class="text-red-400">*</span></label>
                <textarea name="description" rows="3" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('description') border-red-300 @enderror"
                    placeholder="Contoh: Bayar listrik bulan April">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('finance.cash-flow.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">Simpan</button>
        </div>
    </form>
</div>
@endsection