<aside class="fixed left-0 top-16 h-[calc(100vh-4rem)] w-60 bg-white border-r border-gray overflow-y-auto">
    <div class="flex flex-col p-2 gap-1">

        <a href="{{ route('dosen.dashboard') }}"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('dosen.dashboard') ? 'bg-blue-500 text-white' : '' }}">

            <i class="bi bi-grid-1x2-fill mr-2 text-xl"></i>
            Dashboard
        </a>

        <a href="#"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('dosen.bukti_dukung.*') ? 'bg-blue-500 text-white' : '' }}">

            <i class="bi bi-cloud-arrow-up mr-2 text-xl"></i>
            Upload Dokumen
        </a>

        <a href="#"
            class="px-6 py-2 text-gray-600 rounded-md cursor-pointer hover:bg-blue-500 hover:text-white transition duration-200 ease-in-out
            {{ request()->routeIs('dosen.standar_mutu*') ? 'bg-blue-500 text-white' : '' }}">

            <i class="bi bi-ui-checks-grid mr-2 text-xl"></i>
            Standar Mutu
        </a>

    </div>
</aside>