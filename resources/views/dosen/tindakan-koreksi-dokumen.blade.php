<x-app-layout>
    @include('dosen.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded-lg bg-red-100 p-4 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Dokumen Tindakan Koreksi</h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Upload dokumen pendukung untuk tindakan koreksi yang sudah dibuka oleh auditee.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-gray-500">Dosen</p>
                            <p class="font-semibold text-gray-800">{{ $dosen?->nama_lengkap ?? '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-gray-500">Unit/Prodi</p>
                            <p class="font-semibold text-gray-800">{{ $nama_unit }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-gray-500">Periode</p>
                            <p class="font-semibold text-gray-800">{{ $periode_now?->tahun ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if (!$upt || !$periode_now || !$penugasan)
                <div class="rounded-lg bg-yellow-50 p-4 text-sm text-yellow-800">
                    Belum ada periode aktif atau penugasan AMI untuk unit Anda.
                </div>
            @elseif ($tindakanKoreksi->isEmpty())
                <div class="rounded-lg bg-yellow-50 p-4 text-sm text-yellow-800">
                    Belum ada tindakan koreksi yang dibuka auditee untuk upload dokumen dosen.
                </div>
            @else
                @foreach ($tindakanKoreksi as $tk)
                    @php
                        $dokumenList = $dokumenSaya[$tk->tindakan_koreksi_id] ?? collect();
                        $standar = $tk->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->nama_standar_mutu ?? '-';
                        $item = $tk->jawabanAudit?->itemSubStandar?->nama_item ?? '-';
                        $tkLocked = $tk->p4mp_status === 'terverifikasi' || filled($tk->p4mp_verified_at);
                    @endphp

                    <article id="tk-{{ $tk->tindakan_koreksi_id }}" class="scroll-mt-24 rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 p-4 sm:p-5">
                            <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">{{ $standar }}</span>
                                        <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">{{ $dokumenList->count() }} dokumen saya</span>
                                        @if ($tkLocked)
                                            <span class="rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Terverifikasi P4MP</span>
                                        @endif
                                    </div>
                                    <h2 class="mt-3 text-base font-semibold text-gray-900">{{ $item }}</h2>
                                    <p class="mt-2 whitespace-pre-line rounded bg-gray-50 p-3 text-sm text-gray-700">
                                        {{ $tk->rencana_koreksi ?: 'Belum ada usulan tindakan koreksi.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 lg:grid-cols-2">
                            <section class="rounded border border-gray-200 bg-gray-50 p-4">
                                <h3 class="text-sm font-semibold text-gray-900">Dokumen Saya</h3>
                                @if ($dokumenList->isEmpty())
                                    <p class="mt-3 text-sm text-gray-500">Belum ada dokumen yang Anda upload untuk tindakan koreksi ini.</p>
                                @else
                                    <div class="mt-3 space-y-3">
                                        @foreach ($dokumenList as $dokumen)
                                            @php
                                                $statusClass = match ($dokumen->status_validasi) {
                                                    'diterima' => 'bg-green-100 text-green-700',
                                                    'ditolak' => 'bg-red-100 text-red-700',
                                                    default => 'bg-yellow-100 text-yellow-700',
                                                };
                                            @endphp
                                            <div class="rounded border bg-white p-3">
                                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <p class="break-all text-sm font-medium text-gray-800">{{ $dokumen->nama_file }}</p>
                                                        @if ($dokumen->keterangan)
                                                            <p class="mt-1 whitespace-pre-line text-xs text-gray-500">{{ $dokumen->keterangan }}</p>
                                                        @endif
                                                        @if ($dokumen->catatan_validasi)
                                                            <p class="mt-1 whitespace-pre-line text-xs text-red-600">Catatan auditee: {{ $dokumen->catatan_validasi }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                                                        <span class="rounded px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                                                            {{ ucfirst($dokumen->status_validasi ?? 'menunggu') }}
                                                        </span>
                                                        <button type="button"
                                                            data-preview-url="{{ route('dosen.tindakan_koreksi_dokumen.preview', $dokumen->dokumen_tk_dosen_id) }}"
                                                            data-extension="{{ strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION)) }}"
                                                            data-file-name="{{ $dokumen->nama_file }}"
                                                            class="rounded bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">
                                                            Lihat
                                                        </button>
                                                        @if (!$tkLocked && $dokumen->status_validasi !== 'diterima')
                                                            <form action="{{ route('dosen.tindakan_koreksi_dokumen.hapus', $dokumen->dokumen_tk_dosen_id) }}" method="POST"
                                                                data-scroll-target="tk-{{ $tk->tindakan_koreksi_id }}">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="rounded bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </section>

                            <section class="rounded border border-green-100 bg-green-50 p-4">
                                @if ($tkLocked)
                                    <h3 class="text-sm font-semibold text-green-900">Dokumen Terkunci</h3>
                                    <p class="mt-2 text-sm text-green-800">
                                        Tindakan koreksi sudah diverifikasi P4MP. Upload dokumen baru sudah ditutup.
                                    </p>
                                @else
                                    <h3 class="text-sm font-semibold text-green-900">Upload Dokumen Baru</h3>
                                    <form action="{{ route('dosen.tindakan_koreksi_dokumen.upload', $tk->tindakan_koreksi_id) }}" method="POST"
                                        enctype="multipart/form-data"
                                        data-scroll-target="tk-{{ $tk->tindakan_koreksi_id }}"
                                        class="mt-3 space-y-3">
                                        @csrf
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">File Dokumen</label>
                                            <input type="file" name="file_bukti[]" multiple required
                                                class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded file:border-0 file:bg-green-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-green-700 hover:file:bg-green-200">
                                            <p class="mt-2 text-xs text-gray-500">PDF, Word, Excel, JPG, JPEG, atau PNG. Maksimal 5 MB per file.</p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Keterangan</label>
                                            <textarea name="keterangan" rows="3"
                                                class="mt-1 block w-full rounded border-gray-300 text-sm"
                                                placeholder="Contoh: dokumen pendukung pelaksanaan tindakan koreksi..."></textarea>
                                        </div>
                                        <button type="submit" class="rounded bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                                            Upload Dokumen
                                        </button>
                                    </form>
                                @endif
                            </section>
                        </div>
                    </article>
                @endforeach
            @endif
        </div>
    </div>

    @include('layouts.partials.smart-file-preview')
    @include('layouts.partials.back-to-top')
</x-app-layout>
