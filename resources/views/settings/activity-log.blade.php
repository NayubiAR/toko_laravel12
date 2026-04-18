@extends('components.layouts.app')

@section('title', 'Log Aktivitas')
@section('subtitle', 'Audit trail — siapa melakukan apa, kapan')

@section('content')

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..."
                class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-52">
        </div>
        <select name="log_name" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
            <option value="">Semua Modul</option>
            @foreach($logNames as $ln)
                <option value="{{ $ln }}" {{ request('log_name') === $ln ? 'selected' : '' }}>{{ ucfirst($ln) }}</option>
            @endforeach
        </select>
        <select name="causer_id" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
            <option value="">Semua User</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('causer_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
        <button type="submit" class="px-4 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">Filter</button>
        @if(request()->hasAny(['search', 'log_name', 'causer_id', 'date_from', 'date_to']))
            <a href="{{ route('settings.activity-log') }}" class="text-sm text-slate-500 hover:text-red-500">Reset</a>
        @endif
    </form>

    {{-- Log Table --}}
    <div class="stat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Waktu</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">User</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Modul</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Aktivitas</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Objek</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    @php
                        $moduleBadge = match($log->log_name) {
                            'auth' => 'bg-violet-100 text-violet-700',
                            'sale' => 'bg-emerald-100 text-emerald-700',
                            'product' => 'bg-blue-100 text-blue-700',
                            'stock' => 'bg-amber-100 text-amber-700',
                            'user' => 'bg-red-100 text-red-700',
                            'settings' => 'bg-slate-200 text-slate-700',
                            'loyalty' => 'bg-pink-100 text-pink-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-slate-800 font-medium text-xs">{{ $log->created_at->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $log->created_at->format('H:i:s') }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-slate-700">{{ $log->causer?->name ?? 'System' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $moduleBadge }}">
                                {{ ucfirst($log->log_name ?? 'default') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $log->description }}</td>
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            @if($log->subject_type)
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4" x-data="{ showDetail: false }">
                            @if($log->properties && $log->properties->count() > 0)
                                <button @click="showDetail = !showDetail" class="text-xs text-blue-500 hover:text-blue-600 font-semibold">
                                    <span x-text="showDetail ? 'Tutup' : 'Lihat'"></span>
                                </button>
                                <div x-show="showDetail" x-cloak class="mt-2 p-3 bg-slate-50 rounded-lg text-xs text-slate-600 max-w-xs overflow-auto" style="display:none;">
                                    <pre class="whitespace-pre-wrap break-words">{{ json_encode($log->properties->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-slate-500">Belum ada log aktivitas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection