<aside class="fixed left-0 top-16 min-h-screen w-60 bg-white border-r border-gray">
    <div class="flex flex-col p-2 gap-1">
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
                <a href="{{ route('admin.akun.dosen') }}"
                    class="pl-5 flex items-center px-2 py-1.5 rounded-md transition duration-200 ease-in-out
                    {{ request()->routeIs('admin.akun.dosen') ? 'font-semibold text-blue-500' : 'text-gray-600 hover:text-blue-500 hover:font-semibold' }}">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Dosen
                </a>
            </li>
        </ul>
        <div class="px-6 flex justify-between items-center py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out"
            aria-controls="dp-audit" data-collapse-toggle="dp-audit">
            <div>
                <i class="bi bi-database mr-2 text-xl"></i> Data Audit
            </div>
            <i class="bi bi-chevron-down text-sm"></i>
        </div>
        <ul id="dp-audit" class="pl-6 hidden py-2 space-y-2">
            <li>
                <a href="{{ route('admin.data.prodi') }}"
                    class="pl-5 flex items-center px-2 py-1.5  text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out">
                    <i class="bi bi-chevron-right mr-3 text-xs"></i> Data Prodi
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
    </div>
</aside>
