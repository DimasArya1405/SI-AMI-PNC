<x-guest-layout>
{{-- <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100"> --}}

    <div class="w-full max-w-md bg-white rounded-2xl p-8">

        <!-- Logo / Title -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-blue-600">AMI System</h1>
            <p class="text-gray-500 text-sm">Audit Mutu Internal</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700"/>
                <x-text-input 
                    id="email"
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required autofocus
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2"/>
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700"/>
                <x-text-input 
                    id="password"
                    class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                    type="password"
                    name="password"
                    required
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2"/>
            </div>

            <!-- Remember -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-gray-600">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">
                        Forgot Password?
                    </a>
                @endif
            </div>

            <!-- Button -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition duration-200 shadow-md"
            >
                Login
            </button>
        </form>

    </div>

{{-- </div> --}}
</x-guest-layout>