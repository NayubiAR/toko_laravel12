@extends('components.layouts.app')

@section('title', 'Profil Saya')
@section('subtitle', 'Kelola informasi akun Anda')

@section('content')

<div class="max-w-3xl space-y-6">

    {{-- ═══════════════════════════════════════════
         INFORMASI PROFIL
    ═══════════════════════════════════════════ --}}
    <div class="stat-card p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                <span class="text-white text-lg font-bold">{{ $user->initials }}</span>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">{{ $user->name }}</h3>
                <p class="text-sm text-slate-500">{{ $user->role_display }} &middot; Bergabung {{ $user->created_at->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors @error('email') border-red-300 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
                </div>

                {{-- Role (read-only) --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role</label>
                    <input type="text" value="{{ $user->role_display }}" disabled
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-sm text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════
         UBAH PASSWORD
    ═══════════════════════════════════════════ --}}
    <div class="stat-card p-6">
        <h3 class="font-bold text-slate-800 mb-1">Ubah Password</h3>
        <p class="text-sm text-slate-500 mb-6">Pastikan menggunakan password yang kuat dan unik</p>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4 max-w-md">
                {{-- Password Saat Ini --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Saat Ini</label>
                    <input type="password" name="current_password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors @error('current_password') border-red-300 @enderror">
                    @error('current_password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors @error('password') border-red-300 @enderror">
                    @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors">
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition-colors">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>

</div>

@endsection