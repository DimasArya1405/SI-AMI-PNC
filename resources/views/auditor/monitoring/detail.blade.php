<x-app-layout>
    @include('auditor.sidebar')

    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-6">

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800">{{ __('Data Temuan & Monitoring Audit - '. $upt->nama_upt) }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Memantau kembali indikator tidak terpenuhi dari periode sebelumnya untuk memastikan tindakan perbaikan.
                    </p>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4 px-1">Riwayat Temuan Per Periode</h3>

                @if($monitoringData->isEmpty())
                    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-sm">
                        <div class="w-16 h-16 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-check-circle text-3xl"></i>
                        </div>
                        <p class="text-gray-700 font-bold text-lg mb-1">Tidak Ada Temuan Tertunggak</p>
                        <p class="text-gray-500 text-sm max-w-md mx-auto">Luar biasa! Semua indikator standar mutu dari periode sebelumnya terpantau sudah terpenuhi atau tidak memiliki riwayat temuan.</p>
                    </div>
                @else
                    
                    {{-- 🔄 LOOPING 1: Berdasarkan Periode/Tahun --}}
                    @foreach($monitoringData as $periodeId => $daftarStandarUpt)
                        <div class="mb-6 bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                            
                            <div class="bg-gray-800 px-6 py-4 flex justify-between items-center text-white">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-calendar3-event font-bold text-lg"></i>
                                    <h4 class="font-bold tracking-wide">
                                        PERIODE: {{ $listPeriode[$periodeId]->nama_periode ?? 'Tahun Lalu (ID: '.$periodeId.')' }}
                                    </h4>
                                </div>
                                <span class="text-[10px] bg-amber-500 text-gray-900 font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                    Masa Pemantauan
                                </span>
                            </div>

                            <div class="p-6 flex flex-col gap-6">
                                {{-- 🔄 LOOPING 2: Berdasarkan Standar Mutu di Periode ini --}}
                                @foreach($daftarStandarUpt as $standarUpt)
                                    <div class="bg-gray-50/60 rounded-xl p-4 border border-gray-100">
                                        
                                        <div class="flex items-center gap-2 text-indigo-700 font-bold mb-3">
                                            <i class="bi bi-bookmark-star-fill"></i>
                                            <span class="text-sm md:text-base">{{ $standarUpt->standar_mutu->nama_standar ?? 'Standar Mutu' }}</span>
                                        </div>

                                        <div class="overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-semibold text-[11px]">
                                                    <tr>
                                                        <th class="px-4 py-3 text-left w-2/5">Indikator / Item Sub Standar</th>
                                                        <th class="px-4 py-3 text-center">Kategori Temuan</th>
                                                        <th class="px-4 py-3 text-left w-1/4">Catatan Auditor Lalu</th>
                                                        <th class="px-4 py-3 text-center">Status Sekarang</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 text-gray-600">
                                                    
                                                    {{-- 🔄 LOOPING 3: Filter Khusus Item Berstatus Rusak/Temuan (Jawaban = 0) --}}
                                                    @php $hasItems = false; @endphp
                                                    @foreach($standarUpt->subStandarUpt as $subStandar)
                                                        @foreach($subStandar->items as $item)
                                                            @if($item->jawaban_audit && $item->jawaban_audit->jawaban == 0)
                                                                @php $hasItems = true; @endphp
                                                                <tr class="hover:bg-gray-50/50 transition duration-150">
                                                                    <td class="px-4 py-3.5">
                                                                        <p class="font-bold text-gray-900 leading-snug">{{ $item->nama_item }}</p>
                                                                        <span class="text-[11px] text-gray-400 block mt-0.5">{{ $subStandar->nama_sub_standar }}</span>
                                                                    </td>
                                                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                                                        <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 rounded-md text-xs font-bold uppercase tracking-wide">
                                                                            {{ $item->jawaban_audit->kategori_temuan ?? 'KTS' }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-4 py-3.5 text-xs text-gray-500 italic leading-relaxed">
                                                                        "{{ $item->jawaban_audit->catatan ?? 'Tidak ada catatan khusus.' }}"
                                                                    </td>
                                                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                                            Belum Diperbaiki
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    @endforeach

                                                    {{-- Fallback jika dalam looping standar ternyata datanya bersih dari jawaban 0 --}}
                                                    @if(!$hasItems)
                                                        <tr>
                                                            <td colspan="4" class="px-4 py-6 text-center text-gray-400 bg-gray-50/40 italic text-xs">
                                                                <i class="bi bi-info-circle mr-1"></i> Tidak ada indikator bermasalah pada komponen standar ini.
                                                            </td>
                                                        </tr>
                                                    @endif

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endforeach

                @endif
            </div>

        </div>
    </div>
</x-app-layout>