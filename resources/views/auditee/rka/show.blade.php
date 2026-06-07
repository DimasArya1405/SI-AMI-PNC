<x-app-layout>
    @include('auditee.sidebar')

    <div class="ml-60 py-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Ringkasan Kondisi Audit</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $penugasan->upt?->nama_upt ?? '-' }} - Periode {{ $penugasan->periode?->tahun ?? '-' }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('auditee.rka.export', $penugasan->penugasan_id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            <i class="bi bi-download"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('auditee.tindakan_koreksi.show', $penugasan->penugasan_id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <i class="bi bi-clipboard-check"></i>
                            Tindakan Koreksi
                        </a>
                        <a href="{{ route('auditee.rka.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-gray-500 px-4 py-2 text-sm font-medium text-white hover:bg-gray-600">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
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
                <h2 class="text-base font-semibold text-gray-800">Informasi RKA Final</h2>
                <div class="mt-4 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Audit</p>
                        <p class="mt-1 font-medium text-gray-800">
                            {{ $penugasan->tanggal_audit ? \Illuminate\Support\Carbon::parse($penugasan->tanggal_audit)->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jam</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $penugasan->jam ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Auditor Ketua</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $penugasan->auditor1?->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Auditor Anggota</p>
                        <p class="mt-1 font-medium text-gray-800">{{ $penugasan->auditor2?->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Rapat RKA</p>
                        <p class="mt-1 font-medium text-gray-800">{{ optional($rka->tanggal_rapat)->translatedFormat('d F Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Finalisasi</p>
                        <p class="mt-1 font-medium text-gray-800">{{ optional($rka->finalized_at)->translatedFormat('d F Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 p-4 sm:p-6">
                    <h2 class="text-base font-semibold text-gray-800">Rumusan Final Kondisi Audit per Standar</h2>
                    <p class="mt-1 text-sm text-gray-500">Daftar kondisi hasil rapat internal tim auditor.</p>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse ($ringkasanStandar as $standar)
                        <section class="p-4 sm:p-6">
                            <h3 class="font-semibold text-gray-800">{{ $standar['nama_standar'] }}</h3>

                            <div class="mt-3 overflow-x-auto">
                                <table class="min-w-[42rem] w-full text-left text-sm">
                                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="px-3 py-3">Item Pertanyaan</th>
                                            <th class="px-3 py-3">Kondisi Final</th>
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
                                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $temuan->kategori_final === 'KTS' ? 'bg-orange-100 text-orange-700' : 'bg-yellow-100 text-yellow-700' }}">
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
                            Tidak ada temuan pada RKA final.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
