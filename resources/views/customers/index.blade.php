@extends('components.layouts.app')

@section('title', 'Member')
@section('subtitle', 'Kelola pelanggan dan program loyalty')

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="stat-card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800">{{ $totalMembers }}</p>
                <p class="text-xs text-slate-500">Total Member</p>
            </div>
        </div>
        @php
            $tierColors = ['bronze' => 'amber', 'silver' => 'slate', 'gold' => 'yellow', 'platinum' => 'violet'];
            $tierLabels = ['bronze' => 'Bronze', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
        @endphp
        @foreach(['bronze', 'silver', 'gold'] as $tier)
        <div class="stat-card p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-{{ $tierColors[$tier] }}-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-{{ $tierColors[$tier] }}-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold text-slate-800">{{ $tierCounts[$tier] ?? 0 }}</p>
                <p class="text-xs text-slate-500">{{ $tierLabels[$tier] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter & Add --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, HP..."
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-56">
            </div>
            <select name="tier" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <option value="">Semua Tier</option>
                <option value="bronze" {{ request('tier') === 'bronze' ? 'selected' : '' }}>Bronze</option>
                <option value="silver" {{ request('tier') === 'silver' ? 'selected' : '' }}>Silver</option>
                <option value="gold" {{ request('tier') === 'gold' ? 'selected' : '' }}>Gold</option>
                <option value="platinum" {{ request('tier') === 'platinum' ? 'selected' : '' }}>Platinum</option>
            </select>
            @if(request()->hasAny(['search', 'tier', 'status']))
                <a href="{{ route('customers.index') }}" class="text-sm text-slate-500 hover:text-red-500">Reset</a>
            @endif
        </form>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Member
        </a>
    </div>

    {{-- Table --}}
    <div class="stat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Member</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Kontak</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Tier</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Poin</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Total Belanja</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Transaksi</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                    @php
                        $tierValue = $customer->tier instanceof \App\Enums\CustomerTier ? $customer->tier->value : $customer->tier;
                        $tierBadge = match($tierValue) {
                            'silver' => 'bg-slate-200 text-slate-700',
                            'gold' => 'bg-yellow-100 text-yellow-700',
                            'platinum' => 'bg-violet-100 text-violet-700',
                            default => 'bg-amber-100 text-amber-700',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $customer->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $customer->code }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-slate-700">{{ $customer->phone }}</p>
                            <p class="text-xs text-slate-400">{{ $customer->email ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $tierBadge }}">
                                {{ ucfirst($tierValue) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-amber-600">{{ number_format($customer->points) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-800">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">{{ $customer->sales_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('customers.show', $customer) }}" class="p-2 rounded-lg text-slate-400 hover:text-blue-500 hover:bg-blue-50 transition-colors" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="p-2 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            <p class="text-sm text-slate-500">Belum ada member</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $customers->links() }}</div>
        @endif
    </div>

@endsection