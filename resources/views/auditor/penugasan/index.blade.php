<x-app-layout> @include('auditor.sidebar') <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900"> {{ __('Data Penugasan') }} </div>
            </div>
            <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                <div class="dt-responsive table-responsive p-4 pt-4">
                    <div
                        class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                        <table class="w-full text-sm text-left rtl:text-right text-body">
                            <thead class="text-sm text-body bg-gray-200 border-b rounded-base border-default">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-semibold text-center">NO</th>
                                    <th scope="col" class="px-6 py-3 w-72 font-semibold">NAMA UPT</th>
                                    <th scope="col" class="px-6 py-3 font-semibold text-center">TIM AUDITOR</th>
                                    <th scope="col" class="px-6 py-3 font-semibold text-center">JADWAL</th>
                                    <th scope="col" class="px-6 py-3 font-semibold text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penugasanProdi as $item)
                                    @php
                                        // Pindahkan logika ke sini agar variabel tersedia untuk baris table dan modal
                                        $dataPengajuan = $item->pengajuan_jadwal_audit
                                            ? $item->pengajuan_jadwal_audit->first()
                                            : null;
                                        $isPengaju = $dataPengajuan ? $dataPengajuan->id_pengaju == $auditor_id : false;
                                        $sudahSetuju = false;

                                        if ($dataPengajuan) {
                                            if (
                                                $item->auditor_id_1 == $auditor_id &&
                                                $dataPengajuan->ketua_auditor == 1
                                            ) {
                                                $sudahSetuju = true;
                                            } elseif (
                                                $item->auditor_id_2 == $auditor_id &&
                                                $dataPengajuan->anggota_auditor == 1
                                            ) {
                                                $sudahSetuju = true;
                                            }
                                        }

                                        if ($item->pengajuan_jadwal_audit->count() > 0) {
                                            $statusUPT = $item->pengajuan_jadwal_audit->first()->upt;
                                            $statusKetua = $item->pengajuan_jadwal_audit->first()->ketua_auditor;
                                            $statusAnggota = $item->pengajuan_jadwal_audit->first()->anggota_auditor;
                                            $done = 0;
                                            if ($statusUPT == 1 && $statusKetua == 1 && $statusAnggota == 1) {
                                                $done = 1;
                                            }
                                        }
                                        $pengaju = $dataPengajuan
                                            ? $dataPengajuan->auditor->nama_lengkap ??
                                                ($dataPengajuan->upt == 1 ? ($item->upt->nama_upt ?? 'UPT / Auditee') : 'Tidak Diketahui')
                                            : 'Tidak Ada';
                                    @endphp
                                    <tr class="bg-neutral-primary border-b border-default">
                                        <td class="px-6 py-4 font-medium text-center">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 font-bold">{{ $item->upt->nama_upt }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1"> <span
                                                    class="text-xs text-green-600 font-semibold uppercase">Ketua:</span>
                                                <span
                                                    class="font-bold">{{ $item->auditor1->nama_lengkap ?? '-' }}</span>
                                                <hr class="w-full border-gray-100"> <span
                                                    class="text-xs text-blue-600 font-semibold uppercase">Anggota:</span>
                                                <span
                                                    class="font-bold">{{ $item->auditor2->nama_lengkap ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex flex-col gap-2">
                                                @if ($dataPengajuan && $statusUPT && $statusKetua && $statusAnggota)
                                                    {{-- KONDISI 1: JADWAL SUDAH DISETUJUI SEMUA PIHAK --}}

                                                    {{-- Tampilan Jadwal Awal (Dicoret) --}}
                                                    <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                                        <span
                                                            class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Jadwal
                                                            Awal</span>
                                                        <div
                                                            class="flex items-center gap-1.5 text-gray-500 line-through">
                                                            <svg class="w-3.5 h-3.5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                            <span
                                                                class="font-medium">{{ \Carbon\Carbon::parse($item->tanggal_audit)->format('d M Y') }}</span>
                                                        </div>
                                                        <span
                                                            class="text-[11px] ml-5 text-gray-400 italic">{{ $item->jam }}
                                                            WIB</span>
                                                    </div>

                                                    {{-- Tampilan Jadwal Akhir (Aktif/Baru) --}}
                                                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                                                        <span
                                                            class="block text-[10px] font-bold uppercase tracking-wider text-blue-600 mb-1">Jadwal
                                                            Akhir (Revisi)</span>
                                                        <div class="flex items-center gap-1.5 text-blue-900 font-bold">
                                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                            <span>{{ \Carbon\Carbon::parse($dataPengajuan->tanggal_audit)->format('d M Y') }}</span>
                                                        </div>
                                                        <span
                                                            class="text-[11px] ml-5 text-blue-700 font-medium">{{ $dataPengajuan->jam }}
                                                            WIB</span>
                                                    </div>
                                                @elseif ($dataPengajuan)
                                                    {{-- KONDISI 2: ADA PENGAJUAN TAPI BELUM DISIDANG/DISETUJUI SEMUA --}}
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center gap-1.5 font-bold text-gray-900">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                            <span>{{ \Carbon\Carbon::parse($item->tanggal_audit)->format('d M Y') }}</span>
                                                        </div>
                                                        <div
                                                            class="flex items-center gap-1.5 mt-1 text-xs text-gray-500 ml-5 font-medium">
                                                            <span>{{ $item->jam }} WIB</span>
                                                        </div>
                                                        <div
                                                            class="mt-2 inline-flex items-center text-[10px] text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded border border-yellow-100 self-start">
                                                            <span class="relative flex h-2 w-2 mr-1.5">
                                                                <span
                                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                                                <span
                                                                    class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                                            </span>
                                                            Ada Pengajuan Perubahan
                                                        </div>
                                                    </div>
                                                @else
                                                    {{-- KONDISI 3: JADWAL NORMAL --}}
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center gap-1.5 font-bold text-gray-800">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                            <span>{{ \Carbon\Carbon::parse($item->tanggal_audit)->format('d M Y') }}</span>
                                                        </div>
                                                        <div
                                                            class="flex items-center gap-1.5 mt-1 text-xs text-gray-500 ml-5 font-medium">
                                                            <span>{{ $item->jam }} WIB</span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($dataPengajuan)
                                                <button data-modal-target={{ $item->penugasan_id }}
                                                    data-modal-toggle={{ $item->penugasan_id }}
                                                    data-idpenugasan="{{ $item->penugasan_id }}"
                                                    data-tanggal="{{ $dataPengajuan->tanggal_audit }}"
                                                    data-jam="{{ $dataPengajuan->jam }}"
                                                    data-alasan="{{ $dataPengajuan->alasan }}"
                                                    data-idpengaju="{{ $dataPengajuan->id_pengaju }}"
                                                    data-pengaju="{{ $pengaju }}"
                                                    data-tanggalold="{{ $item->tanggal_audit }}"
                                                    data-jamold="{{ $item->jam }}"
                                                    class="inline-flex button-lihat-pengajuan items-center px-3 py-1.5 bg-yellow-600 text-white rounded text-xs hover:bg-yellow-700 transition mb-2">
                                                    <i class="bi bi-eye mr-1"></i> Lihat Pengajuan </button>
                                            @else
                                                <div data-modal-target="modal-ajukan" data-modal-toggle="modal-ajukan"
                                                    data-idpenugasan="{{ $item->penugasan_id }}"
                                                    data-tanggal="{{ $item->tanggal_audit }}"
                                                    data-jam="{{ $item->jam }}"
                                                    class="inline-flex ajukan-jadwal cursor-pointer items-center px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition">
                                                    <i class="bi bi-calendar-check mr-1"></i> Ajukan Jadwal
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- MODAL LIHAT PENGAJUAN (Diletakkan di dalam loop agar status per item sesuai) --}}
                                    @if ($item->pengajuan_jadwal_audit->count() > 0)
                                        <div id="{{ $item->penugasan_id }}" tabindex="-1" aria-hidden="true"
                                            class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full">
                                            <div class="relative p-4 w-full max-w-lg">
                                                <div
                                                    class="relative bg-white border border-default rounded-base shadow-sm flex flex-col max-h-[90vh]">
                                                    <div
                                                        class="flex items-center justify-between border-b border-default p-4 md:px-6 md:py-4">
                                                        <h3 class="text-lg font-medium text-heading">Detail Pengajuan
                                                            Jadwal</h3>
                                                        <button type="button" class="text-body hover:text-heading"
                                                            data-modal-hide="{{ $item->penugasan_id }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-4 md:px-6 overflow-y-auto">
                                                        <form id="form-konfirmasi-jadwal-{{ $item->penugasan_id }}" method="post"> @csrf
                                                            <input type="hidden" id="penugasan_id_detail"
                                                                name="penugasan_id" value="{{ $item->penugasan_id }}">
                                                            <input type="hidden" id="tess" name="tes"
                                                                value="{{ $statusUPT . '-' . $statusKetua . '-' . $statusAnggota }}">
                                                            <input type="hidden" id="auditor_id_detail"
                                                                name="auditor_id_detail" value="{{ $auditor_id }}">
                                                            <div
                                                                class="mb-6 p-3 bg-gray-50 border border-dashed border-gray-300 rounded-lg">
                                                                <p
                                                                    class="text-[10px] font-bold uppercase text-gray-500 mb-2 tracking-wider">
                                                                    Status Konfirmasi :</p>
                                                                <div class="flex flex-wrap gap-2">
                                                                    <span
                                                                        class="flex items-center px-2 py-1 rounded text-[11px] font-medium {{ $statusUPT ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                                                        <i
                                                                            class="bi {{ $statusUPT ? 'bi-check-circle-fill' : 'bi-clock-history' }} mr-1"></i>
                                                                        UPT / Auditee
                                                                    </span>
                                                                    <span
                                                                        class="flex items-center px-2 py-1 rounded text-[11px] font-medium {{ $statusKetua ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                                                        <i
                                                                            class="bi {{ $statusKetua ? 'bi-check-circle-fill' : 'bi-clock-history' }} mr-1"></i>
                                                                        Ketua Auditor
                                                                    </span>
                                                                    <span
                                                                        class="flex items-center px-2 py-1 rounded text-[11px] font-medium {{ $statusAnggota ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                                                                        <i
                                                                            class="bi {{ $statusAnggota ? 'bi-check-circle-fill' : 'bi-clock-history' }} mr-1"></i>
                                                                        Anggota Auditor
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="grid gap-4 grid-cols-12">
                                                                <div class="col-span-12">
                                                                    <label
                                                                        class="block mb-1 text-sm font-medium text-heading">Pengaju
                                                                        :</label>
                                                                    @php
                                                                        $namaPengaju = 'Tidak Ada'; // Default jika dataPengajuan null
                                                                        if ($dataPengajuan) {
                                                                            if ($dataPengajuan->auditor) {
                                                                                // Jika relasi auditor ada, ambil nama lengkap
                                                                                $namaPengaju =
                                                                                    $dataPengajuan->auditor
                                                                                        ->nama_lengkap;
                                                                            } elseif ($dataPengajuan->upt == 1) {
                                                                                // Jika relasi upt ada, ambil nama upt
                                                                                $namaPengaju =
                                                                                    $item->upt->nama_upt;
                                                                            } else {
                                                                                $namaPengaju =
                                                                                    'Data Pengaju Tidak Ditemukan';
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    <input type="text" id="pengaju"
                                                                        value="{{ $namaPengaju }}"
                                                                        class="bg-gray-50 border border-default text-gray-600 text-sm rounded-base block w-full px-3 py-2"
                                                                        readonly>
                                                                </div>
                                                                <div class="col-span-5">
                                                                    <label
                                                                        class="block mb-1 text-[10px] font-medium text-gray-400 uppercase">Tanggal
                                                                        Lama</label>
                                                                    <input type="date" id="tanggal_detail_old" value="{{ \Carbon\Carbon::parse($item->tanggal_audit)->format('Y-m-d') }}"
                                                                        class="bg-gray-50 border border-default text-gray-400 text-sm rounded-base block w-full px-3 py-2"
                                                                        readonly>
                                                                </div>
                                                                <div
                                                                    class="col-span-2 flex items-center justify-center pt-5">
                                                                    <i
                                                                        class="bi bi-arrow-right text-blue-600 text-xl"></i>
                                                                </div>
                                                                <div class="col-span-5">
                                                                    <label
                                                                        class="block mb-1 text-[10px] font-medium text-blue-600 uppercase">Tanggal
                                                                        Baru</label>
                                                                    <input type="date" id="tanggal_detail" value="{{ \Carbon\Carbon::parse($dataPengajuan->tanggal_audit)->format('Y-m-d') }}"
                                                                        class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-base block w-full px-3 py-2 font-bold"
                                                                        readonly>
                                                                </div>
                                                                <div class="col-span-5">
                                                                    <label
                                                                        class="block mb-1 text-[10px] font-medium text-gray-400 uppercase">Jam
                                                                        Lama</label>
                                                                    <input type="time" id="jam_detail_old" value="{{ \Carbon\Carbon::parse($item->jam)->format('H:i') }}"
                                                                        class="bg-gray-50 border border-default text-gray-400 text-sm rounded-base block w-full px-3 py-2"
                                                                        readonly>
                                                                </div>
                                                                <div
                                                                    class="col-span-2 flex items-center justify-center pt-5">
                                                                    <i
                                                                        class="bi bi-arrow-right text-blue-600 text-xl"></i>
                                                                </div>
                                                                <div class="col-span-5">
                                                                    <label
                                                                        class="block mb-1 text-[10px] font-medium text-blue-600 uppercase">Jam
                                                                        Baru</label>
                                                                    <input type="time" id="jam_detail" value="{{ \Carbon\Carbon::parse($dataPengajuan->jam)->format('H:i') }}"
                                                                        class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-base block w-full px-3 py-2 font-bold"
                                                                        readonly>
                                                                </div>
                                                                <div class="col-span-12 mb-4">
                                                                    <label
                                                                        class="block mb-1 text-sm font-medium text-heading">Alasan
                                                                        Perubahan</label>
                                                                    <textarea id="alasan_detail" rows="3"
                                                                        class="bg-gray-50 border border-default text-sm text-gray-600 rounded-lg block w-full p-2.5 shadow-inner" readonly>{{ $dataPengajuan->alasan }}</textarea>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div
                                                        class="p-4 md:px-6 border-t border-default bg-gray-50 rounded-b-base">
                                                        <div class="flex items-center space-x-3">
                                                            @if (!$isPengaju && !$sudahSetuju)
                                                                <button type="submit" form="form-konfirmasi-jadwal-{{ $item->penugasan_id }}"
                                                                    formaction="{{ route('auditor.penugasan.setuju') }}"
                                                                    class="flex-1 rounded-lg text-white bg-green-600 hover:bg-green-700 font-bold text-sm px-4 py-2.5 transition flex justify-center items-center">
                                                                    <i class="bi bi-check-circle mr-2"></i> Setuju
                                                                </button>
                                                                <button type="submit" form="form-konfirmasi-jadwal-{{ $item->penugasan_id }}"
                                                                    formaction="{{ route('auditor.penugasan.tolak') }}"
                                                                    onclick="document.getElementById('auditor_id_detail').disabled = true;"
                                                                    class="flex-1 rounded-lg text-white bg-red-600 hover:bg-red-700 font-bold text-sm px-4 py-2.5 transition flex justify-center items-center">
                                                                    <i class="bi bi-x-circle mr-2"></i> Tolak
                                                                </button>
                                                            @else
                                                                @if ($done == 1)
                                                                    <div
                                                                        class="w-full p-2.5 bg-green-100 border border-green-200 text-green-800 rounded-lg text-center text-xs font-medium">
                                                                        <i class="bi bi-check-all mr-1"></i> Jadwal
                                                                        sudah disetujui oleh semua pihak
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="w-full p-2.5 bg-blue-100 border border-blue-200 text-blue-800 rounded-lg text-center text-xs font-medium">
                                                                        @if ($isPengaju)
                                                                            <i class="bi bi-info-circle mr-1"></i>
                                                                            Menunggu
                                                                            konfirmasi pihak terkait.
                                                                        @else
                                                                            <i class="bi bi-check-all mr-1"></i> Anda
                                                                            telah
                                                                            mengonfirmasi pengajuan ini.
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                @empty <tr>
                                        <td colspan="5" class="px-6 py-10 text-center italic">Belum ada penugasan
                                            untuk Anda pada kategori ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL AJUKAN (Di luar loop karena data diisi via JS) --}}
    <div id="modal-ajukan" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading"> Form Pengajuan Ganti Jadwal </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-ajukan">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('auditor.penugasan.ajukan') }}" method="post"> @csrf @method('get')
                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <input type="hidden" id="penugasan_id" name="penugasan_id">
                        <input type="hidden" id="auditor_id" name="auditor_id" value="{{ $auditor_id }}">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2.5 text-sm font-medium text-heading">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2.5 text-sm font-medium text-heading">Jam</label>
                            <input type="time" name="jam" id="jam"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
                        </div>
                        <div class="col-span-2">
                            <label class="block mb-2.5 text-sm font-medium text-heading">Alasan Perubahan
                                Jadwal</label>
                            <textarea id="alasan" name="alasan" rows="4"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tuliskan alasan detail mengenai pengajuan perubahan jadwal di sini..." required></textarea>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-500 hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none hover:bg-blue-700 transition duration-200 ease-in-out">
                            <svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg> Ajukan Jadwal
                        </button>
                        <button data-modal-hide="modal-ajukan" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(document).on('click', '.ajukan-jadwal', function() {
                let id_penugasan = $(this).data('idpenugasan');
                let tanggal = $(this).data('tanggal');
                let jam = $(this).data('jam');
                $('#penugasan_id').val(id_penugasan);
                $('#tanggal').val(tanggal);
                $('#jam').val(jam);
            });
            $(document).on('click', '.button-lihat-pengajuan', function() {
                let id_penugasan = $(this).data('idpenugasan');
                let tanggal = $(this).data('tanggal');
                let jam = $(this).data('jam');
                let tanggal_old = $(this).data('tanggalold');
                let jam_old = $(this).data('jamold');
                let alasan = $(this).data('alasan');
                // let pengaju = $(this).data('pengaju');

                $('#penugasan_id_detail').val(id_penugasan);
                $('#jam_detail').val(jam);
                $('#tanggal_detail').val(tanggal);
                $('#jam_detail_old').val(jam_old);
                $('#tanggal_detail_old').val(tanggal_old);
                $('#alasan_detail').val(alasan);
                // $('#pengaju').val(pengaju);
            });
        </script>
    @endpush
</x-app-layout>
