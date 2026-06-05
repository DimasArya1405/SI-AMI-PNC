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

            @forelse ($temuan as $index => $jawaban)
                @php
                    $tk = $jawaban->tindakanKoreksi;
                    $status = $tk?->status ?? 'belum_dibuat';
                    $kategori = $jawaban->rkaTemuan?->kategori_final ?: $jawaban->kategori_temuan;
                    $kondisi = $jawaban->rkaTemuan?->kondisi_final ?: $jawaban->catatan;
                    $ketuaAuditor = $penugasan->auditor1?->nama_lengkap ?? '-';
                    $anggotaAuditor = $penugasan->auditor2?->nama_lengkap ?? '-';
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
                        !$tk => 'Menunggu auditor membuat usulan tindakan koreksi.',
                        $p4mpStatus === 'perlu_perbaikan' => 'Unggah bukti perbaikan baru sesuai catatan P4MP.',
                        !$tk->bukti_file_path => 'Isi pelaksanaan dan unggah bukti tindakan koreksi.',
                        $status !== 'selesai' => 'Bukti sudah dikirim. Menunggu penilaian ulang auditor.',
                        !$p4mpStatus || $p4mpStatus === 'menunggu_verifikasi' => 'Menunggu verifikasi P4MP.',
                        $p4mpStatus === 'terverifikasi' => 'Tindakan koreksi sudah terverifikasi.',
                        default => 'Pantau catatan terbaru dari auditor atau P4MP.',
                    };
                    $steps = [
                        ['label' => 'Temuan', 'done' => true],
                        ['label' => 'Usulan', 'done' => (bool) $tk?->rencana_koreksi],
                        ['label' => 'Bukti', 'done' => (bool) $tk?->bukti_file_path],
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
                                <p class="mt-1 text-sm font-medium text-gray-700">{{ $jawaban->itemSubStandar?->nama_item ?? '-' }}</p>
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
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                <section class="rounded border border-gray-200 bg-gray-50 p-4">
                                    <h3 class="text-sm font-semibold text-gray-900">Usulan dari Auditor</h3>
                                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">Tindakan Koreksi</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->rencana_koreksi ?: '-' }}</p>
                                </section>

                                <section class="rounded border border-blue-100 bg-blue-50 p-4">
                                    <h3 class="text-sm font-semibold text-blue-900">Pelaksanaan oleh Auditee</h3>
                                    <p class="mt-3 text-xs font-semibold uppercase text-blue-700">Bukti saat ini</p>
                                    <p class="mt-1 text-sm text-gray-700">{{ $tk->bukti_nama_file ?: 'Belum ada bukti.' }}</p>
                                    @if ($tk->bukti_file_path)
                                        <button type="button"
                                            data-preview-url="{{ route('auditee.tindakan_koreksi.preview_bukti', $tk->tindakan_koreksi_id) }}"
                                            data-extension="{{ strtolower(pathinfo($tk->bukti_nama_file, PATHINFO_EXTENSION)) }}"
                                            data-file-name="{{ $tk->bukti_nama_file }}"
                                            class="mt-3 inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                            <i class="bi bi-eye"></i>
                                            Lihat Bukti
                                        </button>
                                    @endif
                                    <p class="mt-4 text-xs font-semibold uppercase text-blue-700">Uraian saat ini</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->pelaksanaan_deskripsi ?: '-' }}</p>
                                </section>
                            </div>

                            @if ($tkLocked)
                                <div class="mt-4 rounded border border-green-100 bg-green-50 p-4 text-sm text-green-800">
                                    Tindakan koreksi sudah diverifikasi P4MP. Upload bukti dan perubahan dokumen pendukung sudah dikunci.
                                </div>
                            @else
                                <form action="{{ route('auditee.tindakan_koreksi.upload_bukti', $tk->tindakan_koreksi_id) }}" method="POST"
                                    data-scroll-target="tk-{{ $jawaban->id }}"
                                    enctype="multipart/form-data" class="mt-4 grid grid-cols-1 gap-3 rounded border border-green-100 bg-green-50 p-4 lg:grid-cols-2">
                                    @csrf
                                    <div class="lg:col-span-2">
                                        <h3 class="text-sm font-semibold text-green-900">Kirim Pelaksanaan dan Bukti</h3>
                                    </div>
                                    <div class="lg:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">Uraian Pelaksanaan</label>
                                        <textarea name="pelaksanaan_deskripsi" rows="3" class="mt-1 block w-full rounded border-gray-300 text-sm">{{ old('pelaksanaan_deskripsi', $tk->pelaksanaan_deskripsi) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">File Bukti</label>
                                        <input type="file" name="bukti_koreksi" required
                                            class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded file:border-0 file:bg-green-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-green-700 hover:file:bg-green-200">
                                        <p class="mt-2 text-xs text-gray-500">PDF, Word, Excel, JPG, JPEG, atau PNG. Maksimal 5 MB.</p>
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
                                    $dokumenDosen = $tk->dokumenDosen ?? collect();
                                    $butuhDokumenDosen = (bool) $tk->kebutuhanDokumenDosen;
                                @endphp
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-indigo-900">Dokumen Pendukung dari Dosen</h3>
                                        <p class="mt-1 text-sm text-indigo-800">
                                            Aktifkan jika tindakan koreksi ini membutuhkan dokumen dari dosen. Dosen hanya melihat tindakan yang diaktifkan di sini.
                                        </p>
                                    </div>
                                    @if ($tkLocked)
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

                                                    @if (!$tkLocked)
                                                        <form action="{{ route('auditee.tindakan_koreksi.dokumen_dosen.validasi', $dokumen->dokumen_tk_dosen_id) }}" method="POST"
                                                            data-scroll-target="tk-{{ $jawaban->id }}"
                                                            class="mt-3 grid grid-cols-1 gap-3 border-t border-gray-100 pt-3 lg:grid-cols-3">
                                                            @csrf
                                                            @method('patch')
                                                            <div>
                                                                <label class="text-xs font-medium text-gray-600">Status Validasi</label>
                                                                <select name="status_validasi" required class="mt-1 block w-full rounded border-gray-300 text-sm">
                                                                    <option value="diterima" @selected($dokumen->status_validasi === 'diterima')>Diterima</option>
                                                                    <option value="ditolak" @selected($dokumen->status_validasi === 'ditolak')>Ditolak</option>
                                                                </select>
                                                            </div>
                                                            <div class="lg:col-span-2">
                                                                <label class="text-xs font-medium text-gray-600">Catatan</label>
                                                                <textarea name="catatan_validasi" rows="2" class="mt-1 block w-full rounded border-gray-300 text-sm">{{ $dokumen->catatan_validasi }}</textarea>
                                                            </div>
                                                            <div class="lg:col-span-3">
                                                                <button type="submit" class="rounded bg-indigo-600 px-3 py-2 text-xs font-medium text-white hover:bg-indigo-700">
                                                                    Simpan Validasi Dokumen
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

                            <details class="mt-4 rounded border border-gray-200 bg-gray-50 p-4">
                                <summary class="cursor-pointer text-sm font-semibold text-gray-900">Lihat detail analisis auditor</summary>
                                <p class="mt-3 text-xs font-semibold uppercase text-gray-500">Analisa Ketidaksesuaian</p>
                                <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->analisis_ketidaksesuaian ?: '-' }}</p>
                            </details>
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
</x-app-layout>
