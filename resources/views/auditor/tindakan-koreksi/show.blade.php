<x-app-layout>
    @include('auditor.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Tindakan Koreksi</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $penugasan->upt?->nama_upt ?? '-' }} &middot; Periode {{ $penugasan->periode?->tahun ?? '-' }}
                        </p>
                        @unless ($isKetuaAuditor)
                            <p class="mt-3 rounded border border-blue-100 bg-blue-50 px-3 py-2 text-sm text-blue-700">
                                Mode lihat saja. Penyusunan dan penilaian ulang dilakukan oleh ketua auditor.
                            </p>
                        @endunless
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <a href="{{ route('auditor.tindakan_koreksi.export', $penugasan->penugasan_id) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                            <i class="bi bi-file-earmark-pdf"></i>
                            Export PDF
                        </a>
                        <a href="{{ route('auditor.tindakan_koreksi.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            @if ($carryForward->isNotEmpty())
                <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 shadow-sm sm:p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="font-semibold text-orange-800">Temuan Lintas Siklus</h2>
                            <p class="mt-1 text-sm text-orange-700">
                                {{ $carryForward->count() }} temuan dari siklus sebelumnya masih perlu dipantau pada AMI ini.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @forelse ($temuan as $index => $jawaban)
                @php
                    $tk = $jawaban->tindakanKoreksi;
                    $status = $tk?->status ?? 'belum_dibuat';
                    $kategori = $jawaban->rkaTemuan?->kategori_final ?: $jawaban->kategori_temuan;
                    $kondisi = $jawaban->rkaTemuan?->kondisi_final ?: $jawaban->catatan;
                    $ketuaAuditor = $penugasan->auditor1?->nama_lengkap ?? '-';
                    $anggotaAuditor = $penugasan->auditor2?->nama_lengkap ?? '-';
                    $auditeeName = $penugasan->upt?->nama_upt ?? '-';
                    $itemPath = collect($jawaban->item_path ?? []);
                    $temuanItemId = $jawaban->upt_item_sub_standar_id;
                    $p4mpStatus = $tk?->p4mp_status;
                    $currentStep = !$tk ? 2 : ($status === 'selesai' ? 5 : ($tk->bukti_file_path ? 4 : 4));
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
                        !$tk && $isKetuaAuditor => 'Isi analisis dan usulan tindakan koreksi.',
                        !$tk => 'Menunggu ketua auditor menyusun tindakan koreksi.',
                        !$tk->bukti_file_path => 'Menunggu auditee mengunggah bukti pelaksanaan.',
                        $status !== 'selesai' && $isKetuaAuditor => 'Isi hasil penilaian ulang auditor.',
                        $status !== 'selesai' => 'Menunggu ketua auditor menilai ulang bukti.',
                        !$p4mpStatus || $p4mpStatus === 'menunggu_verifikasi' => 'Menunggu verifikasi P4MP.',
                        $p4mpStatus === 'perlu_perbaikan' => 'P4MP meminta perbaikan. Tunggu auditee mengunggah bukti baru.',
                        default => 'Tindakan koreksi sudah terverifikasi.',
                    };
                    $steps = [
                        ['label' => 'Temuan', 'done' => true],
                        ['label' => 'Analisis', 'done' => (bool) $tk?->analisis_ketidaksesuaian],
                        ['label' => 'Usulan', 'done' => (bool) $tk?->rencana_koreksi],
                        ['label' => 'Pelaksanaan', 'done' => (bool) $tk?->bukti_file_path],
                        ['label' => 'Verifikasi', 'done' => $p4mpStatus === 'terverifikasi'],
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

                    <div class="grid grid-cols-1 gap-4 p-4 sm:p-5">
                        <section class="rounded border border-gray-200 p-4">
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

                    </div>

                    @if ($isKetuaAuditor)
                        <div class="border-t border-gray-100 p-4 sm:p-5">
                            <form action="{{ route('auditor.tindakan_koreksi.rumuskan', [$penugasan->penugasan_id, $jawaban->id]) }}" method="POST"
                                data-scroll-target="tk-{{ $jawaban->id }}"
                                class="grid grid-cols-1 gap-4 rounded border border-blue-100 bg-blue-50 p-4 lg:grid-cols-2">
                                @csrf
                                <div class="lg:col-span-2">
                                    <h3 class="text-sm font-semibold text-blue-900">{{ $tk ? 'Perbarui Analisis dan Usulan' : 'Buat Analisis dan Usulan' }}</h3>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="text-sm font-medium text-gray-700">Analisa Ketidaksesuaian</label>
                                    <textarea name="analisis_ketidaksesuaian" rows="3" required class="mt-1 block w-full rounded border-gray-300 text-sm">{{ old('analisis_ketidaksesuaian', $tk?->analisis_ketidaksesuaian) }}</textarea>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="text-sm font-medium text-gray-700">Usulan Tindakan Koreksi</label>
                                    <textarea name="rencana_koreksi" rows="3" required class="mt-1 block w-full rounded border-gray-300 text-sm">{{ old('rencana_koreksi', $tk?->rencana_koreksi) }}</textarea>
                                </div>
                                <div class="lg:col-span-2">
                                    <button type="submit" class="inline-flex justify-center rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                        Simpan Analisis dan Usulan
                                    </button>
                                </div>
                            </form>

                            @if ($tk)
                                <form action="{{ route('auditor.tindakan_koreksi.verifikasi', $tk->tindakan_koreksi_id) }}" method="POST"
                                    data-scroll-target="tk-{{ $jawaban->id }}"
                                    class="mt-4 rounded border border-gray-200 bg-gray-50 p-4">
                                    @csrf
                                    @method('patch')
                                    <h3 class="text-sm font-semibold text-gray-900">Penilaian Ulang Bukti Auditee</h3>

                                    <div class="mt-3 rounded border border-gray-200 bg-white p-3">
                                        <p class="text-xs font-semibold uppercase text-gray-500">Bukti Auditee</p>
                                        <p class="mt-1 text-sm text-gray-700">{{ $tk->bukti_nama_file ?: 'Belum ada bukti yang diunggah.' }}</p>
                                        @if ($tk->bukti_file_path)
                                            <button type="button"
                                                data-preview-url="{{ route('auditor.tindakan_koreksi.preview_bukti', $tk->tindakan_koreksi_id) }}"
                                                data-extension="{{ strtolower(pathinfo($tk->bukti_nama_file, PATHINFO_EXTENSION)) }}"
                                                data-file-name="{{ $tk->bukti_nama_file }}"
                                                class="mt-3 inline-flex items-center justify-center gap-2 rounded bg-gray-700 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                                <i class="bi bi-eye"></i>
                                                Lihat Bukti
                                            </button>
                                        @endif
                                        <p class="mt-4 text-xs font-semibold uppercase text-gray-500">Uraian Pelaksanaan Auditee</p>
                                        <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->pelaksanaan_deskripsi ?: 'Belum ada uraian pelaksanaan dari auditee.' }}</p>
                                    </div>

                                    <label class="mt-3 block text-sm font-medium text-gray-700">Hasil Penilaian Ulang</label>
                                    <textarea name="hasil_penilaian_auditor" rows="3" class="mt-1 block w-full rounded border-gray-300 text-sm">{{ old('hasil_penilaian_auditor', $tk->hasil_penilaian_auditor) }}</textarea>

                                    <label class="mt-3 block text-sm font-medium text-gray-700">Catatan Auditor</label>
                                    <textarea name="catatan_auditor" rows="3" class="mt-1 block w-full rounded border-gray-300 text-sm">{{ old('catatan_auditor', $tk->catatan_auditor) }}</textarea>

                                    <input type="hidden" name="status" value="selesai">

                                    <div class="mt-4">
                                        <button type="submit"
                                            class="rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                            Simpan Penilaian Auditor
                                        </button>
                                    </div>
                                    @if (!$tk->bukti_file_path)
                                        <p class="mt-2 text-xs text-gray-500">Penilaian bisa disimpan setelah auditee mengunggah bukti.</p>
                                    @endif
                                </form>
                            @endif
                        </div>
                    @elseif ($tk)
                        <div class="border-t border-gray-100 p-4 sm:p-5">
                            <details class="rounded border border-gray-200 bg-gray-50 p-4">
                                <summary class="cursor-pointer text-sm font-semibold text-gray-900">Lihat detail analisis, usulan, dan pelaksanaan</summary>
                                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                                    <div>
                                        <p class="text-xs font-semibold uppercase text-gray-500">Analisa Ketidaksesuaian</p>
                                        <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->analisis_ketidaksesuaian ?: '-' }}</p>
                                    </div>
                                    <div class="lg:col-span-2">
                                        <p class="text-xs font-semibold uppercase text-gray-500">Usulan Tindakan Koreksi</p>
                                        <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->rencana_koreksi ?: '-' }}</p>
                                    </div>
                                    <div class="rounded border border-blue-100 bg-blue-50 p-3 lg:col-span-2">
                                        <p class="text-xs font-semibold uppercase text-blue-700">Uraian Pelaksanaan Auditee</p>
                                        <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $tk->pelaksanaan_deskripsi ?: 'Belum ada uraian pelaksanaan dari auditee.' }}</p>
                                        <p class="mt-4 text-xs font-semibold uppercase text-blue-700">Bukti Auditee</p>
                                        <p class="mt-1 text-sm text-gray-700">{{ $tk->bukti_nama_file ?: 'Belum ada bukti yang diunggah.' }}</p>
                                        @if ($tk->bukti_file_path)
                                            <button type="button"
                                                data-preview-url="{{ route('auditor.tindakan_koreksi.preview_bukti', $tk->tindakan_koreksi_id) }}"
                                                data-extension="{{ strtolower(pathinfo($tk->bukti_nama_file, PATHINFO_EXTENSION)) }}"
                                                data-file-name="{{ $tk->bukti_nama_file }}"
                                                class="mt-3 inline-flex items-center justify-center gap-2 rounded bg-gray-700 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                                                <i class="bi bi-eye"></i>
                                                Lihat Bukti
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </details>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white p-10 text-center">
                    <i class="bi bi-check2-circle text-4xl text-green-400"></i>
                    <p class="mt-3 text-sm font-medium text-gray-500">Belum ada temuan audit.</p>
                </div>
            @endforelse
        </div>
    </div>

    @include('layouts.partials.smart-file-preview')
    @include('layouts.partials.back-to-top')
</x-app-layout>
