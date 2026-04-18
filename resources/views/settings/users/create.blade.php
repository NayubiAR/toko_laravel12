@extends('components.layouts.app')

@section('title', 'Tambah User')
@section('subtitle', 'Buat akun pengguna baru')

@section('content')
<div class="max-w-xl">
    <a href="{{ route('settings.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali
    </a>

    <form method="POST" action="{{ route('settings.users.store') }}">
        @csrf
        <div class="stat-card p-6 space-y-4">
            <h3 class="font-bold text-slate-800 mb-2">Informasi User</h3>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('name') border-red-300 @enderror">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('email') border-red-300 @enderror">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role <span class="text-red-400">*</span></label>
                <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('role') border-red-300 @enderror">
                    <option value="">Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                @error('role')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-slate-400 mt-1">Owner: akses penuh. Admin: semua kecuali kelola user. Kasir: POS saja.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('password') border-red-300 @enderror">
                    @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('settings.users.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">Buat User</button>
        </div>
    </form>
</div>
@endsection