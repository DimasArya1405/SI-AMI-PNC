<x-app-layout>
    @include('auditee.sidebar')

    <div class="ml-60 py-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">Ringkasan Kondisi Audit</h1>
                <p class="mt-1 text-sm text-gray-600">
                    RKA dapat dilihat setelah difinalisasi oleh tim auditor melalui rapat internal.
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('auditee.rka.index') }}"
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
                                <a href="{{ route('auditee.rka.index') }}"
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
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-semibold text-gray-800">
                                    {{ $item->upt?->nama_upt ?? '-' }}
                                </h2>
                                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                    Periode {{ $item->periode?->tahun ?? '-' }}
                                </span>
                                @if ($item->rka_tersedia)
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                        RKA final tersedia
                                    </span>
                                @elseif ($item->penilaian_selesai)
                                    <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">
                                        Menunggu finalisasi auditor
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                        Penilaian belum lengkap
                                    </span>
                                @endif
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
                                    <i class="bi bi-clock mr-1 text-blue-600"></i>
                                    {{ $item->jam ?? '-' }}
                                </p>
                            </div>

                            <div class="mt-4">
                                <div class="mb-1 flex items-center justify-between gap-4 text-xs text-gray-500">
                                    <span>{{ $item->item_terjawab }} dari {{ $item->total_item }} item dinilai</span>
                                    <span class="font-semibold text-gray-700">{{ $item->persentase }}%</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full {{ $item->rka_tersedia ? 'bg-green-500' : 'bg-blue-500' }}"
                                        style="width: {{ $item->persentase }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($item->rka_tersedia)
                                <a href="{{ route('auditee.rka.show', $item->penugasan_id) }}"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 lg:w-auto">
                                    <i class="bi bi-eye"></i>
                                    Lihat RKA
                                </a>
                            @else
                                <span class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 lg:w-auto">
                                    <i class="bi bi-lock"></i>
                                    Belum final
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white p-8 text-center sm:p-12">
                    <i class="bi bi-file-earmark-text text-4xl text-gray-300"></i>
                    <p class="mt-3 text-sm font-medium text-gray-500">Belum ada penugasan audit untuk unit Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
