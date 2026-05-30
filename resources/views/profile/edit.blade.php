<x-app-layout>
    @php
    $profileSidebarView = match (Auth::user()->role) {
        'admin' => 'admin.sidebar',
        'auditor' => 'auditor.sidebar',
        'auditee' => 'auditee.sidebar',
        'dosen' => 'dosen.sidebar',
        default => null,
    };
    @endphp

    @if ($profileSidebarView)
        @include($profileSidebarView)
    @endif

    <div class="py-6 px-4 sm:px-6 lg:px-8 {{ $profileSidebarView ? 'md:ml-60' : '' }}">
        <div class="mx-auto flex w-full max-w-5xl flex-col gap-4 sm:gap-6">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">Profile</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola informasi akun dan keamanan password Anda.</p>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                <div class="w-full max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                <div class="w-full max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                <div class="w-full max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
