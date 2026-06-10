<div x-cloak x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = ! sidebarOpen">
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 top-16 z-30 bg-gray-900/40 md:hidden" style="display: none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="app-sidebar fixed left-0 top-16 z-40 h-[calc(100vh-4rem)] w-60 transform bg-white border-r border-gray overflow-y-auto transition-transform duration-200 ease-in-out md:translate-x-0">
    <div class="flex min-h-full flex-col p-2 gap-1">

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.dashboard') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-grid-1x2-fill mr-2 text-xl"></i> Dashboard
        </a>

        <a href="{{ route('auditee.dosen') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.dosen*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-person mr-2 text-xl"></i> Kelola Akun Dosen
        </a>

        <a href="{{ route('auditee.penugasan') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.penugasan') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-calendar-check mr-2 text-xl"></i> Penugasan Audit
        </a>

        <a href="{{ route('auditee.ami') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.ami*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-ui-checks-grid mr-2 text-xl"></i> Standar Mutu
        </a>

        <a href="{{ route('auditee.rka.index') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.rka.*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-file-earmark-bar-graph mr-2 text-xl"></i> RKA
        </a>

        <a href="{{ route('auditee.tindakan_koreksi.index') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.tindakan_koreksi.*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-clipboard-check mr-2 text-xl"></i> Tindakan Koreksi
        </a>

        <!-- <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.laporan*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-file-earmark-text mr-2 text-xl"></i> Laporan Audit
        </a> -->
        <div class="mt-auto border-t border-gray-200 pt-2 md:hidden">
            <a href="{{ route('profile.edit') }}"
                class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out">
                <i class="bi bi-person-circle mr-2 text-xl"></i> Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full px-6 py-2 text-left text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out">
                    <i class="bi bi-box-arrow-right mr-2 text-xl"></i> Log Out
                </button>
            </form>
        </div>

    </div>
    </aside>
</div>
