<x-app-layout>
    @include('auditee.sidebar')

    <div class="py-12 ml-64 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- HEADER --}}
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                        Dashboard Auditee
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Selamat datang kembali,
                        <span class="font-semibold">
                            {{ $auditee->nama_lengkap ?? Auth::user()->name }}
                        </span>
                    </p>
                </div>

                {{-- STATUS --}}
                <div class="flex flex-col items-end">
                    <span class="text-xs text-gray-400 mb-1">Status Audit</span>

                    @if($is_assigned)
                    <div class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                        Audit Terdaftar
                    </div>
                    @else
                    <div class="px-4 py-2 bg-gray-100 text-gray-500 rounded-full text-sm">
                        Belum Ada Audit
                    </div>
                    @endif
                </div>
            </div>

            {{-- CARD --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

                {{-- UNIT --}}
                <div class="bg-white p-6 rounded-2xl border flex items-center">
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 mr-4">
                        <i class="bi bi-building text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Unit</p>
                        <p class="text-lg font-bold">{{ $nama_unit }}</p>
                    </div>
                </div>

                {{-- PERIODE --}}
                <div class="bg-white p-6 rounded-2xl border flex items-center">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mr-4">
                        <i class="bi bi-calendar text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Periode</p>
                        <p class="text-lg font-bold">{{ $periode_now->tahun ?? '-' }}</p>
                    </div>
                </div>

                {{-- TOTAL --}}
                <div class="bg-white p-6 rounded-2xl border flex items-center">
                    <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 mr-4">
                        <i class="bi bi-journal-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Audit</p>
                        <p class="text-xl font-bold">{{ $total_penugasan }}</p>
                    </div>
                </div>

                {{-- SELESAI --}}
                <div class="bg-white p-6 rounded-2xl border flex items-center">
                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600 mr-4">
                        <i class="bi bi-check-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Selesai</p>
                        <p class="text-xl font-bold">{{ $penugasan_selesai }}</p>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- PROGRESS --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border p-8">

                    <h3 class="text-lg font-semibold mb-6">
                        Progress Audit {{ $periode_now->tahun ?? '-' }}
                    </h3>

                    @php
                    $progressWidth = match($currentStep) {
                    1 => '0%',
                    2 => '33%',
                    3 => '66%',
                    4 => '100%',
                    default => '0%',
                    };
                    @endphp

                    {{-- BAR --}}
                    <div class="relative mb-10">
                        <div class="absolute w-full h-1 bg-gray-200 top-5"></div>
                        <div class="absolute h-1 bg-indigo-500 top-5"
                            style="width: {{ $progressWidth }}"></div>

                        <div class="flex justify-between relative">

                            {{-- STEP --}}
                            @foreach([
                            1 => 'Belum',
                            2 => 'Pending',
                            3 => 'Aktif',
                            4 => 'Selesai'
                            ] as $step => $label)

                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        {{ $currentStep >= $step ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                                    {{ $step }}
                                </div>
                                <span class="text-xs mt-2 font-semibold">
                                    {{ $label }}
                                </span>
                            </div>

                            @endforeach

                        </div>
                    </div>

                    {{-- INFO --}}
                    @if($is_assigned)
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                        <p class="text-sm text-indigo-700">
                            Status saat ini:
                            <span class="font-bold underline">
                                {{ $currentStageLabel }}
                            </span>
                        </p>
                    </div>
                    @else
                    <div class="p-4 bg-gray-50 border rounded-xl text-gray-500 text-sm">
                        Belum ada penugasan audit pada periode ini.
                    </div>
                    @endif

                </div>

                {{-- DETAIL --}}
                <div class="bg-white rounded-2xl border p-6">

                    <h3 class="text-lg font-semibold mb-5">
                        Detail Audit
                    </h3>

                    <div class="space-y-4">

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Auditor</p>
                            <p class="font-semibold">{{ $nama_auditor }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Tanggal Audit</p>
                            <p class="font-semibold">{{ $tanggal_audit }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Lokasi</p>
                            <p class="font-semibold">{{ $lokasi_audit }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-xs text-gray-400">Audit Aktif</p>
                            <p class="font-semibold">{{ $penugasan_aktif }}</p>
                        </div>

                    </div>

                    <div class="mt-6">
                        <a href="#"
                            class="w-full flex justify-center bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700">
                            <i class="bi bi-calendar-check mr-2"></i>
                            Lihat Penugasan
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>