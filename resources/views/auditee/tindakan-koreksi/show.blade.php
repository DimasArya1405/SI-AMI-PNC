<x-app-layout>
    @include('auditee.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Tindakan Koreksi</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $penugasan->upt?->nama_upt ?? '-' }} &middot; Periode {{ $penugasan->periode?->tahun ?? '-' }}
                        </p>
                        @unless ($rkaDitandatangani)
                            <p class="mt-3 rounded border border-yellow-200 bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
                                Tindakan koreksi belum tersedia untuk dikerjakan karena RKA masih menunggu tanda tangan Kepala P4MP.
                            </p>
                        @endunless
                        @unless ($periodeAktif)
                            <p class="mt-3 rounded border border-yellow-200 bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
                                Periode ini tidak aktif. Tindakan koreksi hanya dapat dilihat dan tidak dapat diubah.
                            </p>
                        @endunless
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('auditee.tindakan_koreksi.export', $penugasan->penugasan_id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('auditee.tindakan_koreksi.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            @php
                $tkUntukTtd = $temuan->pluck('tindakanKoreksi')->filter()->values();
                $sudahPernahDitandatangani = $tkUntukTtd->contains(fn ($tk) => filled($tk->auditee_signed_at));
                $tandaTanganAuditee = $tkUntukTtd->first(fn ($tk) => filled($tk->auditee_signed_at));
                $bisaTandaTanganTk = $tkUntukTtd->isNotEmpty() && !$sudahPernahDitandatangani;
            @endphp

            @if ($periodeAktif && $rkaDitandatangani && $tkUntukTtd->isNotEmpty())
                <div class="rounded-lg border border-blue-100 bg-blue-50 p-4 shadow-sm sm:p-5">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-blue-900">Tanda Tangan Auditee</h2>
                            @if ($sudahPernahDitandatangani)
                                <p class="mt-1 text-sm text-green-700">
                                    Tindakan koreksi sudah ditandatangani auditee pada
                                    {{ $tandaTanganAuditee?->auditee_signed_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}.
                                </p>
                            @else
                                <p class="mt-1 text-sm text-blue-800">
                                    Klik tombol ini untuk menampilkan barcode tanda tangan auditee pada export PDF tindakan koreksi.
                                </p>
                            @endif
                        </div>

                        @if ($bisaTandaTanganTk)
                            <form action="{{ route('auditee.tindakan_koreksi.tanda_tangan', $penugasan->penugasan_id) }}" method="POST">
                                @csrf
                                @method('patch')
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                    <i class="bi bi-pen"></i>
                                    Tanda Tangani
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            @forelse ($temuan as $index => $jawaban)
                @php
                    $tk = $jawaban->tindakanKoreksi;
                    $dokumenAuditee = $tk?->dokumenAuditee ?? collect();
                    $dokumenDosen = $tk?->dokumenDosen ?? collect();
                    $dokumenDosenDisetujui = $dokumenDosen->where('status_validasi', 'diterima')->values();
                    $adaBuktiPelaksanaan = (bool) $tk?->bukti_file_path || $dokumenAuditee->isNotEmpty() || $dokumenDosenDisetujui->isNotEmpty();
                    $status = $tk?->status ?? 'belum_dibuat';
                    $kategori = $jawaban->rkaTemuan?->kategori_final ?: $jawaban->kategori_temuan;
                    $kondisi = $jawaban->rkaTemuan?->kondisi_final ?: $jawaban->catatan;
                    $ketuaAuditor = $penugasan->auditor1?->nama_lengkap ?? '-';
                    $anggotaAuditor = $penugasan->auditor2?->nama_lengkap ?? '-';
                    $itemPath = collect($jawaban->item_path ?? []);
                    $temuanItemId = $jawaban->upt_item_sub_standar_id;
                    $p4mpStatus = $tk?->p4mp_status;
                    $tkLocked = $tk && ($p4mpStatus === 'terverifikasi' || filled($tk->p4mp_verified_at));
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
                        !$periodeAktif => 'Periode ini tidak aktif. Data hanya dapat dilihat.',
                        !$rkaDitandatangani => 'Menunggu RKA ditandatangani Kepala P4MP.',
                        !$tk => 'Menunggu auditor membuat usulan tindakan koreksi.',
                        $p4mpStatus === 'perlu_perbaikan' => 'Unggah bukti perbaikan baru sesuai catatan P4MP.',
                        !$adaBuktiPelaksanaan => 'Isi pelaksanaan dan unggah bukti tindakan koreksi, atau setujui dokumen dosen yang sesuai.',
                        $status !== 'selesai' => 'Bukti sudah dikirim. Menunggu penilaian ulang auditor.',
                        !$p4mpStatus || $p4mpStatus === 'menunggu_verifikasi' => 'Menunggu verifikasi P4MP.',
                        $p4mpStatus === 'terverifikasi' => 'Tindakan koreksi sudah terverifikasi.',
                        default => 'Pantau catatan terbaru dari auditor atau P4MP.',
                    };
                    $steps = [
                        ['label' => 'Temuan', 'done' => true],
                        ['label' => 'Usulan', 'done' => (bool) $tk?->rencana_koreksi],
                        ['label' => 'Bukti', 'done' => $adaBuktiPelaksanaan],
                        ['label' => 'Dinilai', 'done' => $status === 'selesai'],
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
                                        {{ $p4mpStatus ? str_replace('_', ' ', ucfirst($p4mpStatus)) : 'Belum verifikasi P4MP' }}
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
                            <div class="rounded border border-blue-100 bg-blue-50 p-3 text-sm text-blue-800 lg:max-w-sm">
                                <p class="font-semibold">Yang perlu dilakukan</p>
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
                            <h3 class="text-sm font-semibold text-gray-900">Temuan Audit</h3>
                            <p class="mt-3 whitespace-pre-line rounded bg-gray-50 p-3 text-sm text-gray-700">{{ $kondisi ?: 'Belum ada kondisi final RKA.' }}</p>
                            <dl class="mt-3 grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs uppercase text-gray-500">Ketua Auditor</dt>
                                    <dd class="font-medium text-gray-800">{{ $ketuaAuditor }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase text-gray-500">Anggota Auditor</dt>
                                    <dd class="font-medium text-gray-800">{{ $anggotaAuditor }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="rounded border border-gray-200 p-4">
                            <h3 class="text-sm font-semibold text-gray-900">Status Terakhir</h3>
                            <p class="mt-3 text-xs font-semibold uppercase text-gray-500">Penilaian auditor</p>
                            <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk?->hasil_penilaian_auditor ?: '-' }}</p>
                            <p class="mt-3 text-xs font-semibold uppercase text-gray-500">Catatan P4MP</p>
                            <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk?->p4mp_catatan ?: '-' }}</p>
                        </section>
                    </div>

                    @if ($tk)
                        <div class="border-t border-gray-100 p-4 sm:p-5">
                            <details class="mb-4 rounded border border-gray-200 bg-gray-50 p-4">
                                <summary class="cursor-pointer text-sm font-semibold text-gray-900">Lihat detail analisis auditor</summary>
                                <p class="mt-3 text-xs font-semibold uppercase text-gray-500">Analisa Ketidaksesuaian</p>
                                <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->analisis_ketidaksesuaian ?: '-' }}</p>
                            </details>

                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                <section class="min-w-0 rounded border border-gray-200 bg-gray-50 p-4">
                                    <h3 class="text-sm font-semibold text-gray-900">Usulan dari Auditor</h3>
                                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">Tindakan Koreksi</p>
                                    <p class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-700">{{ $tk->rencana_koreksi ?: '-' }}</p>
                                </section>

                                <section class="min-w-0 rounded border border-blue-100 bg-blue-50 p-4">
                                    <h3 class="text-sm font-semibold text-blue-900">Pelaksanaan oleh Auditee</h3>
                                    <p class="mt-3 text-xs font-semibold uppercase text-blue-700">Bukti saat ini</p>
                                    @if ($dokumenAuditee->isNotEmpty())
                                        <div class="mt-2 space-y-2">
                                            @foreach ($dokumenAuditee as $dokumenAuditeeItem)
                                                <div class="flex flex-col gap-2 rounded border border-blue-100 bg-white p-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <p class="break-all text-sm font-semibold text-gray-800">{{ $dokumenAuditeeItem->nama_file }}</p>
                                                        @if ($dokumenAuditeeItem->keterangan)
                                                            <p class="mt-1 whitespace-pre-wrap break-words text-xs text-gray-600">{{ $dokumenAuditeeItem->keterangan }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                                                        <button type="button"
                                                            data-preview-url="{{ route('auditee.tindakan_koreksi.preview_bukti', $dokumenAuditeeItem->dokumen_tk_auditee_id) }}"
                                                            data-extension="{{ strtolower(pathinfo($dokumenAuditeeItem->nama_file, PATHINFO_EXTENSION)) }}"
                                                            data-file-name="{{ $dokumenAuditeeItem->nama_file }}"
                                                            class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">
                                                            <i class="bi bi-eye"></i>
                                                            Lihat
                                                        </button>
                                                        @if ($periodeAktif && $rkaDitandatangani && !$tkLocked)
                                                            <button type="button"
                                                                data-delete-doc-open
                                                                data-delete-action="{{ route('auditee.tindakan_koreksi.hapus_bukti', $dokumenAuditeeItem->dokumen_tk_auditee_id) }}"
                                                                data-delete-file="{{ $dokumenAuditeeItem->nama_file }}"
                                                                class="inline-flex items-center justify-center gap-2 rounded bg-red-600 px-3 py-2 text-xs font-medium text-white hover:bg-red-700">
                                                                <i class="bi bi-trash"></i>
                                                                Hapus
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($tk->bukti_file_path)
                                        <p class="mt-1 text-sm text-gray-700">{{ $tk->bukti_nama_file }}</p>
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            <button type="button"
                                                data-preview-url="{{ route('auditee.tindakan_koreksi.preview_bukti', $tk->tindakan_koreksi_id) }}"
                                                data-extension="{{ strtolower(pathinfo($tk->bukti_nama_file, PATHINFO_EXTENSION)) }}"
                                                data-file-name="{{ $tk->bukti_nama_file }}"
                                                class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                                <i class="bi bi-eye"></i>
                                                Lihat Bukti
                                            </button>
                                            @if ($periodeAktif && $rkaDitandatangani && !$tkLocked)
                                                <button type="button"
                                                    data-delete-doc-open
                                                    data-delete-action="{{ route('auditee.tindakan_koreksi.hapus_bukti', $tk->tindakan_koreksi_id) }}"
                                                    data-delete-file="{{ $tk->bukti_nama_file }}"
                                                    class="inline-flex items-center justify-center gap-2 rounded bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700">
                                                    <i class="bi bi-trash"></i>
                                                    Hapus Bukti
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <p class="mt-1 text-sm text-gray-700">Belum ada bukti.</p>
                                    @endif
                                    <p class="mt-4 text-xs font-semibold uppercase text-blue-700">Uraian saat ini</p>
                                    <p class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-700">{{ $tk->pelaksanaan_deskripsi ?: '-' }}</p>
                                </section>
                            </div>

                            @if (!$periodeAktif)
                                <div class="mt-4 rounded border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                                    Periode ini tidak aktif. Upload bukti pelaksanaan dan perubahan dokumen dosen sudah dikunci.
                                </div>
                            @elseif (!$rkaDitandatangani)
                                <div class="mt-4 rounded border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                                    Upload bukti pelaksanaan dan pengaturan dokumen dosen akan dibuka setelah RKA ditandatangani Kepala P4MP dan auditor menyusun tindakan koreksi.
                                </div>
                            @elseif ($tkLocked)
                                <div class="mt-4 rounded border border-green-100 bg-green-50 p-4 text-sm text-green-800">
                                    Tindakan koreksi sudah diverifikasi P4MP. Upload bukti dan perubahan dokumen pendukung sudah dikunci.
                                </div>
                            @else
                                <form action="{{ route('auditee.tindakan_koreksi.upload_bukti', $tk->tindakan_koreksi_id) }}" method="POST"
                                    data-scroll-target="tk-{{ $jawaban->id }}"
                                    enctype="multipart/form-data" class="mt-4 grid grid-cols-1 gap-3 rounded border border-green-100 bg-green-50 p-4 lg:grid-cols-2">
                                    @csrf
                                    <input type="hidden" name="submitted_tk_id" value="{{ $tk->tindakan_koreksi_id }}">
                                    <div class="lg:col-span-2">
                                        <h3 class="text-sm font-semibold text-green-900">Kirim Pelaksanaan dan Bukti</h3>
                                    </div>
                                    <div class="lg:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">Uraian Pelaksanaan</label>
                                        <textarea name="pelaksanaan_deskripsi" rows="3" class="mt-1 block w-full rounded border-gray-300 text-sm">{{ old('submitted_tk_id') === $tk->tindakan_koreksi_id ? old('pelaksanaan_deskripsi', $tk->pelaksanaan_deskripsi) : $tk->pelaksanaan_deskripsi }}</textarea>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">File Bukti</label>
                                        <div data-file-upload-field>
                                        <input type="file" name="bukti_koreksi[]" multiple required data-max-file-size="5242880"
                                            class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded file:border-0 file:bg-green-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-green-700 hover:file:bg-green-200">
                                        <p data-file-size-error class="mt-2 hidden text-sm font-medium text-red-600"></p>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">PDF, Word, Excel, JPG, JPEG, atau PNG. Maksimal 5 MB per file.</p>
                                        @php
                                            $buktiErrors = collect($errors->get('bukti_koreksi'))
                                                ->merge($errors->get('bukti_koreksi.*'))
                                                ->filter();
                                        @endphp
                                        @if ($buktiErrors->isNotEmpty())
                                            <div class="mt-2 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                                {{ $buktiErrors->first() }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit" class="inline-flex justify-center rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                            Kirim ke Auditor
                                        </button>
                                    </div>
                                </form>
                            @endif

                            <section class="mt-4 rounded border border-indigo-100 bg-indigo-50 p-4">
                                @php
                                    $butuhDokumenDosen = (bool) $tk->kebutuhanDokumenDosen;
                                @endphp
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-indigo-900">Dokumen Pendukung dari Dosen</h3>
                                        <p class="mt-1 text-sm text-indigo-800">
                                            Aktifkan jika tindakan koreksi ini membutuhkan dokumen dari dosen. Dosen hanya melihat tindakan yang diaktifkan di sini.
                                        </p>
                                    </div>
                                    @if (!$periodeAktif)
                                        <div class="flex shrink-0 items-center gap-2 rounded bg-white p-3 text-xs font-medium text-yellow-700">
                                            <i class="bi bi-lock"></i>
                                            Periode tidak aktif
                                        </div>
                                    @elseif (!$rkaDitandatangani)
                                        <div class="flex shrink-0 items-center gap-2 rounded bg-white p-3 text-xs font-medium text-yellow-700">
                                            <i class="bi bi-lock"></i>
                                            Menunggu TTD RKA
                                        </div>
                                    @elseif ($tkLocked)
                                        <div class="flex shrink-0 items-center gap-2 rounded bg-white p-3 text-xs font-medium text-green-700">
                                            <i class="bi bi-lock"></i>
                                            Dokumen dosen terkunci
                                        </div>
                                    @else
                                        <form action="{{ route('auditee.tindakan_koreksi.dokumen_dosen.atur', $tk->tindakan_koreksi_id) }}" method="POST"
                                            data-scroll-target="tk-{{ $jawaban->id }}"
                                            class="flex shrink-0 items-center gap-3 rounded bg-white p-3">
                                            @csrf
                                            @method('patch')
                                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                                <input type="checkbox" name="butuh_dokumen_dosen" value="1" @checked($butuhDokumenDosen)
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                Butuh dokumen dosen
                                            </label>
                                            <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                                Simpan
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <div class="mt-4 rounded bg-white p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <h4 class="text-sm font-semibold text-gray-900">Dokumen Dosen Masuk</h4>
                                        <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">{{ $dokumenDosen->count() }} dokumen</span>
                                    </div>

                                    @if ($dokumenDosen->isEmpty())
                                        <p class="mt-3 text-sm text-gray-500">Belum ada dokumen dari dosen untuk tindakan koreksi ini.</p>
                                    @else
                                        <div class="mt-3 space-y-3">
                                            @foreach ($dokumenDosen as $dokumen)
                                                @php
                                                    $statusClass = match ($dokumen->status_validasi) {
                                                        'diterima' => 'bg-green-100 text-green-700',
                                                        'ditolak' => 'bg-red-100 text-red-700',
                                                        default => 'bg-yellow-100 text-yellow-700',
                                                    };
                                                @endphp
                                                <div class="rounded border border-gray-200 p-3">
                                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                                        <div class="min-w-0">
                                                            <p class="break-all text-sm font-semibold text-gray-800">{{ $dokumen->nama_file }}</p>
                                                            <p class="mt-1 text-xs text-gray-500">Dosen: {{ $dokumen->dosen?->nama_lengkap ?? '-' }}</p>
                                                            @if ($dokumen->keterangan)
                                                                <p class="mt-1 whitespace-pre-line text-xs text-gray-600">{{ $dokumen->keterangan }}</p>
                                                            @endif
                                                            @if ($dokumen->catatan_validasi)
                                                                <p class="mt-1 whitespace-pre-line text-xs text-red-600">Catatan validasi: {{ $dokumen->catatan_validasi }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                                                            <span class="rounded px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                                                                {{ ucfirst($dokumen->status_validasi ?? 'menunggu') }}
                                                            </span>
                                                            <button type="button"
                                                                data-preview-url="{{ route('auditee.tindakan_koreksi.dokumen_dosen.preview', $dokumen->dokumen_tk_dosen_id) }}"
                                                                data-extension="{{ strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION)) }}"
                                                                data-file-name="{{ $dokumen->nama_file }}"
                                                                class="rounded bg-gray-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-gray-800">
                                                                Lihat
                                                            </button>
                                                        </div>
                                                    </div>

                                                    @if ($periodeAktif && !$tkLocked && $rkaDitandatangani)
                                                        <form action="{{ route('auditee.tindakan_koreksi.dokumen_dosen.validasi', $dokumen->dokumen_tk_dosen_id) }}" method="POST"
                                                            data-scroll-target="tk-{{ $jawaban->id }}"
                                                            class="mt-3 border-t border-gray-100 pt-3">
                                                            @csrf
                                                            @method('patch')
                                                            <div>
                                                                <label class="text-xs font-medium text-gray-600">Catatan validasi</label>
                                                                <textarea name="catatan_validasi" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm">{{ $dokumen->catatan_validasi }}</textarea>
                                                                <p class="mt-1 text-xs text-gray-500">Opsional. Isi terutama jika dokumen ditolak.</p>
                                                            </div>
                                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                                <button type="submit" name="status_validasi" value="diterima"
                                                                    class="rounded bg-green-600 px-3 py-2 text-xs font-medium text-white hover:bg-green-700">
                                                                    Setujui
                                                                </button>
                                                                <button type="submit" name="status_validasi" value="ditolak"
                                                                    class="rounded bg-red-600 px-3 py-2 text-xs font-medium text-white hover:bg-red-700">
                                                                    Tolak
                                                                </button>
                                                            </div>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </section>

                        </div>
                    @else
                        <div class="border-t border-gray-100 p-4 sm:p-5">
                            <div class="rounded border border-orange-100 bg-orange-50 p-3 text-sm text-orange-700">
                                Auditor belum merumuskan tindakan koreksi untuk temuan ini.
                            </div>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white p-10 text-center">
                    <i class="bi bi-check2-circle text-4xl text-green-400"></i>
                    <p class="mt-3 text-sm font-medium text-gray-500">Belum ada temuan yang membutuhkan tindakan koreksi.</p>
                </div>
            @endforelse
        </div>
    </div>

    @include('layouts.partials.smart-file-preview')
    @include('layouts.partials.back-to-top')

    <div id="modal-hapus-dokumen-tk" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 p-4">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative rounded-base border border-default bg-white p-4 shadow-sm md:p-6">
                <button type="button" data-delete-doc-close
                    class="absolute top-3 end-2.5 inline-flex h-9 w-9 items-center justify-center rounded-base bg-transparent text-body hover:bg-neutral-tertiary hover:text-heading">
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                        <span class="sr-only">Tutup modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg class="mx-auto mb-4 h-12 w-12 text-fg-disabled" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-3 text-body">Apakah anda yakin akan menghapus dokumen ini?</h3>
                    <p id="nama-dokumen-hapus-tk" class="mb-6 break-all text-sm font-semibold text-gray-700">-</p>
                    <form id="form-hapus-dokumen-tk" method="POST">
                    @csrf
                    @method('delete')
                    <div class="flex items-center justify-center space-x-4">
                        <button type="submit"
                            class="rounded-base border border-transparent bg-blue-500 px-4 py-2.5 text-sm font-medium leading-5 text-white shadow-xs transition duration-300 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-danger-medium">
                            Iya, saya yakin
                        </button>
                        <button type="button" data-delete-doc-close
                            class="rounded-base border border-default-medium bg-white px-4 py-2.5 text-sm font-medium leading-5 text-body shadow-xs transition duration-300 ease-in-out hover:bg-gray-200 hover:text-heading focus:outline-none focus:ring-4 focus:ring-neutral-tertiary">
                            Tidak, Batal
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const formatMb = (bytes) => (bytes / 1024 / 1024).toFixed(2).replace('.', ',');
                const deleteModal = document.getElementById('modal-hapus-dokumen-tk');
                const deleteForm = document.getElementById('form-hapus-dokumen-tk');
                const deleteFileName = document.getElementById('nama-dokumen-hapus-tk');

                const closeDeleteModal = () => {
                    deleteModal?.classList.add('hidden');
                    deleteModal?.classList.remove('flex');
                };

                document.querySelectorAll('[data-delete-doc-open]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (!deleteModal || !deleteForm || !deleteFileName) {
                            return;
                        }

                        deleteForm.action = button.dataset.deleteAction;
                        deleteFileName.textContent = button.dataset.deleteFile || '-';
                        deleteModal.classList.remove('hidden');
                        deleteModal.classList.add('flex');
                    });
                });

                document.querySelectorAll('[data-delete-doc-close]').forEach((button) => {
                    button.addEventListener('click', closeDeleteModal);
                });

                deleteModal?.addEventListener('click', (event) => {
                    if (event.target === deleteModal) {
                        closeDeleteModal();
                    }
                });

                document.querySelectorAll('[data-max-file-size]').forEach((input) => {
                    const field = input.closest('[data-file-upload-field]');
                    const errorElement = field?.querySelector('[data-file-size-error]');
                    const maxSize = Number(input.dataset.maxFileSize || 5242880);

                    const showError = (message) => {
                        if (errorElement) {
                            errorElement.textContent = message;
                            errorElement.classList.remove('hidden');
                        }
                        input.setCustomValidity(message);
                    };

                    const clearError = () => {
                        if (errorElement) {
                            errorElement.textContent = '';
                            errorElement.classList.add('hidden');
                        }
                        input.setCustomValidity('');
                    };

                    const validateFiles = () => {
                        const oversizedFile = Array.from(input.files || []).find((file) => file.size > maxSize);

                        if (oversizedFile) {
                            showError(`File "${oversizedFile.name}" berukuran ${formatMb(oversizedFile.size)} MB. Maksimal 5 MB per file.`);
                            return false;
                        }

                        clearError();
                        return true;
                    };

                    input.addEventListener('change', validateFiles);
                    input.form?.addEventListener('submit', (event) => {
                        if (!validateFiles()) {
                            event.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
