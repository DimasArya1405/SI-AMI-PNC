<x-app-layout>
    @include('auditee.sidebar')

    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('Data Penugasan Auditee') }}
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <strong>UPT Anda:</strong>
                    {{ $auditee_login->upt->nama_upt ?? '-' }}
                </p>
            </div>

            <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                <div class="dt-responsive table-responsive p-4 pt-4">
                    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                        <table class="w-full text-sm text-left rtl:text-right text-body">
                            <thead class="text-sm text-body bg-gray-200 border-b rounded-base border-default">
                                <tr>
                                    <th class="px-6 py-3 font-semibold text-center">NO</th>
                                    <th class="px-6 py-3 font-semibold text-center">TIM AUDITOR</th>
                                    <th class="px-6 py-3 font-semibold text-center">JADWAL</th>
                                    <th class="px-6 py-3 font-semibold text-center">AKSI</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($penugasanProdi as $item)
                                @php
                                $dataPengajuan = $item->pengajuan_jadwal_audit
                                ? $item->pengajuan_jadwal_audit->first()
                                : null;

                                $statusUPT = $dataPengajuan ? $dataPengajuan->upt == 1 : false;
                                $statusKetua = $dataPengajuan ? $dataPengajuan->ketua_auditor == 1 : false;
                                $statusAnggota = $dataPengajuan ? $dataPengajuan->anggota_auditor == 1 : false;

                                $done = $statusUPT && $statusKetua && $statusAnggota;

                                $isPengaju = $dataPengajuan
                                ? $dataPengajuan->id_pengaju == $auditee_login->auditee_id
                                : false;

                                $sudahKonfirmasi = $statusUPT;

                                $pengaju = 'Tidak Ada';

                                if ($dataPengajuan) {
                                if ($dataPengajuan->id_pengaju == $auditee_login->auditee_id) {
                                $pengaju = $auditee_login->nama_lengkap ?? 'Auditee';
                                } else {
                                $pengaju = $dataPengajuan->auditor->nama_lengkap
                                ?? $dataPengajuan->auditee->nama_lengkap
                                ?? 'Tidak Diketahui';
                                }
                                }
                                @endphp

                                <tr class="bg-neutral-primary border-b border-default">
                                    <td class="px-6 py-4 font-medium text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs text-green-600 font-semibold uppercase">
                                                Ketua:
                                            </span>
                                            <span class="font-bold">
                                                {{ $item->auditor1->nama_lengkap ?? '-' }}
                                            </span>

                                            <hr class="w-full border-gray-100">

                                            <span class="text-xs text-blue-600 font-semibold uppercase">
                                                Anggota:
                                            </span>
                                            <span class="font-bold">
                                                {{ $item->auditor2->nama_lengkap ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex flex-col gap-2">
                                            @if ($dataPengajuan && $done)
                                            <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                                <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                                                    Jadwal Awal
                                                </span>
                                                <div class="flex items-center gap-1.5 text-gray-500 line-through">
                                                    <i class="bi bi-calendar-event"></i>
                                                    <span class="font-medium">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_audit)->format('d M Y') }}
                                                    </span>
                                                </div>
                                                <span class="text-[11px] ml-5 text-gray-400 italic">
                                                    {{ $item->jam }} WIB
                                                </span>
                                            </div>

                                            <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                                                <span class="block text-[10px] font-bold uppercase tracking-wider text-blue-600 mb-1">
                                                    Jadwal Akhir
                                                </span>
                                                <div class="flex items-center gap-1.5 text-blue-900 font-bold">
                                                    <i class="bi bi-calendar-check"></i>
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($dataPengajuan->tanggal_audit)->format('d M Y') }}
                                                    </span>
                                                </div>
                                                <span class="text-[11px] ml-5 text-blue-700 font-medium">
                                                    {{ $dataPengajuan->jam }} WIB
                                                </span>
                                            </div>
                                            @elseif ($dataPengajuan)
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5 font-bold text-gray-900">
                                                    <i class="bi bi-calendar-event text-gray-400"></i>
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($item->tanggal_audit)->format('d M Y') }}
                                                    </span>
                                                </div>

                                                <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500 ml-5 font-medium">
                                                    <span>{{ $item->jam }} WIB</span>
                                                </div>

                                                <div class="mt-2 inline-flex items-center text-[10px] text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded border border-yellow-100 self-start">
                                                    <span class="relative flex h-2 w-2 mr-1.5">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                                    </span>
                                                    Ada Pengajuan Perubahan
                                                </div>
                                            </div>
                                            @else
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5 font-bold text-gray-800">
                                                    <i class="bi bi-calendar-event text-gray-400"></i>
                                                    <span>
                                                        {{ \Carbon\Carbon::parse($item->tanggal_audit)->format('d M Y') }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-500 ml-5 font-medium">
                                                    <span>{{ $item->jam }} WIB</span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @if ($dataPengajuan)
                                        <button
                                            type="button"
                                            data-modal-target="modal-lihat-pengajuan"
                                            data-modal-toggle="modal-lihat-pengajuan"
                                            data-idpenugasan="{{ $item->penugasan_id }}"
                                            data-tanggal="{{ $dataPengajuan->tanggal_audit }}"
                                            data-jam="{{ $dataPengajuan->jam }}"
                                            data-alasan="{{ $dataPengajuan->alasan }}"
                                            data-pengaju="{{ $pengaju }}"
                                            data-tanggalold="{{ $item->tanggal_audit }}"
                                            data-jamold="{{ $item->jam }}"
                                            data-statusupt="{{ $statusUPT ? 1 : 0 }}"
                                            data-statusketua="{{ $statusKetua ? 1 : 0 }}"
                                            data-statusanggota="{{ $statusAnggota ? 1 : 0 }}"
                                            data-done="{{ $done ? 1 : 0 }}"
                                            data-ispengaju="{{ $isPengaju ? 1 : 0 }}"
                                            data-sudahkonfirmasi="{{ $sudahKonfirmasi ? 1 : 0 }}"
                                            class="button-lihat-pengajuan inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white rounded text-xs hover:bg-yellow-700 transition mb-2">
                                            <i class="bi bi-eye mr-1"></i>
                                            Lihat Pengajuan
                                        </button>
                                        @else
                                        <button
                                            type="button"
                                            data-modal-target="modal-ajukan"
                                            data-modal-toggle="modal-ajukan"
                                            data-idpenugasan="{{ $item->penugasan_id }}"
                                            data-tanggal="{{ $item->tanggal_audit }}"
                                            data-jam="{{ $item->jam }}"
                                            class="ajukan-jadwal inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition">
                                            <i class="bi bi-calendar-check mr-1"></i>
                                            Ajukan Jadwal
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center italic">
                                        Belum ada penugasan untuk UPT Anda.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- MODAL AJUKAN JADWAL --}}
    <div id="modal-ajukan" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Form Pengajuan Ganti Jadwal
                    </h3>

                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-ajukan">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form action="{{ route('auditee.penugasan.ajukan') }}" method="POST">
                    @csrf

                    <input type="hidden" id="penugasan_id" name="penugasan_id">
                    <input type="hidden" name="auditee_id" value="{{ $auditee_login->auditee_id }}">

                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2.5 text-sm font-medium text-heading">
                                Tanggal
                            </label>
                            <input type="date" name="tanggal" id="tanggal"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required>
                        </div>

                        <div class="col-span-2 sm:col-span-1">
                            <label class="block mb-2.5 text-sm font-medium text-heading">
                                Jam
                            </label>
                            <input type="time" name="jam" id="jam"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required>
                        </div>

                        <div class="col-span-2">
                            <label class="block mb-2.5 text-sm font-medium text-heading">
                                Alasan Perubahan Jadwal
                            </label>
                            <textarea id="alasan" name="alasan" rows="4"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tuliskan alasan pengajuan perubahan jadwal..." required></textarea>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 transition duration-200 ease-in-out border border-transparent font-medium rounded-base text-sm px-4 py-2.5">
                            <i class="bi bi-send mr-2"></i>
                            Ajukan Jadwal
                        </button>

                        <button data-modal-hide="modal-ajukan" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading font-medium rounded-base text-sm px-4 py-2.5">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- MODAL LIHAT PENGAJUAN --}}
    <div id="modal-lihat-pengajuan" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full">
        <div class="relative p-4 w-full max-w-lg">
            <div class="relative bg-white border border-default rounded-base shadow-sm flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between border-b border-default p-4 md:px-6 md:py-4">
                    <h3 class="text-lg font-medium text-heading">
                        Detail Pengajuan Jadwal
                    </h3>

                    <button type="button" class="text-body hover:text-heading" data-modal-hide="modal-lihat-pengajuan">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="p-4 md:px-6 overflow-y-auto">
                    <form id="form-konfirmasi-jadwal" method="POST">
                        @csrf

                        <input type="hidden" id="penugasan_id_detail" name="penugasan_id">
                        <input type="hidden" name="auditee_id" value="{{ $auditee_login->auditee_id }}">

                        <div class="mb-6 p-3 bg-gray-50 border border-dashed border-gray-300 rounded-lg">
                            <p class="text-[10px] font-bold uppercase text-gray-500 mb-2 tracking-wider">
                                Status Konfirmasi:
                            </p>

                            <div class="flex flex-wrap gap-2">
                                <span id="badge-upt" class="flex items-center px-2 py-1 rounded text-[11px] font-medium">
                                    <i id="icon-upt" class="bi mr-1"></i>
                                    UPT / Auditee
                                </span>

                                <span id="badge-ketua" class="flex items-center px-2 py-1 rounded text-[11px] font-medium">
                                    <i id="icon-ketua" class="bi mr-1"></i>
                                    Ketua Auditor
                                </span>

                                <span id="badge-anggota" class="flex items-center px-2 py-1 rounded text-[11px] font-medium">
                                    <i id="icon-anggota" class="bi mr-1"></i>
                                    Anggota Auditor
                                </span>
                            </div>
                        </div>

                        <div class="grid gap-4 grid-cols-12">
                            <div class="col-span-12">
                                <label class="block mb-1 text-sm font-medium text-heading">
                                    Pengaju:
                                </label>
                                <input type="text" id="pengaju"
                                    class="bg-gray-50 border border-default text-gray-600 text-sm rounded-base block w-full px-3 py-2"
                                    readonly>
                            </div>

                            <div class="col-span-5">
                                <label class="block mb-1 text-[10px] font-medium text-gray-400 uppercase">
                                    Tanggal Lama
                                </label>
                                <input type="date" id="tanggal_detail_old"
                                    class="bg-gray-50 border border-default text-gray-400 text-sm rounded-base block w-full px-3 py-2"
                                    readonly>
                            </div>

                            <div class="col-span-2 flex items-center justify-center pt-5">
                                <i class="bi bi-arrow-right text-blue-600 text-xl"></i>
                            </div>

                            <div class="col-span-5">
                                <label class="block mb-1 text-[10px] font-medium text-blue-600 uppercase">
                                    Tanggal Baru
                                </label>
                                <input type="date" id="tanggal_detail"
                                    class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-base block w-full px-3 py-2 font-bold"
                                    readonly>
                            </div>

                            <div class="col-span-5">
                                <label class="block mb-1 text-[10px] font-medium text-gray-400 uppercase">
                                    Jam Lama
                                </label>
                                <input type="time" id="jam_detail_old"
                                    class="bg-gray-50 border border-default text-gray-400 text-sm rounded-base block w-full px-3 py-2"
                                    readonly>
                            </div>

                            <div class="col-span-2 flex items-center justify-center pt-5">
                                <i class="bi bi-arrow-right text-blue-600 text-xl"></i>
                            </div>

                            <div class="col-span-5">
                                <label class="block mb-1 text-[10px] font-medium text-blue-600 uppercase">
                                    Jam Baru
                                </label>
                                <input type="time" id="jam_detail"
                                    class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-base block w-full px-3 py-2 font-bold"
                                    readonly>
                            </div>

                            <div class="col-span-12 mb-4">
                                <label class="block mb-1 text-sm font-medium text-heading">
                                    Alasan Perubahan
                                </label>
                                <textarea id="alasan_detail" rows="3"
                                    class="bg-gray-50 border border-default text-sm text-gray-600 rounded-lg block w-full p-2.5 shadow-inner"
                                    readonly></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="p-4 md:px-6 border-t border-default bg-gray-50 rounded-b-base">
                    <div id="action-konfirmasi" class="flex items-center space-x-3">
                        <button type="submit" form="form-konfirmasi-jadwal"
                            formaction="{{ route('auditee.penugasan.setuju') }}"
                            class="flex-1 rounded-lg text-white bg-green-600 hover:bg-green-700 font-bold text-sm px-4 py-2.5 transition flex justify-center items-center">
                            <i class="bi bi-check-circle mr-2"></i>
                            Setuju
                        </button>

                        <button type="submit" form="form-konfirmasi-jadwal"
                            formaction="{{ route('auditee.penugasan.tolak') }}"
                            class="flex-1 rounded-lg text-white bg-red-600 hover:bg-red-700 font-bold text-sm px-4 py-2.5 transition flex justify-center items-center">
                            <i class="bi bi-x-circle mr-2"></i>
                            Tolak
                        </button>
                    </div>

                    <div id="info-konfirmasi" class="hidden w-full p-2.5 rounded-lg text-center text-xs font-medium"></div>
                </div>
            </div>
        </div>
    </div>


    @push('js')
    <script>
        $(document).on('click', '.ajukan-jadwal', function() {
            $('#penugasan_id').val($(this).data('idpenugasan'));
            $('#tanggal').val($(this).data('tanggal'));
            $('#jam').val($(this).data('jam'));
        });

        function setBadge(prefix, status) {
            const badge = $('#badge-' + prefix);
            const icon = $('#icon-' + prefix);

            badge.removeClass('bg-green-100 text-green-700 bg-gray-200 text-gray-500');
            icon.removeClass('bi-check-circle-fill bi-clock-history');

            if (status == 1) {
                badge.addClass('bg-green-100 text-green-700');
                icon.addClass('bi-check-circle-fill');
            } else {
                badge.addClass('bg-gray-200 text-gray-500');
                icon.addClass('bi-clock-history');
            }
        }

        $(document).on('click', '.button-lihat-pengajuan', function() {
            let statusUPT = $(this).data('statusupt');
            let statusKetua = $(this).data('statusketua');
            let statusAnggota = $(this).data('statusanggota');
            let done = $(this).data('done');
            let isPengaju = $(this).data('ispengaju');
            let sudahKonfirmasi = $(this).data('sudahkonfirmasi');

            $('#penugasan_id_detail').val($(this).data('idpenugasan'));
            $('#tanggal_detail').val($(this).data('tanggal'));
            $('#jam_detail').val($(this).data('jam'));
            $('#tanggal_detail_old').val($(this).data('tanggalold'));
            $('#jam_detail_old').val($(this).data('jamold'));
            $('#alasan_detail').val($(this).data('alasan'));
            $('#pengaju').val($(this).data('pengaju'));

            setBadge('upt', statusUPT);
            setBadge('ketua', statusKetua);
            setBadge('anggota', statusAnggota);

            $('#action-konfirmasi').addClass('hidden');
            $('#info-konfirmasi').addClass('hidden').removeClass(
                'bg-green-100 border border-green-200 text-green-800 bg-blue-100 border-blue-200 text-blue-800'
            );

            if (done == 1) {
                $('#info-konfirmasi')
                    .removeClass('hidden')
                    .addClass('bg-green-100 border border-green-200 text-green-800')
                    .html('<i class="bi bi-check-all mr-1"></i> Jadwal sudah disetujui oleh semua pihak');
            } else if (isPengaju == 1) {
                $('#info-konfirmasi')
                    .removeClass('hidden')
                    .addClass('bg-blue-100 border border-blue-200 text-blue-800')
                    .html('<i class="bi bi-info-circle mr-1"></i> Menunggu konfirmasi pihak terkait');
            } else if (sudahKonfirmasi == 1) {
                $('#info-konfirmasi')
                    .removeClass('hidden')
                    .addClass('bg-blue-100 border border-blue-200 text-blue-800')
                    .html('<i class="bi bi-check-all mr-1"></i> Anda telah mengonfirmasi pengajuan ini');
            } else {
                $('#action-konfirmasi').removeClass('hidden');
            }
        });
    </script>
    @endpush
</x-app-layout>