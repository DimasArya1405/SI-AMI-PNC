<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIAMI') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo-pnc-1.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo-pnc-1.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo-pnc-1.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col lg:flex-row bg-white">
        
        <div class="hidden lg:flex lg:w-3/5 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-700/90 via-blue-600/70 to-transparent z-10"></div>
            <img 
                src="{{ asset('img/pnc.png') }}" 
                alt="Campus" 
                class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 hover:scale-105"
            >
            <!-- <div class="relative z-20 flex flex-col justify-center px-16 text-white">
                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-3xl border border-white/20 inline-block max-w-xl">
                    <h2 class="text-5xl font-black leading-tight italic tracking-tighter">AMI SYSTEM</h2>
                    <p class="mt-6 text-xl text-blue-50 leading-relaxed font-light">
                        "Menjamin standar mutu internal kampus yang berkelanjutan dan terintegrasi secara digital."
                    </p>
                </div>
            </div> -->
        </div>

        <div class="flex-1 flex items-center justify-center p-8 bg-gray-50 lg:bg-white">
            <div class="w-full max-w-md space-y-8">
                
                <div class="lg:hidden flex flex-col items-center text-center mb-10">
                    <img src="{{ asset('img/logo-pnc-1.png') }}" alt="Logo PNC" class="h-20 w-20 object-contain mb-3">
                    <h1 class="text-4xl font-black text-blue-600 italic tracking-tighter">SIAMI</h1>
                </div>

                <div class="text-left">
                    <div class="hidden lg:flex items-center gap-3 mb-8">
                        <img src="{{ asset('img/logo-pnc-1.png') }}" alt="Logo PNC" class="h-14 w-14 object-contain">
                        <div>
                            <h1 class="text-2xl font-black text-blue-600 italic tracking-tighter">SIAMI</h1>
                            <p class="text-sm text-gray-500">Sistem Audit Mutu Internal</p>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Login Portal</h2>
                    <p class="text-gray-500 mt-2">Silahkan masuk ke akun Anda</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all duration-200 outline-none"
                            placeholder="Masukan Alamat Email">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center">
                            <label class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">Lupa Password?</a>
                            @endif
                        </div>
                        <div class="relative mt-1">
                            <input id="password" type="password" name="password" required
                                class="block w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all duration-200 outline-none"
                                placeholder="Masukan Kata Sandi">
                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-blue-600 focus:outline-none"
                                aria-label="Tampilkan password">
                                <i class="bi bi-eye text-lg" aria-hidden="true"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-600">Ingat sesi saya</label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-sm shadow-blue-200 transition-all duration-300 active:scale-95">
                        Login
                    </button>
                </form>

                <div class="pt-8 text-center border-t border-gray-100 text-xs text-gray-400">
                    &copy; 2026 Sistem Informasi Audit Mutu Internal.
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            if (!passwordInput || !togglePassword) {
                return;
            }

            togglePassword.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                togglePassword.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
                togglePassword.querySelector('i').className = isHidden ? 'bi bi-eye-slash text-lg' : 'bi bi-eye text-lg';
            });
        });
    </script>
</body>
</html>
