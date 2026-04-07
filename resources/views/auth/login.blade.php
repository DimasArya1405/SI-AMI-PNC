<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
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
            <div class="relative z-20 flex flex-col justify-center px-16 text-white">
                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-3xl border border-white/20 inline-block max-w-xl">
                    <h2 class="text-5xl font-black leading-tight italic tracking-tighter">AMI SYSTEM</h2>
                    <p class="mt-6 text-xl text-blue-50 leading-relaxed font-light">
                        "Menjamin standar mutu internal kampus yang berkelanjutan dan terintegrasi secara digital."
                    </p>
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-8 bg-gray-50 lg:bg-white">
            <div class="w-full max-w-md space-y-8">
                
                <div class="lg:hidden text-center mb-10">
                    <h1 class="text-4xl font-black text-blue-600 italic tracking-tighter">AMI SYSTEM</h1>
                </div>

                <div class="text-left">
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
                        <input type="password" name="password" required
                            class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all duration-200 outline-none"
                            placeholder="Masukan Kata Sandi">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-600">Ingat sesi saya</label>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-sm shadow-blue-200 transition-all duration-300 active:scale-95">
                        MASUK KE DASHBOARD
                    </button>
                </form>

                <div class="pt-8 text-center border-t border-gray-100 text-xs text-gray-400">
                    &copy; 2026 Audit Mutu Internal System. Semua Hak Dilindungi.
                </div>
            </div>
        </div>
    </div>
</body>
</html>