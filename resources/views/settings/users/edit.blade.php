@extends('components.layouts.app')

@section('title', 'Edit User')
@section('subtitle', $user->name)

@section('content')
<div class="max-w-xl">
    <a href="{{ route('settings.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali
    </a>

    <form method="POST" action="{{ route('settings.users.update', $user) }}">
        @csrf @method('PUT')
        <div class="stat-card p-6 space-y-4">
            <h3 class="font-bold text-slate-800 mb-2">Informasi User</h3>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('name') border-red-300 @enderror">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('email') border-red-300 @enderror">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role <span class="text-red-400">*</span></label>
                <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>

            <hr class="border-slate-100">
            <h3 class="font-bold text-slate-800">Ubah Password</h3>
            <p class="text-xs text-slate-400 -mt-2">Kosongkan jika tidak ingin mengubah password</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 @error('password') border-red-300 @enderror">
                    @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <hr class="border-slate-100">

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-slate-300 text-blue-500 focus:ring-blue-500/30">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Akun Aktif</p>
                    <p class="text-xs text-slate-400">Jika dinonaktifkan, user tidak bisa login</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('settings.users.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
            <button type="submit" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection