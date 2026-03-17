<x-app-layout>
    @include('admin.sidebar')

    <div class="py-12 ml-64 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                    {{ __("Dashboard Overview") }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, Admin. Berikut adalah ringkasan sistem hari ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="flex justify-center items-center w-14 h-14 bg-indigo-50 rounded-lg text-indigo-600 mr-4">
                        <i class="bi bi-people font-bold text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Auditor</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $auditor }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="flex justify-center items-center w-14 h-14 bg-green-50 rounded-lg text-green-600 mr-4">
                        <i class="bi bi-people font-bold text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Auditee</p>
                        <p class="text-2xl font-bold text-gray-900">{{$auditee}}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 bg-amber-50 rounded-lg text-amber-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Alert-Icon-Path"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Periode</p>
                        <p class="text-2xl font-bold text-gray-900">7</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Progress Audit Periode Saat Ini</h3>
                        <button class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition">Lihat Semua</button>
                    </div>
                    
                    <div class="text-gray-600 leading-relaxed border-t border-gray-50 pt-6">
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl mb-4">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                            {{ __("Status: Sistem berjalan optimal.") }}
                        </div>
                        <p class="text-sm">Anda masuk sebagai administrator. Gunakan sidebar untuk mengelola data pengguna, konten, dan pengaturan keamanan.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>