<nav x-data class="fixed top-0 left-0 w-full bg-white border-b border-gray-300 z-50">
    <!-- Primary Navigation Menu -->
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @php
                    $dashboardRoute = match (Auth::user()->role) {
                    'admin' => 'admin.dashboard',
                    'kepala_p4mp' => 'kepala_p4mp.dashboard',
                    'auditor' => 'auditor.dashboard',
                    'auditee' => 'auditee.dashboard',
                    'dosen' => 'dosen.dashboard',
                    default => 'login',
                    };
                    @endphp
                    <a href="{{ route($dashboardRoute) }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route($dashboardRoute)">
                        {{ __('SI-AMI PNC') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @php
                    $notifikasiAktif = \Illuminate\Support\Facades\Schema::hasTable('notifications');
                    $notifikasiBelumDibaca = $notifikasiAktif ? Auth::user()->unreadNotifications()->count() : 0;
                    $daftarNotifikasi = $notifikasiAktif
                        ? Auth::user()->unreadNotifications()->latest()->limit(5)->get()
                        : collect();
                @endphp
                <x-dropdown align="right" width="w-80" contentClasses="bg-white">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center justify-center w-10 h-10 mr-3 text-gray-500 bg-white rounded-full hover:text-blue-600 hover:bg-blue-50 focus:outline-none transition ease-in-out duration-150">
                            <i class="bi bi-bell text-xl"></i>
                            @if ($notifikasiBelumDibaca > 0)
                                <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">
                                    {{ $notifikasiBelumDibaca > 9 ? '9+' : $notifikasiBelumDibaca }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                            <div class="font-semibold text-sm text-gray-800">Notifikasi</div>
                            @if ($notifikasiBelumDibaca > 0)
                                <form method="POST" action="{{ route('notifikasi.baca_semua') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                        Tandai semua dibaca
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto">
                            @forelse ($daftarNotifikasi as $notifikasi)
                                <a href="{{ route('notifikasi.buka', $notifikasi->id) }}"
                                    class="block px-4 py-3 border-b border-gray-100 hover:bg-blue-50 transition">
                                    <div class="flex gap-3">
                                        <div class="mt-1 text-blue-600">
                                            <i class="bi bi-info-circle-fill"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $notifikasi->data['judul'] ?? 'Notifikasi' }}
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                                {{ $notifikasi->data['pesan'] ?? '' }}
                                            </p>
                                            <p class="text-[11px] text-gray-400 mt-1">
                                                {{ $notifikasi->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-gray-500">
                                    Belum ada notifikasi baru.
                                </div>
                            @endforelse
                        </div>
                    </x-slot>
                </x-dropdown>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button type="button" @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:bg-blue-50 focus:text-blue-600 transition duration-150 ease-in-out">
                    <i class="bi bi-list text-2xl leading-none"></i>
                    <span class="sr-only">Buka menu</span>
                </button>
            </div>
        </div>
    </div>
</nav>
