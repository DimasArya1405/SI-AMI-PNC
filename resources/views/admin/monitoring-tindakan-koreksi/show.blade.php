<x-app-layout>
    @php
        $isKepalaP4mp = Auth::user()->role === 'kepala_p4mp';
        $indexRouteName = $isKepalaP4mp ? 'kepala_p4mp.tindakan_koreksi.index' : 'admin.monitoring_tk.index';
        $exportRouteName = $isKepalaP4mp ? 'kepala_p4mp.tindakan_koreksi.export' : 'admin.monitoring_tk.export';
        $previewRouteName = $isKepalaP4mp ? 'kepala_p4mp.tindakan_koreksi.preview_bukti' : 'admin.monitoring_tk.preview_bukti';
        $previewDokumenDosenRouteName = $isKepalaP4mp ? 'kepala_p4mp.tindakan_koreksi.dokumen_dosen.preview' : 'admin.monitoring_tk.dokumen_dosen.preview';
    @endphp

    @include($isKepalaP4mp ? 'kepala-p4mp.sidebar' : 'admin.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Detail Tindakan Koreksi</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $penugasan->upt?->nama_upt ?? '-' }} &middot; Periode {{ $penugasan->periode?->tahun ?? '-' }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route($exportRouteName, $penugasan->penugasan_id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
                        </a>
                        <a href="{{ route($indexRouteName) }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            @php
                $semuaSiapFinalisasi = $temuan->isNotEmpty()
                    && $temuan->every(fn ($jawaban) => $jawaban->tindakanKoreksi && $jawaban->tindakanKoreksi->status === 'selesai');
                $sudahFinalisasi = (bool) $verifikasiTk?->finalized_at;
            @endphp

            @if ($temuan->isNotEmpty() && $isKepalaP4mp)
                <form action="{{ route('kepala_p4mp.tindakan_koreksi.finalisasi', $penugasan->penugasan_id) }}" method="POST"
                    data-scroll-target="verifikasi-p4mp"
                    class="flex flex-col gap-4">
                    @csrf
            @endif

            @forelse ($temuan as $index => $jawaban)
                @php
                    $tk = $jawaban->tindakanKoreksi;
                    $dokumenAuditee = $tk?->dokumenAuditee ?? collect();
                    $dokumenDosenDisetujui = $tk?->dokumenDosen ?? collect();
                    $adaBuktiPelaksanaan = (bool) $tk?->bukti_file_path || $dokumenAuditee->isNotEmpty() || $dokumenDosenDisetujui->isNotEmpty();
                    $status = $tk?->status ?? 'belum_dibuat';
                    $p4mpStatus = $tk?->p4mp_status;
                    $kategori = $jawaban->rkaTemuan?->kategori_final ?: $jawaban->kategori_temuan;
                    $kondisi = $jawaban->rkaTemuan?->kondisi_final ?: $jawaban->catatan;
                    $ketuaAuditor = $penugasan->auditor1?->nama_lengkap ?? '-';
                    $anggotaAuditor = $penugasan->auditor2?->nama_lengkap ?? '-';
                    $auditeeName = $penugasan->upt?->nama_upt ?? '-';
                    $itemPath = collect($jawaban->item_path ?? []);
                    $temuanItemId = $jawaban->upt_item_sub_standar_id;
                    $statusClass = match ($status) {
                        'diajukan' => 'bg-blue-100 text-blue-700',
                        'ditolak' => 'bg-red-100 text-red-700',
                        'disetujui' => 'bg-yellow-100 text-yellow-800',
                        'selesai' => 'bg-green-100 text-green-700',
                        default => 'bg-orange-100 text-orange-700',
                    };
                    $p4mpClass = match ($p4mpStatus) {
                        'terverifikasi' => 'bg-green-100 text-green-700',
                        'perlu_perbaikan' => 'bg-red-100 text-red-700',
                        'menunggu_verifikasi' => 'bg-indigo-100 text-indigo-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                    $nextAction = match (true) {
                        !$tk => 'Belum ada tindakan koreksi dari auditor.',
                        $status !== 'selesai' => 'Menunggu auditor menyelesaikan penilaian ulang.',
                        !$p4mpStatus || $p4mpStatus === 'menunggu_verifikasi' => $isKepalaP4mp ? 'Siap difinalisasi oleh Kepala P4MP.' : 'Menunggu finalisasi Kepala P4MP.',
                        $p4mpStatus === 'perlu_perbaikan' => 'Data lama: pernah ditandai perlu perbaikan. Finalisasi ulang jika sudah sesuai.',
                        default => 'Tindakan koreksi sudah diverifikasi P4MP.',
                    };
                    $steps = [
                        ['label' => 'Temuan', 'done' => true],
                        ['label' => 'Usulan', 'done' => (bool) $tk?->rencana_koreksi],
                        ['label' => 'Bukti', 'done' => $adaBuktiPelaksanaan],
                        ['label' => 'Auditor', 'done' => $status === 'selesai'],
                        ['label' => 'P4MP', 'done' => $p4mpStatus === 'terverifikasi'],
                    ];
                @endphp

                <article id="tk-{{ $jawaban->id }}" class="scroll-mt-24 rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-4 sm:p-5">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">Temuan {{ $index + 1 }}</span>
                                    <span class="rounded px-2 py-1 text-xs font-semibold {{ $statusClass }}">{{ str_replace('_', ' ', ucfirst($status)) }}</span>
                                    <span class="rounded bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">{{ $kategori ?? '-' }}</span>
                                    <span class="rounded px-2 py-1 text-xs font-semibold {{ $p4mpClass }}">
                                        {{ $p4mpStatus ? str_replace('_', ' ', ucfirst($p4mpStatus)) : 'Menunggu finalisasi P4MP' }}
                                    </span>
                                </div>
                                <h2 class="mt-3 text-base font-semibold text-gray-900">
                                    {{ $jawaban->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->nama_standar_mutu ?? '-' }}
                                </h2>
                                <div class="mt-2 flex flex-col gap-1.5">
                                    @forelse ($itemPath as $pathItem)
                                        @php
                                            $isTemuanItem = $pathItem->upt_item_sub_standar_id === $temuanItemId;
                                            $level = $pathItem->level ?? $loop->iteration;
                                            $indentClass = match (true) {
                                                $level >= 4 => 'ml-12',
                                                $level === 3 => 'ml-8',
                                                $level === 2 => 'ml-4',
                                                default => '',
                                            };
                                        @endphp
                                        <p class="{{ $indentClass }} text-sm {{ $isTemuanItem ? 'font-semibold text-gray-900' : 'font-medium text-gray-600' }}">
                                            - {{ $pathItem->nama_item }}
                                        </p>
                                    @empty
                                        <p class="text-sm font-medium text-gray-700">{{ $jawaban->itemSubStandar?->nama_item ?? '-' }}</p>
                                    @endforelse
                                </div>
                            </div>
                            <div class="rounded border border-indigo-100 bg-indigo-50 p-3 text-sm text-indigo-800 lg:max-w-sm">
                                <p class="font-semibold">Status Tindakan Koreksi</p>
                                <p class="mt-1">{{ $nextAction }}</p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-5 gap-2">
                            @foreach ($steps as $step)
                                <div class="min-w-0">
                                    <div class="h-1.5 rounded-full {{ $step['done'] ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                                    <p class="mt-2 truncate text-xs font-medium {{ $step['done'] ? 'text-green-700' : 'text-gray-500' }}">{{ $step['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 xl:grid-cols-3">
                        <section class="rounded border border-gray-200 p-4 xl:col-span-2">
                            <h3 class="text-sm font-semibold text-gray-900">Ringkasan Kondisi Audit</h3>
                            <p class="mt-3 whitespace-pre-line rounded bg-gray-50 p-3 text-sm text-gray-700">{{ $kondisi ?: 'Belum ada kondisi final RKA.' }}</p>
                            <dl class="mt-3 grid grid-cols-1 gap-2 text-sm sm:grid-cols-3">
                                <div>
                                    <dt class="text-xs uppercase text-gray-500">Ketua Auditor</dt>
                                    <dd class="font-medium text-gray-800">{{ $ketuaAuditor }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase text-gray-500">Anggota</dt>
                                    <dd class="font-medium text-gray-800">{{ $anggotaAuditor }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase text-gray-500">Auditee</dt>
                                    <dd class="font-medium text-gray-800">{{ $auditeeName }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="rounded border border-gray-200 p-4">
                            <h3 class="text-sm font-semibold text-gray-900">Bukti dan Penilaian</h3>
                            <p class="mt-3 text-xs font-semibold uppercase text-gray-500">Bukti auditee</p>
                            @if ($dokumenAuditee->isNotEmpty())
                                <div class="mt-2 space-y-2">
                                    @foreach ($dokumenAuditee as $dokumenAuditeeItem)
                                        <div class="rounded border border-gray-200 bg-gray-50 p-3">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                <div class="min-w-0">
                                                    <p class="break-all text-sm font-semibold text-gray-800">{{ $dokumenAuditeeItem->nama_file }}</p>
                                                    @if ($dokumenAuditeeItem->keterangan)
                                                        <p class="mt-1 whitespace-pre-line text-xs text-gray-600">{{ $dokumenAuditeeItem->keterangan }}</p>
                                                    @endif
                                                </div>
                                                <button type="button"
                                                    data-preview-url="{{ route($previewRouteName, $dokumenAuditeeItem->dokumen_tk_auditee_id) }}"
                                                    data-extension="{{ strtolower(pathinfo($dokumenAuditeeItem->nama_file, PATHINFO_EXTENSION)) }}"
                                                    data-file-name="{{ $dokumenAuditeeItem->nama_file }}"
                                                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded bg-gray-700 px-3 py-2 text-xs font-medium text-white hover:bg-gray-800">
                                                    <i class="bi bi-eye"></i>
                                                    Lihat
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($tk?->bukti_file_path)
                                <p class="mt-1 text-sm text-gray-700">{{ $tk->bukti_nama_file }}</p>
                                <button type="button"
                                    data-preview-url="{{ route($previewRouteName, $tk->tindakan_koreksi_id) }}"
                                    data-extension="{{ strtolower(pathinfo($tk->bukti_nama_file, PATHINFO_EXTENSION)) }}"
                                    data-file-name="{{ $tk->bukti_nama_file }}"
                                    class="mt-3 inline-flex items-center justify-center gap-2 rounded bg-gray-700 px-3 py-2 text-xs font-medium text-white hover:bg-gray-800">
                                    <i class="bi bi-eye"></i>
                                    Lihat Bukti
                                </button>
                            @else
                                <p class="mt-1 text-sm text-gray-700">-</p>
                            @endif
                            @if ($dokumenDosenDisetujui->isNotEmpty())
                                <div class="mt-4 rounded border border-indigo-100 bg-indigo-50 p-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-xs font-semibold uppercase text-indigo-700">Dokumen Dosen Disetujui Auditee</p>
                                        <span class="rounded bg-white px-2 py-1 text-xs font-semibold text-indigo-700">{{ $dokumenDosenDisetujui->count() }} dokumen</span>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        @foreach ($dokumenDosenDisetujui as $dokumenDosen)
                                            <div class="rounded border border-indigo-100 bg-white p-3">
                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <p class="break-all text-sm font-semibold text-gray-800">{{ $dokumenDosen->nama_file }}</p>
                                                        <p class="mt-1 text-xs text-gray-500">Dosen: {{ $dokumenDosen->dosen?->nama_lengkap ?? '-' }}</p>
                                                        @if ($dokumenDosen->keterangan)
                                                            <p class="mt-1 whitespace-pre-line text-xs text-gray-600">{{ $dokumenDosen->keterangan }}</p>
                                                        @endif
                                                    </div>
                                                    <button type="button"
                                                        data-preview-url="{{ route($previewDokumenDosenRouteName, $dokumenDosen->dokumen_tk_dosen_id) }}"
                                                        data-extension="{{ strtolower(pathinfo($dokumenDosen->nama_file, PATHINFO_EXTENSION)) }}"
                                                        data-file-name="{{ $dokumenDosen->nama_file }}"
                                                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded bg-gray-700 px-3 py-2 text-xs font-medium text-white hover:bg-gray-800">
                                                        <i class="bi bi-eye"></i>
                                                        Lihat
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <p class="mt-4 text-xs font-semibold uppercase text-gray-500">Hasil auditor</p>
                            <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk?->hasil_penilaian_auditor ?: '-' }}</p>
                        </section>
                    </div>

                    <div class="border-t border-gray-100 p-4 sm:p-5">
                        <details class="rounded border border-gray-200 bg-gray-50 p-4">
                            <summary class="cursor-pointer text-sm font-semibold text-gray-900">Lihat detail analisis, usulan, dan pelaksanaan</summary>
                            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase text-gray-500">Analisa Ketidaksesuaian</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk?->analisis_ketidaksesuaian ?: '-' }}</p>
                                </div>
                                <div class="lg:col-span-2">
                                    <p class="text-xs font-semibold uppercase text-gray-500">Usulan Tindakan Koreksi</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk?->rencana_koreksi ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase text-gray-500">Catatan dari Auditee</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk?->pelaksanaan_deskripsi ?: '-' }}</p>
                                </div>
                            </div>
                        </details>

                        <section class="mt-4 rounded border border-indigo-100 bg-indigo-50 p-4">
                            <div>
                                <h3 class="text-sm font-semibold text-indigo-900">Catatan Verifikasi Item</h3>
                                @if ($tk?->p4mp_verified_at)
                                    <p class="mt-1 text-xs text-indigo-700">
                                        Terakhir difinalisasi oleh {{ $tk->p4mpVerifiedBy?->name ?? 'Kepala P4MP' }} pada {{ $tk->p4mp_verified_at->locale('id')->translatedFormat('d F Y H:i') }}
                                    </p>
                                @endif
                            </div>

                            @if ($isKepalaP4mp && $tk && $tk->status === 'selesai')
                                <div class="mt-4 border-t border-indigo-200 pt-4">
                                    <label class="text-sm font-medium text-gray-700">Catatan khusus untuk temuan ini</label>
                                    <textarea name="catatan_item[{{ $tk->tindakan_koreksi_id }}]" rows="3"
                                        data-verifikasi-item-note
                                        class="mt-1 block w-full rounded border-gray-300 text-sm"
                                        placeholder="Opsional. Isi jika ada catatan khusus yang ingin muncul sebagai rincian pada bagian verifikasi PDF.">{{ old('catatan_item.' . $tk->tindakan_koreksi_id, $tk->p4mp_catatan) }}</textarea>
                                </div>
                            @elseif (!$isKepalaP4mp)
                                <div class="mt-4 border-t border-indigo-200 pt-4">
                                    <p class="text-xs font-semibold uppercase text-indigo-700">Catatan item dari Kepala P4MP</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk?->p4mp_catatan ?: 'Belum ada catatan item.' }}</p>
                                </div>
                            @else
                                <p class="mt-4 rounded bg-white p-3 text-sm text-indigo-700">
                                    Catatan item aktif setelah auditor menyelesaikan penilaian ulang.
                                </p>
                            @endif
                        </section>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white p-10 text-center">
                    <i class="bi bi-check2-circle text-4xl text-green-400"></i>
                    <p class="mt-3 text-sm font-medium text-gray-500">Belum ada temuan tindakan koreksi.</p>
                </div>
            @endforelse

            @if ($temuan->isNotEmpty() && $isKepalaP4mp)
                    <section id="verifikasi-p4mp" class="scroll-mt-24 rounded-lg border border-indigo-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Finalisasi Verifikasi P4MP</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Catatan keseluruhan akan menjadi narasi utama pada bagian "Verifikasi Pelaksanaan Tindakan Koreksi". Catatan per item akan ditampilkan sebagai rincian jika diisi.
                                </p>
                                @if ($sudahFinalisasi)
                                    <p class="mt-2 text-xs font-medium text-green-700">
                                        Sudah difinalisasi oleh {{ $verifikasiTk->finalizedBy?->name ?? 'Kepala P4MP' }} pada {{ $verifikasiTk->finalized_at->locale('id')->translatedFormat('d F Y H:i') }}.
                                    </p>
                                @endif
                            </div>
                            <span class="inline-flex w-fit rounded px-3 py-1 text-xs font-semibold {{ $semuaSiapFinalisasi ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $semuaSiapFinalisasi ? 'Siap finalisasi' : 'Menunggu auditor' }}
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-3">
                            <div class="lg:col-span-2">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <label class="text-sm font-medium text-gray-700">Catatan keseluruhan verifikasi P4MP</label>
                                    <button type="button" id="sync-catatan-verifikasi"
                                        class="inline-flex w-fit items-center justify-center rounded border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                                        Ambil dari catatan item
                                    </button>
                                </div>
                                <textarea id="catatan-umum-verifikasi" name="catatan_umum" rows="5"
                                    class="mt-1 block w-full rounded border-gray-300 text-sm"
                                    placeholder="Contoh: Tindakan koreksi telah dilaksanakan, namun belum seluruhnya dapat diselesaikan sesuai standar...">{{ old('catatan_umum', $verifikasiTk?->catatan_umum) }}</textarea>
                                <p class="mt-2 text-xs text-gray-500">Jika catatan item diisi, catatan keseluruhan akan disusun otomatis selama kolom ini belum diedit manual.</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Nama Wadir I</label>
                                <input type="text" name="wadir1_nama" value="{{ old('wadir1_nama', $verifikasiTk?->wadir1_nama) }}"
                                    class="mt-1 block w-full rounded border-gray-300 text-sm"
                                    placeholder="Opsional untuk tanda tangan PDF">
                                <div class="mt-4 rounded bg-indigo-50 p-3 text-sm text-indigo-800">
                                    <p class="font-semibold">Efek finalisasi</p>
                                    <p class="mt-1">Semua tindakan koreksi yang sudah selesai akan ditandai terverifikasi P4MP dan notifikasi dikirim ke auditor serta auditee.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                            @unless ($semuaSiapFinalisasi)
                                <p class="text-sm text-yellow-700 sm:mr-auto">Finalisasi aktif setelah semua temuan selesai dinilai auditor.</p>
                            @endunless
                            <button type="submit"
                                @disabled(!$semuaSiapFinalisasi)
                                class="inline-flex justify-center rounded px-4 py-2 text-sm font-medium text-white {{ $semuaSiapFinalisasi ? 'bg-indigo-600 hover:bg-indigo-700' : 'cursor-not-allowed bg-gray-400' }}">
                                {{ $sudahFinalisasi ? 'Finalisasi Ulang Verifikasi' : 'Finalisasi Verifikasi P4MP' }}
                            </button>
                        </div>
                    </section>
                </form>
            @elseif ($temuan->isNotEmpty())
                <section id="verifikasi-p4mp" class="scroll-mt-24 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Status Verifikasi P4MP</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Finalisasi tindakan koreksi dilakukan oleh akun Kepala P4MP. Admin hanya dapat memantau dan export dokumen.
                            </p>
                            @if ($sudahFinalisasi)
                                <p class="mt-2 text-xs font-medium text-green-700">
                                    Sudah difinalisasi oleh {{ $verifikasiTk->finalizedBy?->name ?? 'Kepala P4MP' }} pada {{ $verifikasiTk->finalized_at->locale('id')->translatedFormat('d F Y H:i') }}.
                                </p>
                            @endif
                        </div>
                        <span class="inline-flex w-fit rounded px-3 py-1 text-xs font-semibold {{ $sudahFinalisasi ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $sudahFinalisasi ? 'Terverifikasi' : 'Menunggu Kepala P4MP' }}
                        </span>
                    </div>

                    <div class="mt-5 rounded bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase text-gray-500">Catatan keseluruhan</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $verifikasiTk?->catatan_umum ?: 'Belum ada catatan keseluruhan.' }}</p>
                    </div>
                </section>
            @endif
        </div>
    </div>

    @include('layouts.partials.smart-file-preview')

    @if ($isKepalaP4mp)
        @push('js')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const umum = document.getElementById('catatan-umum-verifikasi');
                    const tombolSync = document.getElementById('sync-catatan-verifikasi');
                    const itemNotes = Array.from(document.querySelectorAll('[data-verifikasi-item-note]'));

                    if (!umum || itemNotes.length === 0) {
                        return;
                    }

                    let generatedText = '';
                    let umumDieditManual = false;

                    const bersihkanLabelLama = function (text) {
                        return text
                            .split("\n")
                            .map(function (line) {
                                return line.replace(/^\s*(\d+\.\s*)?Temuan\s+\d+\s+-\s*[^:]+:\s*/i, '$1');
                            })
                            .join("\n");
                    };

                    const buatCatatanUmum = function () {
                        const notes = itemNotes
                            .map(function (textarea) {
                                return textarea.value.trim() || null;
                            })
                            .filter(Boolean);

                        if (notes.length === 0) {
                            return '';
                        }

                        return 'Tindakan koreksi telah dilaksanakan, dengan catatan verifikasi sebagai berikut:' + "\n" +
                            notes.map(function (note, index) {
                                return (index + 1) + '. ' + note;
                            }).join("\n");
                    };

                    const sinkronkanCatatan = function (paksa = false) {
                        const nextGeneratedText = buatCatatanUmum();
                        const bolehUpdate = paksa || !umumDieditManual || umum.value.trim() === '' || umum.value === generatedText;

                        if (bolehUpdate) {
                            umum.value = nextGeneratedText;
                            generatedText = nextGeneratedText;
                            umumDieditManual = false;
                        }
                    };

                    itemNotes.forEach(function (textarea) {
                        textarea.addEventListener('input', function () {
                            sinkronkanCatatan(false);
                        });
                    });

                    umum.addEventListener('input', function () {
                        umumDieditManual = umum.value !== generatedText;
                    });

                    tombolSync?.addEventListener('click', function () {
                        sinkronkanCatatan(true);
                        umum.focus();
                    });

                if (/Temuan\s+\d+\s+-/i.test(umum.value)) {
                    umum.value = bersihkanLabelLama(umum.value);
                }

                if (umum.value.trim() === '') {
                    sinkronkanCatatan(true);
                }
                });
            </script>
        @endpush
    @endif
    @include('layouts.partials.back-to-top')
</x-app-layout>
