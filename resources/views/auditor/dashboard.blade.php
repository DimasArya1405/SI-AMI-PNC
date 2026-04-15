<x-app-layout>
    @include('auditor.sidebar')
    <div class="py-12 ml-64 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                        {{ __('Dashboard Auditor') }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, <span class="font-semibold"> {{ Auth::user()->name }} </span>. Berikut progres tugas Anda.</p>
                </div>
                
                <div class="flex flex-col items-end">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status Penugasan Periode Ini</span>
                    {{-- @if($is_selected) --}}
                        <div class="flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-full border border-green-200 shadow-sm">
                            <i class="bi bi-check-circle-fill mr-2"></i>
                            <span class="text-sm font-bold">Terpilih Sebagai Auditor</span>
                        </div>
                    {{-- @else
                        <div class="flex items-center px-4 py-2 bg-gray-100 text-gray-500 rounded-full border border-gray-200">
                            <i class="bi bi-dash-circle mr-2"></i>
                            <span class="text-sm font-medium">Tidak Bertugas</span>
                        </div>
                    @endif --}}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="flex justify-center items-center w-14 h-14 bg-indigo-50 rounded-lg text-indigo-600 mr-4">
                        <i class="bi bi-journal-check font-bold text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Unit Audit (UPT)</p>
                        <p class="text-2xl font-bold text-gray-900">20</p>
                        <p class="text-xs text-gray-400">Ditugaskan kepada Anda</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="flex justify-center items-center w-14 h-14 bg-amber-50 rounded-lg text-amber-600 mr-4">
                        <i class="bi bi-exclamation-triangle font-bold text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Temuan Belum Selesai</p>
                        <p class="text-2xl font-bold text-gray-900">20</p>
                        <p class="text-xs text-gray-400">Membutuhkan verifikasi</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="flex justify-center items-center w-14 h-14 bg-green-50 rounded-lg text-green-600 mr-4">
                        <i class="bi bi-file-earmark-check font-bold text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Laporan Final</p>
                        <p class="text-2xl font-bold text-gray-900">20</p>
                        <p class="text-xs text-gray-400">Telah diterbitkan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Progress Audit Periode 2024
                        </h3>
                        <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition">
                            Lihat Jadwal Audit
                        </a>
                    </div>               

                    <div class="text-gray-600 leading-relaxed border-t border-gray-50 pt-6">
                        {{-- @if($is_selected) --}}
                            <div class="flex items-center p-4 bg-indigo-50 rounded-xl mb-4 border border-indigo-100">
                                <span class="relative flex h-3 w-3 mr-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                                </span>
                                <p class="text-sm text-indigo-700 font-medium">
                                    Info: Anda sedang dalam masa <span class="underline font-bold">Pelaksanaan Audit</span>.
                                </p>
                            </div>
                            <p class="text-sm">Silahkan unggah Kertas Kerja Audit (KKA) dan verifikasi dokumen yang telah diunggah oleh Auditee tepat waktu.</p>
                        {{-- @else
                            <div class="flex items-center p-4 bg-gray-50 rounded-xl mb-4 border border-gray-200">
                                <i class="bi bi-info-circle text-gray-400 mr-3"></i>
                                <p class="text-sm text-gray-500 font-medium">
                                    Anda tidak memiliki penugasan aktif pada periode audit kali ini.
                                </p>
                            </div>
                            <p class="text-sm text-gray-400">Jika ini adalah kesalahan, silahkan hubungi Admin pusat untuk pengecekan data auditor.</p>
                        @endif --}}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>