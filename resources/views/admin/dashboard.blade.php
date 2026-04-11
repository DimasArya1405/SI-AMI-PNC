<x-app-layout>
    @include('admin.sidebar')
    <div class="py-12 ml-64 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                    {{ __('Dashboard Overview') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, Admin. Berikut adalah ringkasan sistem hari
                    ini.</p>
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
                        <p class="text-sm font-medium text-gray-500">Total UPT</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $upt }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-3 bg-amber-50 rounded-lg text-amber-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Alert-Icon-Path">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Periode</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $periode }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-2xl">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Progress Audit Periode Saat Ini ( Tahun {{ $periode_now->tahun ?? '-' }} )
                        </h3>
                        <button class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition">Lihat
                            Detail</button>
                    </div>

                    @php
                        // Logika persentase bar: Step 1 (0%), Step 2 (25%), Step 3 (50%), Step 4 (75%), Step 5 (100%)
                        $progressWidth = ($currentStep - 1) * 25;
                    @endphp

                    <div class="relative mb-12">
                        <div class="absolute top-5 left-0 w-full h-1 bg-gray-100 rounded-full"></div>

                        <div class="absolute top-5 left-0 h-1 bg-indigo-500 rounded-full transition-all duration-700 ease-in-out"
                            style="width: {{ $progressWidth }}%">
                        </div>

                        <div class="relative flex justify-between">

                            <div class="flex flex-col items-center group">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full ring-4 ring-white shadow-lg transition-all 
                {{ $currentStep >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <span
                                    class="mt-3 text-xs font-bold {{ $currentStep >= 1 ? 'text-gray-800' : 'text-gray-400' }}">Pembentukan
                                    Tim</span>
                                @if ($currentStep > 1)
                                    <span class="text-[10px] text-green-500 font-medium">Selesai</span>
                                @elseif($currentStep == 1)
                                    <span class="text-[10px] text-amber-500 font-medium italic animate-pulse">Sedang
                                        Berjalan</span>
                                @endif
                            </div>

                            <div class="flex flex-col items-center group">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full ring-4 ring-white shadow-lg transition-all
                {{ $currentStep >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <span
                                    class="mt-3 text-xs font-bold {{ $currentStep >= 2 ? 'text-gray-800' : 'text-gray-400' }}">Penjadwalan</span>
                                @if ($currentStep > 2)
                                    <span class="text-[10px] text-green-500 font-medium">Selesai</span>
                                @elseif($currentStep == 2)
                                    <span class="text-[10px] text-amber-500 font-medium italic animate-pulse">Sedang
                                        Berjalan</span>
                                @endif
                            </div>

                            <div class="flex flex-col items-center group">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full ring-4 ring-white shadow-lg transition-all
                {{ $currentStep >= 3 ? ($currentStep == 3 ? 'bg-white border-2 border-indigo-500 text-indigo-600 animate-pulse' : 'bg-indigo-600 text-white') : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                        </path>
                                    </svg>
                                </div>
                                <span
                                    class="mt-3 text-xs font-bold {{ $currentStep >= 3 ? 'text-gray-800' : 'text-gray-400' }}">Pelaksanaan</span>
                                @if ($currentStep > 3)
                                    <span class="text-[10px] text-green-500 font-medium">Selesai</span>
                                @elseif($currentStep == 3)
                                    <span class="text-[10px] text-amber-500 font-medium italic">Sedang Berjalan</span>
                                @endif
                            </div>

                            <div class="flex flex-col items-center group">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full ring-4 ring-white shadow-lg transition-all
                {{ $currentStep >= 4 ? ($currentStep == 4 ? 'bg-white border-2 border-indigo-500 text-indigo-600 animate-pulse' : 'bg-indigo-600 text-white') : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                </div>
                                <span
                                    class="mt-3 text-xs font-bold {{ $currentStep >= 4 ? 'text-gray-800' : 'text-gray-400' }}">Monitoring</span>
                                @if ($currentStep > 4)
                                    <span class="text-[10px] text-green-500 font-medium">Selesai</span>
                                @elseif($currentStep == 4)
                                    <span class="text-[10px] text-amber-500 font-medium italic">Sedang Berjalan</span>
                                @endif
                            </div>

                            <div class="flex flex-col items-center group">
                                <div
                                    class="w-10 h-10 flex items-center justify-center rounded-full ring-4 ring-white shadow-lg transition-all
                {{ $currentStep == 5 ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span
                                    class="mt-3 text-xs font-bold {{ $currentStep == 5 ? 'text-gray-800' : 'text-gray-400' }}">Selesai</span>
                                @if ($currentStep == 5)
                                    <span class="text-[10px] text-green-500 font-medium">All Done!</span>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="text-gray-600 leading-relaxed border-t border-gray-50 pt-6">
                        <div class="flex items-center p-4 bg-indigo-50 rounded-xl mb-4 border border-indigo-100">
                            <span class="relative flex h-3 w-3 mr-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
                            </span>
                            <p class="text-sm text-indigo-700 font-medium">
                                Info: Saat ini sedang berlangsung tahap <span class="underline">Pelaksanaan
                                    Audit</span>
                                lapangan.
                            </p>
                        </div>
                        <p class="text-sm">Gunakan dashboard ini untuk memantau aktivitas tim auditor dan status
                            pengumpulan dokumen dari auditee secara real-time.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
