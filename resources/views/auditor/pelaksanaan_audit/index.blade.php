<x-app-layout>
    @include('auditor.sidebar')

    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-6">

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-800">{{ __('Data Pelaksanaan Audit') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Berikut adalah daftar Unit Pelaksana Teknis (UPT) yang ditugaskan kepada Anda.
                    </p>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
                <form method="GET" action="{{ route('auditor.pelaksanaan_audit') }}"
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
                                <a href="{{ route('auditor.pelaksanaan_audit') }}"
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

            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4 px-1">UPT Program Studi</h3>

                @if ($penugasan->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($penugasan as $item)
                            <div
                                class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">

                                <div class="p-6 flex-grow">
                                    <div class="flex items-start justify-between">
                                        @php
                                            $isProdi = $item->upt->kategori_upt == 'Prodi';
                                            $iconClass = $isProdi ? 'bi-mortarboard-fill' : 'bi-gear-wide-connected';
                                            $colorClass = $isProdi
                                                ? 'bg-blue-50 text-blue-600 group-hover:bg-blue-600'
                                                : 'bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600';
                                        @endphp

                                        <div
                                            class="w-12 h-12 {{ $colorClass }} rounded-lg flex items-center justify-center group-hover:text-white transition-all duration-300">
                                            <i class="bi {{ $iconClass }} text-2xl"></i>
                                        </div>

                                        <span
                                            class="text-[10px] font-bold px-2 py-1 {{ $isProdi ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }} rounded-full uppercase">
                                            {{ $item->upt->kategori_upt }}
                                        </span>
                                    </div>

                                    <div class="mt-4">
                                        <h4
                                            class="text-lg font-bold text-gray-900 leading-tight group-hover:text-gray-700">
                                            {{ $item->upt->nama_upt }}
                                        </h4>
                                        <p class="text-xs text-gray-400 mt-2">
                                            <i class="bi bi-calendar3 mr-1"></i>
                                            Periode {{ $item->periode?->tahun ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-auto">
                                    <a href="{{ route('auditor.pelaksanaan_audit.detail', ['id' => $item->upt->upt_id, 'periode_id' => $item->periode_id]) }}"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white font-medium rounded-lg text-sm hover:bg-yellow-700 transition-all">
                                        <i class="bi bi-eye mr-2"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="bi bi-folder2-open text-3xl text-gray-300"></i>
                        </div>
                        <p class="text-gray-500 italic font-medium">
                            Belum ada penugasan untuk Anda pada periode ini.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
