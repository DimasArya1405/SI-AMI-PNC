<nav x-data class="fixed top-0 left-0 w-full bg-white border-b border-gray-300 z-50">
    <!-- Primary Navigation Menu -->
    <div class="w-full mx-auto px-3 sm:px-5 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex min-w-0 items-center">
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

                    $roleLabel = match (Auth::user()->role) {
                    'admin' => 'Admin',
                    'kepala_p4mp' => 'Kepala P4MP',
                    'auditor' => 'Auditor',
                    'auditee' => 'Auditee',
                    'dosen' => 'Dosen',
                    default => \Illuminate\Support\Str::of(Auth::user()->role)->replace('_', ' ')->title(),
                    };
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="inline-flex h-10 w-10 shrink-0 items-center justify-center">
                        <x-application-logo class="block h-10 w-10 object-contain fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <a href="{{ route($dashboardRoute) }}"
                    class="truncate text-sm font-medium text-gray-600 hover:text-blue-600 sm:text-base"
                    style="margin-left: 1rem; white-space: nowrap;">
                    SI-AMI PNC
                </a>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:ms-6">
                @php
                    $notifikasiAktif = \Illuminate\Support\Facades\Schema::hasTable('notifications');
                    $notifikasiBelumDibaca = $notifikasiAktif ? Auth::user()->unreadNotifications()->count() : 0;
                    $daftarNotifikasi = $notifikasiAktif
                        ? Auth::user()->unreadNotifications()->latest()->limit(5)->get()
                        : collect();
                    $tanggalHariIni = now()->locale('id')->translatedFormat('l, d F Y');
                    $tanggalHariIniMobile = now()->locale('id')->translatedFormat('d M Y');
                @endphp
                <div class="mr-3 hidden items-center border-r border-gray-200 pr-4 text-sm font-medium text-gray-600 lg:flex">
                    <i class="bi bi-calendar3 mr-2 text-blue-600"></i>
                    {{ $tanggalHariIni }}
                </div>
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
                            <div class="text-right leading-tight">
                                <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                <div class="mt-0.5 text-xs font-normal text-gray-400">{{ $roleLabel }}</div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex shrink-0 items-center gap-1 lg:hidden">
                <div class="inline-flex max-w-[6.25rem] items-center truncate rounded-full bg-blue-50 px-2 py-1 text-[11px] font-medium text-blue-700 sm:max-w-none sm:px-3 sm:text-xs">
                    <i class="bi bi-calendar3 mr-1 shrink-0"></i>
                    <span class="truncate sm:hidden">{{ $tanggalHariIniMobile }}</span>
                    <span class="hidden truncate sm:inline">{{ $tanggalHariIni }}</span>
                </div>

                <x-dropdown align="right" width="w-72" contentClasses="bg-white">
                    <x-slot name="trigger">
                        <button class="relative inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-500 transition duration-150 ease-in-out hover:bg-blue-50 hover:text-blue-600 focus:outline-none">
                            <i class="bi bi-bell text-xl"></i>
                            @if ($notifikasiBelumDibaca > 0)
                                <span class="absolute right-0 top-0 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-xs text-white">
                                    {{ $notifikasiBelumDibaca > 9 ? '9+' : $notifikasiBelumDibaca }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <div class="text-sm font-semibold text-gray-800">Notifikasi</div>
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
                                    class="block border-b border-gray-100 px-4 py-3 transition hover:bg-blue-50">
                                    <div class="flex gap-3">
                                        <div class="mt-1 text-blue-600">
                                            <i class="bi bi-info-circle-fill"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800">
                                                {{ $notifikasi->data['judul'] ?? 'Notifikasi' }}
                                            </p>
                                            <p class="mt-1 text-xs leading-relaxed text-gray-600">
                                                {{ $notifikasi->data['pesan'] ?? '' }}
                                            </p>
                                            <p class="mt-1 text-[11px] text-gray-400">
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

                <div class="hidden md:block lg:hidden">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center rounded-md border border-transparent bg-white px-2 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none sm:px-3">
                                <div class="text-right leading-tight">
                                    <div class="max-w-[8rem] truncate text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                    <div class="mt-0.5 text-xs font-normal text-gray-400">{{ $roleLabel }}</div>
                                </div>

                                <div class="ms-1">
                                    <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Profil
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    Keluar
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <button type="button" @click="window.dispatchEvent(new CustomEvent('toggle-sidebar'))" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:bg-blue-50 focus:text-blue-600 transition duration-150 ease-in-out md:hidden">
                    <i class="bi bi-list text-2xl leading-none"></i>
                    <span class="sr-only">Buka menu</span>
                </button>
            </div>
        </div>
    </div>
</nav>
