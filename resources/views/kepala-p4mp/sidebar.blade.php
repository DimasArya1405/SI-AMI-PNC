<div x-cloak x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = ! sidebarOpen">
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 top-16 z-30 bg-gray-900/40 md:hidden" style="display: none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="app-sidebar fixed left-0 top-16 z-40 h-[calc(100vh-4rem)] w-60 transform overflow-y-auto border-r border-gray-200 bg-white transition-transform duration-200 ease-in-out md:translate-x-0">
        <div class="flex min-h-full flex-col gap-1 p-2">
            <a href="{{ route('kepala_p4mp.dashboard') }}"
                class="rounded-md px-6 py-2 text-gray-600 transition duration-200 ease-in-out hover:bg-blue-500 hover:text-white
                {{ request()->routeIs('kepala_p4mp.dashboard') ? 'bg-blue-500 text-white' : '' }}">
                <i class="bi bi-house-door mr-2 text-xl"></i> Dashboard
            </a>
            <a href="{{ route('kepala_p4mp.penugasan.index') }}"
                class="rounded-md px-6 py-2 text-gray-600 transition duration-200 ease-in-out hover:bg-blue-500 hover:text-white
                {{ request()->routeIs('kepala_p4mp.penugasan.*') ? 'bg-blue-500 text-white' : '' }}">
                <i class="bi bi-journal-check mr-2 text-xl"></i> Penugasan
            </a>
            <a href="{{ route('kepala_p4mp.rka.index') }}"
                class="rounded-md px-6 py-2 text-gray-600 transition duration-200 ease-in-out hover:bg-blue-500 hover:text-white
                {{ request()->routeIs('kepala_p4mp.rka.*') ? 'bg-blue-500 text-white' : '' }}">
                <i class="bi bi-file-earmark-text mr-2 text-xl"></i> RKA
            </a>
            <a href="{{ route('kepala_p4mp.tindakan_koreksi.index') }}"
                class="rounded-md px-6 py-2 text-gray-600 transition duration-200 ease-in-out hover:bg-blue-500 hover:text-white
                {{ request()->routeIs('kepala_p4mp.tindakan_koreksi.*') ? 'bg-blue-500 text-white' : '' }}">
                <i class="bi bi-clipboard-check mr-2 text-xl"></i> Verifikasi TK
            </a>

            <div class="mt-auto border-t border-gray-200 pt-2 md:hidden">
                <a href="{{ route('profile.edit') }}"
                    class="block rounded-md px-6 py-2 text-gray-600 transition duration-200 ease-in-out hover:bg-blue-500 hover:text-white">
                    <i class="bi bi-person-circle mr-2 text-xl"></i> Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-md px-6 py-2 text-left text-gray-600 transition duration-200 ease-in-out hover:bg-blue-500 hover:text-white">
                        <i class="bi bi-box-arrow-right mr-2 text-xl"></i> Log Out
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
