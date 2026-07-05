<x-app-layout>
    @php
        $isKepalaP4mp = Auth::user()->role === 'kepala_p4mp';
        $showRouteName = $isKepalaP4mp ? 'kepala_p4mp.rka.show' : 'admin.rka.show';
        $exportRouteName = $isKepalaP4mp ? 'kepala_p4mp.rka.export' : 'admin.rka.export';
    @endphp

    @include($isKepalaP4mp ? 'kepala-p4mp.sidebar' : 'admin.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">Ringkasan Kondisi Audit</h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $isKepalaP4mp ? 'Akses review RKA seluruh penugasan AMI.' : 'Monitoring RKA seluruh penugasan AMI.' }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route($isKepalaP4mp ? 'kepala_p4mp.rka.index' : 'admin.rka.index') }}"
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
                                <a href="{{ route($isKepalaP4mp ? 'kepala_p4mp.rka.index' : 'admin.rka.index') }}"
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

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Total Penugasan</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $ringkasan['total_penugasan'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">RKA Final</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ $ringkasan['rka_final'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">RKA Draft</p>
                    <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $ringkasan['rka_draft'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Belum Dibuat</p>
                    <p class="mt-1 text-2xl font-bold text-gray-600">{{ $ringkasan['belum_rka'] }}</p>
                </div>
            </div>

            @forelse ($penugasan as $item)
                @php
                    $statusClass = match ($item->status_rka) {
                        'final' => 'bg-green-100 text-green-700',
                        'draft' => 'bg-yellow-100 text-yellow-700',
                        default => 'bg-gray-100 text-gray-600',
                    };
                    $statusLabel = match ($item->status_rka) {
                        'final' => 'RKA final',
                        'draft' => 'RKA draft',
                        default => $item->penilaian_selesai ? 'Menunggu ketua auditor' : 'Penilaian belum lengkap',
                    };
                @endphp

                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-semibold text-gray-800">{{ $item->upt?->nama_upt ?? '-' }}</h2>
                                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                    Periode {{ $item->periode?->tahun ?? '-' }}
                                </span>
                                <span class="rounded-full px-2 py-1 text-xs font-medium {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-2 text-sm text-gray-600 sm:grid-cols-3">
                                <p>
                                    <i class="bi bi-calendar3 mr-1 text-blue-600"></i>
                                    {{ $item->tanggal_audit ? \Illuminate\Support\Carbon::parse($item->tanggal_audit)->locale('id')->translatedFormat('d F Y') : '-' }}
                                </p>
                                <p>
                                    <i class="bi bi-person-check mr-1 text-blue-600"></i>
                                    {{ $item->auditor1?->nama_lengkap ?? '-' }}
                                </p>
                                <p>
                                    <i class="bi bi-people mr-1 text-blue-600"></i>
                                    {{ $item->auditor2?->nama_lengkap ?? '-' }}
                                </p>
                            </div>

                            <div class="mt-4">
                                <div class="mb-1 flex items-center justify-between gap-4 text-xs text-gray-500">
                                    <span>{{ $item->item_terjawab }} dari {{ $item->total_item }} item dinilai</span>
                                    <span class="font-semibold text-gray-700">{{ $item->persentase }}%</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full {{ $item->rka ? 'bg-green-500' : 'bg-blue-500' }}"
                                        style="width: {{ $item->persentase }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex shrink-0 flex-col gap-2 sm:flex-row lg:flex-col">
                            @if ($item->rka)
                                <a href="{{ route($showRouteName, $item->penugasan_id) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                    <i class="bi bi-eye"></i>
                                    Lihat RKA
                                </a>
                            @else
                                <span class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400">
                                    <i class="bi bi-lock"></i>
                                    Belum ada RKA
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white p-10 text-center">
                    <i class="bi bi-file-earmark-text text-4xl text-gray-300"></i>
                    <p class="mt-3 text-sm font-medium text-gray-500">Belum ada penugasan audit.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
