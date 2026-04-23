<aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-60 bg-white border-r border-gray overflow-y-auto">
    <div class="flex flex-col p-2 gap-1">
        <a href="{{ route('auditor.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-grid-1x2-fill mr-2 text-xl"></i> Dashboard
        </a>

        <a href="{{ route('auditor.penugasan') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditor.penugasan') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-calendar-check mr-2 text-xl"></i> Penugasan
        </a>

        <a href="{{ route('admin.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-pencil-square mr-2 text-xl"></i> Pelaksanaan Audit
        </a>

        <a href="{{ route('admin.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-exclamation-octagon mr-2 text-xl"></i> Hasil dan Temuan
        </a>

        <a href="{{ route('admin.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-file-earmark-medical mr-2 text-xl"></i> Laporan Audit
        </a>
    </div>
</aside>