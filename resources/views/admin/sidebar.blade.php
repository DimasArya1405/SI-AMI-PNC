<div x-cloak x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = ! sidebarOpen">
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 top-16 z-30 bg-gray-900/40 md:hidden" style="display: none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        class="app-sidebar fixed left-0 top-16 z-40 h-[calc(100vh-4rem)] w-60 transform bg-white border-r border-gray overflow-y-auto transition-transform duration-200 ease-in-out md:translate-x-0">
    <div class="flex min-h-full flex-col p-2 gap-1">
        <a href="{{ route('admin.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-house-door mr-2 text-xl"></i> Dashboard
        </a>
        <div class="px-6 flex justify-between items-center py-2 rounded-md cursor-pointer transition duration-200 ease-in-out
            {{ request()->routeIs('admin.akun.*') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-500 hover:text-white' }}"
            aria-controls="dp-akun" data-collapse-toggle="dp-akun">

            <div>
                <i class="bi bi-person mr-2 text-xl"></i> Kelola Akun
            </div>
            <i class="bi bi-chevron-down text-sm"></i>
        </div>
        <ul id="dp-akun"
            class="pl-6 py-2 flex flex-col gap-1 {{ request()->routeIs('admin.akun.*') ? '' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.akun.auditor') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.akun.auditor') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Auditor
                </a>
            </li>
            <li>
                <a href="{{ route('admin.akun.auditee') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.akun.auditee') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Auditee
                </a>
            </li>
            <li>
                <a href="{{ route('admin.akun.kepala_p4mp') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.akun.kepala_p4mp') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Kepala P4MP
                </a>
            </li>
            <!-- <li>
                <a href="{{ route('admin.akun.dosen') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.akun.dosen') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Dosen
                </a>
            </li> -->
        </ul>
        <div class="px-6 flex justify-between items-center py-2 rounded-md cursor-pointer transition duration-200 ease-in-out
            {{ request()->routeIs('admin.ami.*') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-500 hover:text-white' }}"
            aria-controls="dp-ami" data-collapse-toggle="data-ami">

            <div>
                <i class="bi bi-person mr-2 text-xl"></i> Data AMI
            </div>
            <i class="bi bi-chevron-down text-sm"></i>
        </div>
        <ul id="data-ami"
            class="pl-6 py-2 flex flex-col gap-1 {{ request()->routeIs('admin.ami.*') ? '' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.ami.penugasan') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.ami.penugasan') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Penugasan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ami.standar_mutu') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.ami.standar_mutu') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Standar Mutu
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ami.upt_standar_mutu') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.ami.upt_standar_mutu') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Pemetaan Standar Mutu
                </a>
            </li>
            <li>
                <a href="{{ route('admin.rka.index') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.rka.*') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> RKA
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('admin.akun.dosen') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.akun.dosen') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Dosen
                </a>
            </li> --}}
        </ul>
        <div class="px-6 flex justify-between items-center py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.data.*') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:bg-blue-500 hover:text-white' }}"
            aria-controls="dp-audit" data-collapse-toggle="dp-audit">
            <div>
                <i class="bi bi-database mr-2 text-xl"></i> Data Audit
            </div>
            <i class="bi bi-chevron-down text-sm"></i>
        </div>
        <ul id="dp-audit"
            class="pl-6 py-2 flex flex-col gap-1 {{ request()->routeIs('admin.data.*') ? '' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.data.prodi') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.data.prodi') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Data Prodi
                </a>
            </li>
            <li>
                <a href="{{ route('admin.data.upt') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.data.upt') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Data UPT
                </a>
            </li>
            <li>
                <a href="#"
                    class="pl-5 flex items-center px-2 py-1.5  text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Data Akademik
                </a>
            </li>
            <li>
                <a href="#"
                    class="pl-5 flex items-center px-2 py-1.5  text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Data Sertifikasi
                </a>
            </li>
        </ul>
        <a href="{{ route('admin.periode') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.periode') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-calendar mr-2 text-base"></i> Periode
        </a>
        <a href="{{ route('admin.rka.index') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.rka.*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-file-earmark-text mr-2 text-xl"></i> RKA
        </a>
        <a href="{{ route('admin.monitoring_tk.index') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.monitoring_tk.*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-clipboard-data mr-2 text-xl"></i> Tindakan Koreksi
        </a>
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
