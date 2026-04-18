@extends('components.layouts.app')

@section('title', 'Kelola User')
@section('subtitle', 'Manajemen akun dan role pengguna')

@section('content')

    {{-- Filter & Add --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                    class="pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 w-56">
            </div>
            <select name="role" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('settings.users.index') }}" class="text-sm text-slate-500 hover:text-red-500">Reset</a>
            @endif
        </form>
        <a href="{{ route('settings.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah User
        </a>
    </div>

    {{-- Table --}}
    <div class="stat-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">User</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Email</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Role</th>
                        <th class="text-center px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Dibuat</th>
                        <th class="text-right px-6 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    @php
                        $roleName = $user->roles->first()?->name ?? 'user';
                        $roleBadge = match($roleName) {
                            'owner' => 'bg-amber-100 text-amber-700',
                            'admin' => 'bg-blue-100 text-blue-700',
                            'kasir' => 'bg-emerald-100 text-emerald-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-xs font-bold">{{ $user->initials }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->phone ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $roleBadge }}">
                                {{ ucfirst($roleName) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('settings.users.edit', $user) }}" class="p-2 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                </a>
                                @if($user->id !== Auth::id())
                                <form method="POST" action="{{ route('settings.users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-sm text-slate-500">Tidak ada user ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>
@endsection