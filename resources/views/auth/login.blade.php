<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kios Adiva</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js - WAJIB dimuat di halaman login untuk toggle password --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .login-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }

        .login-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(16, 185, 129, 0.06) 0%, transparent 50%);
            animation: bgShift 20s ease-in-out infinite;
        }

        @keyframes bgShift {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-2%, -1%); }
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-login:hover::after { opacity: 1; }
        .btn-login span { position: relative; z-index: 1; }

        .btn-login:active {
            transform: scale(0.98);
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(59, 130, 246, 0.25);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
        }

        .fade-in-delay-1 { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }
        .fade-in-delay-3 { animation-delay: 0.3s; }
        .fade-in-delay-4 { animation-delay: 0.4s; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Tooltip untuk penjelasan "Ingat Saya" */
        .tooltip-container {
            position: relative;
        }

        .tooltip-text {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: calc(100% + 8px);
            left: 0;
            width: 260px;
            padding: 10px 12px;
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
            transition: all 0.2s ease;
            z-index: 10;
            pointer-events: none;
        }

        .tooltip-text::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 20px;
            border-width: 5px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
        }

        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* x-cloak: sembunyikan elemen Alpine sebelum inisialisasi */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <div class="grid-pattern"></div>

    <div class="relative z-10 w-full max-w-md">
        {{-- Logo & Brand --}}
        <div class="text-center mb-8 fade-in">
            <div class="flex justify-center mb-4">
                <div class="logo-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Kios Adiva</h1>
            <p class="text-slate-400 text-sm mt-1">Point of Sales & Inventory Management</p>
        </div>

        {{-- Login Card --}}
        <div class="login-card rounded-2xl p-8 fade-in fade-in-delay-1">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-white">Masuk ke akun Anda</h2>
                <p class="text-slate-400 text-sm mt-1">Silakan masukkan email dan password</p>
            </div>

            {{-- Error Messages --}}
            @if(session('error'))
                <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20">
                    <p class="text-red-400 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20">
                    @foreach($errors->all() as $error)
                        <p class="text-red-400 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4 fade-in fade-in-delay-2">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="input-field w-full pl-10 pr-4 py-3 rounded-xl text-white placeholder-slate-500 text-sm"
                            placeholder="nama@email.com"
                            required
                            autofocus
                        >
                    </div>
                </div>

                {{-- Password dengan Toggle Show/Hide --}}
                <div class="mb-4 fade-in fade-in-delay-3" x-data="{ showPassword: false }">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </div>

                        {{-- Input field: type berubah berdasarkan showPassword --}}
                        <input
                            x-bind:type="showPassword ? 'text' : 'password'"
                            name="password"
                            class="input-field w-full pl-10 pr-12 py-3 rounded-xl text-white placeholder-slate-500 text-sm"
                            placeholder="Masukkan password"
                            required
                        >

                        {{-- Tombol toggle show/hide --}}
                        <button
                            type="button"
                            x-on:click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors"
                        >
                            {{-- Icon mata terbuka (password tersembunyi) --}}
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{-- Icon mata tertutup (password terlihat) --}}
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me dengan Tooltip Penjelasan --}}
                <div class="flex items-center justify-between mb-6 fade-in fade-in-delay-3">
                    <label class="tooltip-container flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500/30 focus:ring-offset-0">
                        <span class="text-sm text-slate-400 flex items-center gap-1">
                            Ingat saya
                            {{-- Icon tanda tanya kecil --}}
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                            </svg>
                        </span>

                        {{-- Tooltip muncul saat hover --}}
                        <div class="tooltip-text">
                            <strong class="text-slate-200">Apa itu "Ingat Saya"?</strong><br>
                            Jika dicentang, Anda akan tetap login meskipun browser ditutup. Cocok untuk perangkat pribadi.<br><br>
                            <span class="text-amber-400">Jangan centang jika menggunakan komputer bersama/umum.</span>
                        </div>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login w-full py-3 rounded-xl text-white font-semibold text-sm fade-in fade-in-delay-4">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                        </svg>
                        Masuk
                    </span>
                </button>
            </form>
        </div>

        {{-- Demo Accounts --}}
        <div class="mt-6 login-card rounded-2xl p-5 fade-in fade-in-delay-4">
            <p class="text-xs font-medium text-slate-400 mb-3 uppercase tracking-wider">Akun Demo</p>
            <div class="space-y-2">
                <button onclick="fillDemo('owner@kiosadiva.com')" class="w-full flex items-center gap-3 p-2.5 rounded-lg hover:bg-white/5 transition-colors text-left group">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">Owner</p>
                        <p class="text-xs text-slate-500">owner@kiosadiva.com</p>
                    </div>
                </button>
                <button onclick="fillDemo('admin@kiosadiva.com')" class="w-full flex items-center gap-3 p-2.5 rounded-lg hover:bg-white/5 transition-colors text-left group">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">Admin</p>
                        <p class="text-xs text-slate-500">admin@kiosadiva.com</p>
                    </div>
                </button>
                <button onclick="fillDemo('kasir@kiosadiva.com')" class="w-full flex items-center gap-3 p-2.5 rounded-lg hover:bg-white/5 transition-colors text-left group">
                    <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors">Kasir</p>
                        <p class="text-xs text-slate-500">kasir@kiosadiva.com</p>
                    </div>
                </button>
            </div>
        </div>

        <p class="text-center text-slate-600 text-xs mt-6">&copy; {{ date('Y') }} Kios Adiva. All rights reserved.</p>
    </div>

    <script>
        function fillDemo(email) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = 'password';
            document.querySelector('input[name="email"]').focus();
        }
    </script>
</body>
</html>