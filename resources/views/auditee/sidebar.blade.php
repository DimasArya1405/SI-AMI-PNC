<aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-60 bg-white border-r border-gray overflow-y-auto">
    <div class="flex flex-col p-2 gap-1">

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.dashboard') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-grid-1x2-fill mr-2 text-xl"></i> Dashboard
        </a>

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.penugasan') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-calendar-check mr-2 text-xl"></i> Penugasan Audit
        </a>

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.dokumen*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-cloud-arrow-up mr-2 text-xl"></i> Upload Dokumen
        </a>

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.instrumen*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-ui-checks-grid mr-2 text-xl"></i> Instrumen Audit
        </a>

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.temuan*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-exclamation-octagon mr-2 text-xl"></i> Temuan Audit
        </a>

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.tindak_lanjut*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-clipboard-check mr-2 text-xl"></i> Tindak Lanjut
        </a>

        <a href="{{ route('auditee.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('auditee.laporan*') ? 'bg-blue-500 text-white' : '' }}">
            <i class="bi bi-file-earmark-text mr-2 text-xl"></i> Laporan Audit
        </a>

    </div>
</aside>