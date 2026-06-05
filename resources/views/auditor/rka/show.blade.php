<x-app-layout>
    @include('auditor.sidebar')

    <div class="ml-60 py-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Penyusunan RKA</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $penugasan->upt?->nama_upt ?? '-' }} - Periode {{ $penugasan->periode?->tahun ?? '-' }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('auditor.rka.export', $rka->rka_id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            <i class="bi bi-download"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('auditor.rka.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-gray-500 px-4 py-2 text-sm font-medium text-white hover:bg-gray-600">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Total Item</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $ringkasan['total_item'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Sesuai</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ $ringkasan['sesuai'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Temuan RKA</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">{{ $ringkasan['temuan'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">KTS</p>
                    <p class="mt-1 text-2xl font-bold text-orange-600">{{ $ringkasan['kts'] }}</p>
                </div>
                <div class="col-span-2 rounded-lg border bg-white p-4 shadow-sm lg:col-span-1">
                    <p class="text-xs text-gray-500">OB</p>
                    <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $ringkasan['ob'] }}</p>
                </div>
            </div>

            <form action="{{ route('auditor.rka.update', $rka->rka_id) }}" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('patch')

                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-gray-800">Hasil Rapat Tim Auditor</h2>
                            <p class="mt-1 text-sm text-gray-500">Isi bagian ini berdasarkan rapat ketua auditor dan anggota auditor.</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $rka->status === 'final' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $rka->status === 'final' ? 'Final' : 'Draft' }}
                        </span>
                    </div>

                    @unless ($isKetuaAuditor)
                        <div class="mt-4 rounded border border-blue-100 bg-blue-50 p-3 text-sm text-blue-700">
                            Anda terdaftar sebagai auditor anggota. RKA dapat dilihat dan diexport, tetapi perubahan hanya dapat dilakukan oleh ketua auditor.
                        </div>
                    @endunless

                    @if ($rka->status === 'final')
                        <div class="mt-4 rounded border border-green-100 bg-green-50 p-3 text-sm text-green-700">
                            RKA sudah final pada {{ optional($rka->finalized_at)->translatedFormat('d F Y H:i') ?? '-' }}.
                            Perubahan setelah finalisasi akan mengembalikan status menjadi draft jika disimpan sebagai draft.
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Rapat Internal</label>
                            <input type="date" name="tanggal_rapat" value="{{ old('tanggal_rapat', optional($rka->tanggal_rapat)->format('Y-m-d')) }}"
                                @disabled(!$isKetuaAuditor)
                                class="mt-1 block w-full rounded border-gray-300 text-sm">
                        </div>
                        <div class="lg:col-span-2">
                            <label class="text-sm font-medium text-gray-700">Tim Auditor</label>
                            <div class="mt-1 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="rounded border border-gray-200 bg-gray-50 p-3">
                                    <p class="text-xs font-semibold uppercase text-gray-500">Ketua Auditor</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">{{ $penugasan->auditor1?->nama_lengkap ?? '-' }}</p>
                                    @if ($penugasan->auditor1?->email)
                                        <p class="mt-1 text-xs text-gray-500">{{ $penugasan->auditor1->email }}</p>
                                    @endif
                                </div>
                                <div class="rounded border border-gray-200 bg-gray-50 p-3">
                                    <p class="text-xs font-semibold uppercase text-gray-500">Auditor Anggota</p>
                                    <p class="mt-1 text-sm font-medium text-gray-800">{{ $penugasan->auditor2?->nama_lengkap ?? '-' }}</p>
                                    @if ($penugasan->auditor2?->email)
                                        <p class="mt-1 text-xs text-gray-500">{{ $penugasan->auditor2->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 p-4 sm:p-6">
                        <h2 class="text-base font-semibold text-gray-800">Rumusan Final Temuan</h2>
                        <p class="mt-1 text-sm text-gray-500">Data awal diambil dari penilaian auditor, lalu dirumuskan lagi sebagai hasil rapat.</p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @forelse ($temuanPerStandar as $standar)
                            <section class="p-4 sm:p-6">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <h3 class="font-semibold text-gray-800">{{ $standar['nama_standar'] }}</h3>
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        {{ $standar['temuan']->count() }} temuan
                                    </span>
                                </div>

                                <div class="mt-4 flex flex-col gap-4">
                                    @foreach ($standar['temuan'] as $temuan)
                                        @php
                                            $jawaban = $temuan->jawabanAudit;
                                            $item = $jawaban?->itemSubStandar?->nama_item ?? '-';
                                        @endphp

                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-medium text-gray-800">{{ $item }}</p>
                                                <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-gray-700">
                                                    Data awal: {{ $jawaban?->kategori_temuan ?? '-' }}
                                                </span>
                                            </div>
                                            <p class="mt-2 rounded bg-white p-3 text-sm text-gray-600">
                                                Catatan awal auditor: {{ $jawaban?->catatan ?: '-' }}
                                            </p>

                                            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                                                <div class="lg:col-span-2">
                                                    <label class="text-sm font-medium text-gray-700">Kondisi Final RKA</label>
                                                    <textarea name="temuan[{{ $temuan->rka_temuan_id }}][kondisi_final]" rows="3" required
                                                        @disabled(!$isKetuaAuditor)
                                                        class="mt-1 block w-full rounded border-gray-300 text-sm">{{ old("temuan.{$temuan->rka_temuan_id}.kondisi_final", $temuan->kondisi_final) }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="text-sm font-medium text-gray-700">Kategori Final</label>
                                                    <select name="temuan[{{ $temuan->rka_temuan_id }}][kategori_final]" required
                                                        @disabled(!$isKetuaAuditor)
                                                        class="mt-1 block w-full rounded border-gray-300 text-sm">
                                                        <option value="KTS" @selected(old("temuan.{$temuan->rka_temuan_id}.kategori_final", $temuan->kategori_final) === 'KTS')>KTS</option>
                                                        <option value="OB" @selected(old("temuan.{$temuan->rka_temuan_id}.kategori_final", $temuan->kategori_final) === 'OB')>OB</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @empty
                            <div class="p-8 text-center text-sm text-gray-500">
                                Tidak ada temuan. RKA dapat berisi ringkasan bahwa seluruh item sesuai.
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($isKetuaAuditor)
                    <div class="flex flex-col gap-2 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:justify-end">
                        <button type="submit" name="aksi" value="simpan"
                            class="inline-flex justify-center rounded bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            Simpan Draft
                        </button>
                        <button type="submit" name="aksi" value="finalisasi"
                            class="inline-flex justify-center rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            Finalisasi RKA
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-app-layout>
