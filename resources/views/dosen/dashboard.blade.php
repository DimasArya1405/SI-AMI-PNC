<x-app-layout>
    @include('dosen.sidebar')

    <div class="ml-60 min-h-screen bg-gray-100">
        <div class="p-6 max-w-7xl mx-auto space-y-6">

            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Dashboard Dosen
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi akun dosen dan auditee yang terkait dengan program studi.
                </p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Informasi Dosen
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Nama Dosen</p>
                        <p class="font-semibold text-gray-800">
                            {{ $dosen?->nama_lengkap ?? '-' }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">NIP</p>
                        <p class="font-semibold text-gray-800">
                            {{ $dosen?->nip ?? '-' }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Jabatan</p>
                        <p class="font-semibold text-gray-800">
                            {{ $dosen?->jabatan ?? '-' }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-semibold text-gray-800">
                            {{ $dosen?->email ?? '-' }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">No. Telepon</p>
                        <p class="font-semibold text-gray-800">
                            {{ $dosen?->no_telp ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Informasi Prodi dan Auditee Terkait
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Unit / Prodi Auditee</p>
                        <p class="font-semibold text-gray-800">
                            {{ $nama_unit }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Periode Aktif</p>
                        <p class="font-semibold text-gray-800">
                            {{ $periode_now?->tahun ?? '-' }}
                        </p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Nama Auditee</p>
                        <p class="font-semibold text-gray-800">
                            {{ $auditee?->nama_lengkap ?? '-' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>