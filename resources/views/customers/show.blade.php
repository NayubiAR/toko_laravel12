@extends('components.layouts.app')

@section('title', $customer->name)
@section('subtitle', 'Detail Member — ' . $customer->code)

@section('content')
<div class="max-w-5xl">

    <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Profile Card --}}
            <div class="stat-card p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    @php
                        $tierValue = $customer->tier instanceof \App\Enums\CustomerTier ? $customer->tier->value : $customer->tier;
                        $tierBg = match($tierValue) { 'silver' => 'from-slate-400 to-slate-500', 'gold' => 'from-yellow-400 to-amber-500', 'platinum' => 'from-violet-400 to-purple-500', default => 'from-amber-600 to-amber-700' };
                    @endphp
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $tierBg }} flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xl font-bold">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h2 class="text-xl font-bold text-slate-800">{{ $customer->name }}</h2>
                            @php
                                $tierBadge = match($tierValue) { 'silver' => 'bg-slate-200 text-slate-700', 'gold' => 'bg-yellow-100 text-yellow-700', 'platinum' => 'bg-violet-100 text-violet-700', default => 'bg-amber-100 text-amber-700' };
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $tierBadge }}">{{ ucfirst($tierValue) }}</span>
                        </div>
                        <p class="text-sm text-slate-400 font-mono">{{ $customer->code }}</p>
                        <div class="flex flex-wrap gap-4 mt-2 text-sm text-slate-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                                {{ $customer->phone }}
                            </span>
                            @if($customer->email)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                                {{ $customer->email }}
                            </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                Member sejak {{ $customer->member_since?->format('d/m/Y') ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Edit</a>
                </div>
            </div>

            {{-- Riwayat Transaksi --}}
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Riwayat Transaksi Terakhir</h3>
                @if($customer->sales->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Invoice</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-center">Items</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-right">Total</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-center">Poin</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-right">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($customer->sales as $sale)
                            <tr>
                                <td class="py-3">
                                    <a href="{{ route('sales.show', $sale) }}" class="font-mono text-xs text-blue-500 hover:text-blue-600">{{ $sale->invoice_number }}</a>
                                </td>
                                <td class="py-3 text-center text-slate-600">{{ $sale->items->count() }}</td>
                                <td class="py-3 text-right font-bold text-slate-800">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                <td class="py-3 text-center">
                                    @if($sale->points_earned > 0)
                                        <span class="text-amber-600 font-semibold">+{{ $sale->points_earned }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="py-3 text-right text-slate-500 text-xs">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-slate-400 text-center py-6">Belum ada riwayat transaksi</p>
                @endif
            </div>

            {{-- Riwayat Poin --}}
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Riwayat Poin</h3>
                @if($customer->pointHistories->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Tanggal</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Tipe</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-center">Poin</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase text-center">Saldo</th>
                                <th class="pb-3 font-semibold text-slate-500 text-xs uppercase">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($customer->pointHistories as $ph)
                            @php
                                $phType = $ph->type instanceof \App\Enums\PointHistoryType ? $ph->type->value : $ph->type;
                            @endphp
                            <tr>
                                <td class="py-3 text-slate-600 text-xs">{{ $ph->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3">
                                    @php
                                        $phBadge = match($phType) {
                                            'earned' => 'bg-emerald-100 text-emerald-700',
                                            'redeemed' => 'bg-blue-100 text-blue-700',
                                            'bonus' => 'bg-amber-100 text-amber-700',
                                            'expired' => 'bg-slate-100 text-slate-500',
                                            default => 'bg-violet-100 text-violet-700',
                                        };
                                        $phLabel = match($phType) {
                                            'earned' => 'Didapat', 'redeemed' => 'Ditukar', 'bonus' => 'Bonus',
                                            'expired' => 'Expired', 'adjusted' => 'Penyesuaian', default => $phType,
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $phBadge }}">{{ $phLabel }}</span>
                                </td>
                                <td class="py-3 text-center font-bold {{ $ph->points > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $ph->points > 0 ? '+' : '' }}{{ $ph->points }}
                                </td>
                                <td class="py-3 text-center text-slate-500">{{ $ph->balance_before }} → <span class="font-bold text-slate-800">{{ $ph->balance_after }}</span></td>
                                <td class="py-3 text-slate-400 text-xs max-w-[200px] truncate">{{ $ph->notes ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-slate-400 text-center py-6">Belum ada riwayat poin</p>
                @endif
            </div>
        </div>

        {{-- Right --}}
        <div class="space-y-6">

            {{-- Poin & Tier --}}
            <div class="stat-card p-6 text-center">
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Total Poin</p>
                <p class="text-4xl font-bold text-amber-600">{{ number_format($customer->points) }}</p>
                <p class="text-sm text-slate-500 mt-2">Total Belanja</p>
                <p class="text-lg font-bold text-slate-800">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $totalTransactions }} transaksi</p>
            </div>

            {{-- Adjust Poin --}}
            <div class="stat-card p-6" x-data="{ type: 'add' }">
                <h3 class="font-bold text-slate-800 mb-4">Sesuaikan Poin</h3>
                <form method="POST" action="{{ route('customers.adjust-points', $customer) }}">
                    @csrf
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="add" x-model="type" class="hidden peer" checked>
                            <div class="p-2 rounded-lg border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-center transition-all">
                                <p class="text-xs font-bold text-slate-700">Tambah</p>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="subtract" x-model="type" class="hidden peer">
                            <div class="p-2 rounded-lg border-2 border-slate-200 peer-checked:border-red-500 peer-checked:bg-red-50 text-center transition-all">
                                <p class="text-xs font-bold text-slate-700">Kurangi</p>
                            </div>
                        </label>
                    </div>
                    <div class="mb-3">
                        <input type="number" name="points" min="1" required placeholder="Jumlah poin"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <input type="text" name="notes" placeholder="Catatan (opsional)"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        Simpan
                    </button>
                </form>
            </div>

            {{-- Tier Progress --}}
            <div class="stat-card p-6">
                <h3 class="font-bold text-slate-800 mb-4">Tier Progress</h3>
                @php
                    $silverT = (int) \App\Models\Setting::get('silver_threshold', 1000000);
                    $goldT = (int) \App\Models\Setting::get('gold_threshold', 5000000);
                    $platinumT = (int) \App\Models\Setting::get('platinum_threshold', 15000000);
                    $spent = $customer->total_spent;
                @endphp
                <div class="space-y-3">
                    @foreach([
                        ['label' => 'Silver', 'threshold' => $silverT, 'color' => 'slate'],
                        ['label' => 'Gold', 'threshold' => $goldT, 'color' => 'yellow'],
                        ['label' => 'Platinum', 'threshold' => $platinumT, 'color' => 'violet'],
                    ] as $t)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-semibold text-slate-600">{{ $t['label'] }}</span>
                            <span class="text-slate-400">Rp {{ number_format($t['threshold'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-{{ $t['color'] }}-500 h-2 rounded-full transition-all" style="width: {{ min(100, ($spent / $t['threshold']) * 100) }}%"></div>
                        </div>
                        @if($spent < $t['threshold'])
                            <p class="text-[10px] text-slate-400 mt-0.5">Kurang Rp {{ number_format($t['threshold'] - $spent, 0, ',', '.') }}</p>
                        @else
                            <p class="text-[10px] text-emerald-600 font-semibold mt-0.5">Tercapai</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection