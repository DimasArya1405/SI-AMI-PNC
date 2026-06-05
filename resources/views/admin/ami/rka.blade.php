<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-medium">
                    {{ __('Data Ringkasan Kondisi Audit') }}
                </div>
            </div>

            @if(isset($no_periode) && $no_periode)
                {{-- KONDISI 1: JIKA TIDAK ADA PERIODE YANG AKTIF --}}
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg text-center font-medium shadow-xs">
                    <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                    Tidak ada periode audit yang sedang aktif saat ini.
                </div>
            @else
                {{-- TABEL PROGRAM STUDI --}}
                <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                    <div class="flex justify-between items-center py-4 mx-4 border-b border-gray-300">
                        <div class="font-semibold text-gray-800">Program Studi</div>
                    </div>
                    
                    <div class="p-4 pt-4">
                        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                            <table class="w-full text-sm text-left text-body">
                                <thead class="text-sm text-gray-700 bg-gray-100 border-b border-default">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-semibold w-16 text-center">NO</th>
                                        <th scope="col" class="px-6 py-3 w-72 font-semibold">NAMA UPT</th>
                                        <th scope="col" class="px-6 py-3 font-semibold text-center">AKSI / STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($uptProdi as $item)
                                        @php
                                            $penugasanTerkini = $item->penugasan->first();
                                        @endphp
                                        <tr class="bg-white border-b border-default hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 font-medium text-center">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->nama_upt }}</td>
                                            <td class="px-6 py-4 text-center">
                                                @if (!$penugasanTerkini)
                                                    {{-- KONDISI 2: ADA PERIODE AKTIF TAPI BELUM ADA PENUGASAN --}}
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                        <i class="bi bi-x-circle"></i> Belum ada data penugasan
                                                    </span>
                                                @elseif ($penugasanTerkini->status_penugasan == 'selesai')
                                                    {{-- KONDISI 3: AUDIT SELESAI (BUTTON TAILWIND) --}}
                                                    <a href="{{ route('admin.rka.show', $penugasanTerkini->penugasan_id) }}"
                                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition duration-150 ease-in-out">
                                                        <i class="bi bi-eye mr-1.5"></i> Lihat RKA
                                                    </a>
                                                @else
                                                    {{-- KONDISI 4: AUDIT BELUM SELESAI (TAG TAILWIND) --}}
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        <i class="bi bi-clock-history"></i> Audit Belum Selesai
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data Program Studi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TABEL UNIT / BAGIAN LAIN --}}
                <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                    <div class="flex justify-between items-center py-4 mx-4 border-b border-gray-300">
                        <div class="font-semibold text-gray-800">Unit / Bagian Lain</div>
                    </div>
                    
                    <div class="p-4 pt-4">
                        <div class="relative mb-6 overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                            <table class="w-full text-sm text-left text-body">
                                <thead class="text-sm text-gray-700 bg-gray-100 border-b border-default">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-semibold w-16 text-center">NO</th>
                                        <th scope="col" class="px-6 py-3 w-72 font-semibold">NAMA UPT</th>
                                        <th scope="col" class="px-6 py-3 font-semibold text-center">AKSI / STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($uptBagian as $item)
                                        @php
                                            $penugasanTerkini = $item->penugasan->first();
                                        @endphp
                                        <tr class="bg-white border-b border-default hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 font-medium text-center">{{ $loop->iteration }}</td>
                                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $item->nama_upt }}</td>
                                            <td class="px-6 py-4 text-center">
                                                @if (!$penugasanTerkini)
                                                    {{-- KONDISI 2: ADA PERIODE AKTIF TAPI BELUM ADA PENUGASAN --}}
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                                        <i class="bi bi-x-circle"></i> Belum ada data penugasan
                                                    </span>
                                                @elseif ($penugasanTerkini->status_penugasan == 'selesai')
                                                    {{-- KONDISI 3: AUDIT SELESAI (BUTTON TAILWIND) --}}
                                                    <a href="{{ route('admin.rka.show', $penugasanTerkini->penugasan_id) }}"
                                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md shadow-xs transition duration-150 ease-in-out">
                                                        <i class="bi bi-eye mr-1.5"></i> Lihat RKA
                                                    </a>
                                                @else
                                                    {{-- KONDISI 4: AUDIT BELUM SELESAI (TAG TAILWIND) --}}
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        <i class="bi bi-clock-history"></i> Audit Belum Selesai
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data UPT/Bagian yang tersedia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- JS --}}
    @push('js')
    @endpush
    @stack('js')
</x-app-layout>
