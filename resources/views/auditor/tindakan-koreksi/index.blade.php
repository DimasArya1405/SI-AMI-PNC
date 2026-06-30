<x-app-layout>
    @include('auditor.sidebar')

    <div class="ml-60 py-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">Verifikasi Tindakan Koreksi</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Pantau dan verifikasi tindakan koreksi yang diajukan auditee.
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('auditor.tindakan_koreksi.index') }}"
                    class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-800">Filter Periode</p>
                        <p class="mt-1 text-xs text-gray-500">
                            Default menampilkan periode aktif. Pilih periode lain untuk melihat data tahun sebelumnya.
                        </p>
                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end">
                            <div class="w-full sm:max-w-xs" style="max-width: 20rem;">
                                <label class="mb-1 block text-xs font-medium text-gray-600">Periode</label>
                                <select name="periode_id" class="block w-full rounded border-gray-300 text-sm">
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
                                <a href="{{ route('auditor.tindakan_koreksi.index') }}"
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

            @forelse ($penugasan as $item)
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-semibold text-gray-800">{{ $item->upt?->nama_upt ?? '-' }}</h2>
                                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                    Periode {{ $item->periode?->tahun ?? '-' }}
                                </span>
                                <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                    {{ $item->jumlah_temuan }} temuan
                                </span>
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $item->rka_ditandatangani ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item->rka_ditandatangani ? 'RKA sudah ditandatangani' : 'Menunggu tanda tangan RKA' }}
                                </span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-sm">
                                <span class="rounded bg-yellow-50 px-2 py-1 text-yellow-700">{{ $item->tk_menunggu }} menunggu verifikasi</span>
                                <span class="rounded bg-green-50 px-2 py-1 text-green-700">{{ $item->tk_selesai }} selesai</span>
                            </div>
                        </div>

                        <a href="{{ route('auditor.tindakan_koreksi.show', $item->penugasan_id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded px-4 py-2 text-sm font-medium text-white {{ $item->rka_ditandatangani ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-500 hover:bg-gray-600' }}">
                            <i class="bi bi-eye"></i>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white p-10 text-center">
                    <i class="bi bi-clipboard2-check text-4xl text-gray-300"></i>
                    <p class="mt-3 text-sm font-medium text-gray-500">Belum ada penugasan audit.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
