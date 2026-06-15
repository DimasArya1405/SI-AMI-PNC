<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Lupa Kata Sandi</h1>
        <p class="mt-2 text-sm text-gray-600">
            Masukkan alamat email akun Anda. Kami akan mengirimkan link untuk membuat kata sandi baru.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('login') }}" class="text-center text-sm text-gray-600 hover:text-blue-600 hover:underline">
                Kembali ke login
            </a>

            <x-primary-button class="justify-center">
                Kirim Link Reset
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
