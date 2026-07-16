<x-app-layout>
    @include('kepala-p4mp.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Penugasan AMI</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Lihat jadwal penugasan auditor dan tanda tangani jadwal AMI per periode.
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        @if ($selectedPeriode)
                            <a href="{{ route('kepala_p4mp.penugasan.export', $selectedPeriode->id) }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                <i class="bi bi-file-earmark-pdf"></i>
                                Export PDF
                            </a>
                        @endif
                        @if ($selectedPeriode && $penugasanSudahDiaktifkan && !$sudahDitandatangani)
                            <form id="form-ttd-penugasan-p4mp" action="{{ route('kepala_p4mp.penugasan.tanda_tangan', $selectedPeriode->id) }}" method="POST">
                                @csrf
                                @method('patch')
                                <button type="button" id="btn-open-modal-ttd-penugasan"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 sm:w-auto">
                                    <i class="bi bi-pen"></i>
                                    Tanda Tangani
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('kepala_p4mp.penugasan.index') }}"
                    class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800">Filter Periode</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Default menampilkan periode aktif. Pilih periode lain untuk melihat data tahun sebelumnya.
                        </p>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end">
                            <div class="w-full sm:max-w-xs" style="max-width: 20rem;">
                                <label class="mb-1 block text-xs font-medium text-gray-600">Periode</label>
                                <select name="periode_id" id="periode_id" class="block w-full rounded border-gray-300 text-sm">
                                    @foreach ($periodeOptions as $periodeItem)
                                        <option value="{{ $periodeItem->id }}" @selected((string) $selectedPeriodeId === (string) $periodeItem->id)>
                                            {{ $periodeItem->tahun }}{{ $periodeAktif?->id === $periodeItem->id ? ' (Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                Terapkan
                            </button>
                            @if (request()->filled('periode_id'))
                                <a href="{{ route('kepala_p4mp.penugasan.index') }}"
                                    class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Periode Aktif
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-800 lg:max-w-xs">
                        <p class="font-semibold">Menampilkan</p>
                        <p class="mt-1">Periode {{ $selectedPeriode?->tahun ?? '-' }}</p>
                    </div>
                </form>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">
                            Jadwal AMI Periode {{ $selectedPeriode?->tahun ?? '-' }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $penugasan->count() }} penugasan ditemukan.</p>
                    </div>
                    @if ($sudahDitandatangani)
                        <span class="inline-flex items-center gap-2 rounded bg-green-100 px-3 py-2 text-sm font-semibold text-green-700">
                            <i class="bi bi-check-circle"></i>
                            Sudah ditandatangani
                        </span>
                    @elseif (!$penugasanSudahDiaktifkan)
                        <span class="inline-flex items-center gap-2 rounded bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-600">
                            <i class="bi bi-hourglass-split"></i>
                            Menunggu aktivasi admin
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 rounded bg-yellow-100 px-3 py-2 text-sm font-semibold text-yellow-700">
                            <i class="bi bi-clock"></i>
                            Belum ditandatangani
                        </span>
                    @endif
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-[54rem] w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-3 py-3">No</th>
                                <th class="px-3 py-3">Hari/Tanggal</th>
                                <th class="px-3 py-3">Waktu</th>
                                <th class="px-3 py-3">Auditi</th>
                                <th class="px-3 py-3">Ketua Auditor</th>
                                <th class="px-3 py-3">Anggota Auditor</th>
                                <!-- <th class="px-3 py-3">Status TTD</th> -->
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($penugasan as $item)
                                <tr>
                                    <td class="px-3 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-3">
                                        {{ $item->tanggal_audit?->locale('id')->translatedFormat('l, d F Y') ?? '-' }}
                                    </td>
                                    <td class="px-3 py-3">{{ $item->jam ? \Illuminate\Support\Carbon::parse($item->jam)->format('H:i') . ' WIB' : '-' }}</td>
                                    <td class="px-3 py-3">{{ $item->upt?->nama_upt ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $item->auditor1?->nama_lengkap ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $item->auditor2?->nama_lengkap ?? '-' }}</td>
                                    <!-- <td class="px-3 py-3">
                                        @if ($item->acc_kepala_p4mp === '1')
                                            <span class="rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                                Ditandatangani
                                            </span>
                                        @else
                                            <span class="rounded bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-700">
                                                Menunggu
                                            </span>
                                        @endif
                                    </td> -->
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-gray-500">
                                        Belum ada penugasan pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-ttd-penugasan-p4mp" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 px-4">
        <div class="w-full max-w-md rounded-lg bg-white shadow-lg">
            <div class="border-b px-5 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Tanda Tangan</h3>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-gray-600">
                    Jadwal penugasan AMI periode ini akan ditandatangani oleh Kepala P4MP dan barcode akan tampil pada PDF.
                </p>
            </div>
            <div class="flex justify-end gap-2 border-t px-5 py-4">
                <button type="button" id="btn-cancel-modal-ttd-penugasan"
                    class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" form="form-ttd-penugasan-p4mp"
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Ya, Tanda Tangani
                </button>
            </div>
        </div>
    </div>

    <script>
        const modalTtdPenugasan = document.getElementById('modal-ttd-penugasan-p4mp');
        const openModalTtdPenugasan = document.getElementById('btn-open-modal-ttd-penugasan');
        const cancelModalTtdPenugasan = document.getElementById('btn-cancel-modal-ttd-penugasan');

        openModalTtdPenugasan?.addEventListener('click', () => {
            modalTtdPenugasan?.classList.remove('hidden');
            modalTtdPenugasan?.classList.add('flex');
        });

        cancelModalTtdPenugasan?.addEventListener('click', () => {
            modalTtdPenugasan?.classList.add('hidden');
            modalTtdPenugasan?.classList.remove('flex');
        });
    </script>
</x-app-layout>
