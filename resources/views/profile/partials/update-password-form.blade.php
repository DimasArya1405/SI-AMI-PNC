<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Ubah Kata Sandi
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Pastikan akun Anda menggunakan kata sandi yang kuat agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5 sm:space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Kata Sandi Saat Ini" />
            <div class="relative mt-1">
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full pr-12" autocomplete="current-password" />
                <button type="button"
                    class="js-toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-blue-600 focus:outline-none"
                    data-target="update_password_current_password"
                    aria-label="Tampilkan kata sandi saat ini">
                    <i class="bi bi-eye text-lg" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Kata Sandi Baru" />
            <div class="relative mt-1">
                <x-text-input id="update_password_password" name="password" type="password" class="block w-full pr-12" autocomplete="new-password" />
                <button type="button"
                    class="js-toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-blue-600 focus:outline-none"
                    data-target="update_password_password"
                    aria-label="Tampilkan kata sandi baru">
                    <i class="bi bi-eye text-lg" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Kata Sandi Baru" />
            <div class="relative mt-1">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full pr-12" autocomplete="new-password" />
                <button type="button"
                    class="js-toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-blue-600 focus:outline-none"
                    data-target="update_password_password_confirmation"
                    aria-label="Tampilkan konfirmasi kata sandi baru">
                    <i class="bi bi-eye text-lg" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
            <x-primary-button class="justify-center sm:justify-start">Simpan</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Tersimpan.</p>
            @endif
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.js-toggle-password').forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = document.getElementById(button.dataset.target);

                    if (!input) {
                        return;
                    }

                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    button.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                    button.querySelector('i').className = isHidden ? 'bi bi-eye-slash text-lg' : 'bi bi-eye text-lg';
                });
            });
        });
    </script>
</section>
