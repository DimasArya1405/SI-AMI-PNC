<x-app-layout>
    @php
        $isKepalaP4mp = Auth::user()->role === 'kepala_p4mp';
        $indexRouteName = $isKepalaP4mp ? 'kepala_p4mp.rka.index' : 'admin.rka.index';
        $exportRouteName = $isKepalaP4mp ? 'kepala_p4mp.rka.export' : 'admin.rka.export';
        $tkRouteName = $isKepalaP4mp ? 'kepala_p4mp.tindakan_koreksi.show' : 'admin.monitoring_tk.show';
    @endphp

    @include($isKepalaP4mp ? 'kepala-p4mp.sidebar' : 'admin.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl font-bold text-gray-800">Detail RKA</h1>
                            <span
                                class="rounded-full px-2 py-1 text-xs font-semibold {{ $rka->status === 'final' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $rka->status === 'final' ? 'Final' : 'Draft' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $penugasan->upt?->nama_upt ?? '-' }} - Periode {{ $penugasan->periode?->tahun ?? '-' }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route($exportRouteName, $rka->rka_id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
                            {{-- {{ $rka->rka_id }} --}}
                        </a>
                        <a href="{{ route($tkRouteName, $penugasan->penugasan_id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <i class="bi bi-clipboard-check"></i>
                            Tindakan Koreksi
                        </a>
                        @if ($isKepalaP4mp)
                            @if ($rka->acc_p4mp != '1')
                                <button data-modal-target="modal-acc" data-modal-toggle="modal-acc"
                                    class="inline-flex items-center justify-center gap-2 rounded bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700">
                                    <i class="bi bi-pen"></i> {{-- Menggunakan icon pena dari Bootstrap Icons --}}
                                    Tanda Tangan
                                </button>
                            @endif
                        @endif
                        <a href="{{ route($indexRouteName) }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-gray-500 px-4 py-2 text-sm font-medium text-white hover:bg-gray-600">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            {{-- MODAL ACC --}}
            <div id="modal-acc" tabindex="-1"
                class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                        <button type="button"
                            class="absolute top-3 end-2.5 text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                            data-modal-hide="modal-acc">
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-fg-disabled w-12 h-12" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="mb-6 text-body">Apakah anda yakin akan menandatangani RKA ini?</h3>
                            <form action="{{ route('kepala_p4mp.rka.acc', $rka->rka_id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="flex items-center space-x-4 justify-center">
                                    <button data-modal-hide="modal-acc" type="submit"
                                        class="text-white transition duration-300 ease-in-out bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                                        Iya, saya yakin
                                    </button>
                                    <button data-modal-hide="modal-acc" type="button"
                                        class="text-body transition duration-300 ease-in-out bg-white box-border border border-default-medium hover:bg-gray-200 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Tidak,
                                        Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Total Item</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $ringkasan['total_item'] }}</p>
                </div>
                <div class="rounded-lg border border-green-200 bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Sesuai</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ $ringkasan['sesuai'] }}</p>
                </div>
                <div class="rounded-lg border border-red-200 bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Temuan RKA</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">{{ $ringkasan['temuan'] }}</p>
                </div>
                <div class="rounded-lg border border-orange-200 bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">KTS</p>
                    <p class="mt-1 text-2xl font-bold text-orange-600">{{ $ringkasan['kts'] }}</p>
                </div>
                <div class="col-span-2 rounded-lg border border-yellow-200 bg-white p-4 shadow-sm lg:col-span-1">
                    <p class="text-xs text-gray-500">OB</p>
                    <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $ringkasan['ob'] }}</p>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                <h2 class="text-base font-semibold text-gray-800">Informasi RKA</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Audit</p>
                        <p class="mt-1 font-medium text-gray-800">
                            {{ $penugasan->tanggal_audit ? \Illuminate\Support\Carbon::parse($penugasan->tanggal_audit)->locale('id')->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jam</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $penugasan->jam ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Ketua Auditor</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $penugasan->auditor1?->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Anggota Auditor</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $penugasan->auditor2?->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dibuat Oleh</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $rka->createdBy?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Finalisasi</p>
                        <p class="mt-1 font-medium text-gray-800">
                            {{ $rka->finalized_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Final Oleh</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $rka->finalizedBy?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        @if ($rka->acc_p4mp != '1')
                            <p class="mt-1 font-medium text-red-800">Belum di Tanda Tangani</p>
                        @else
                            <p class="mt-1 font-medium text-green-800">Sudah di Tanda Tangani</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 p-4 sm:p-6">
                    <h2 class="text-base font-semibold text-gray-800">Kondisi Audit per Standar</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Daftar temuan dari RKA yang disusun oleh tim auditor.
                    </p>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse ($temuanPerStandar as $standar)
                        <section class="p-4 sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="font-semibold text-gray-800">{{ $standar['nama_standar'] }}</h3>
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">
                                    {{ $standar['temuan']->count() }} temuan
                                </span>
                            </div>

                            <div class="mt-3 overflow-x-auto">
                                <table class="min-w-[44rem] w-full text-left text-sm">
                                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="px-3 py-3">Item Pertanyaan</th>
                                            <th class="px-3 py-3">Kondisi RKA</th>
                                            <th class="px-3 py-3 text-center">Kategori</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($standar['temuan'] as $temuan)
                                            <tr>
                                                <td class="px-3 py-3 align-top text-gray-700">
                                                    {{ $temuan->jawabanAudit?->itemSubStandar?->nama_item ?? '-' }}
                                                </td>
                                                <td class="px-3 py-3 align-top text-gray-700">
                                                    {{ $temuan->kondisi_final }}
                                                </td>
                                                <td class="px-3 py-3 text-center align-top">
                                                    <span
                                                        class="rounded-full px-2 py-1 text-xs font-semibold {{ $temuan->kategori_final === 'KTS' ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                        {{ $temuan->kategori_final }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500">
                            Belum ada temuan pada RKA ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @include('layouts.partials.back-to-top')
</x-app-layout>
