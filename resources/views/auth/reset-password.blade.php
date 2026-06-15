<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reset Kata Sandi</h1>
        <p class="mt-2 text-sm text-gray-600">
            Buat kata sandi baru untuk akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Kata Sandi Baru" />
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-12" type="password" name="password" required autocomplete="new-password" />
                <button type="button"
                    class="js-toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-blue-600 focus:outline-none"
                    data-target="password"
                    aria-label="Tampilkan kata sandi baru">
                    <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" />
            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pr-12"
                    type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button"
                    class="js-toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-blue-600 focus:outline-none"
                    data-target="password_confirmation"
                    aria-label="Tampilkan konfirmasi kata sandi baru">
                    <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-end">
            <x-primary-button class="justify-center">
                Reset Kata Sandi
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const eyeIcon = `
                <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>`;
            const eyeSlashIcon = `
                <svg class="h-5 w-5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.48A9.68 9.68 0 0 1 12 5.25c6 0 9.75 6.75 9.75 6.75a17.8 17.8 0 0 1-2.85 3.74" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.53 6.53C3.75 8.4 2.25 12 2.25 12S6 18.75 12 18.75a9.8 9.8 0 0 0 4.08-.88" />
                </svg>`;

            document.querySelectorAll('.js-toggle-password').forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = document.getElementById(button.dataset.target);

                    if (!input) {
                        return;
                    }

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    button.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                    button.innerHTML = isHidden ? eyeSlashIcon : eyeIcon;
                });
            });
        });
    </script>
</x-guest-layout>
