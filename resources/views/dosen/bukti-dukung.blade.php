<x-app-layout>
    @include('dosen.sidebar')

    <div class="ml-60 min-h-screen bg-gray-100">
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            @php
                $rka = $penugasan?->relationLoaded('rka') ? $penugasan?->rka : $penugasan?->rka()->first();
                $rkaFinal = $rka && ($rka->status === 'final' || filled($rka->finalized_at));
            @endphp

            @if ($errors->any())
                <div class="rounded-lg bg-red-100 p-4 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Upload Dokumen Pendukung AMI</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Unggah bukti pendukung sesuai item standar. Dokumen akan masuk ke auditee untuk divalidasi.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
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
                <div class="p-4 text-sm text-yellow-800 bg-yellow-50 rounded-lg">
                    Belum ada periode aktif atau penugasan AMI untuk unit Anda.
                </div>
            @elseif ($pemetaanStandar->count() < 1)
                <div class="p-4 text-sm text-yellow-800 bg-yellow-50 rounded-lg">
                    Belum ada item AMI yang dibuka untuk dosen pada periode aktif.
                </div>
            @elseif ($assignedItemIds->isEmpty())
                <div class="p-4 text-sm text-yellow-800 bg-yellow-50 rounded-lg">
                    Auditee belum memilih item pertanyaan yang bisa diisi oleh dosen.
                </div>
            @else
                @if ($rkaFinal)
                    <div class="rounded-lg border border-green-100 bg-green-50 p-4 text-sm text-green-800">
                        RKA sudah difinalisasi. Dokumen AMI yang sudah masuk tetap bisa dilihat, tetapi upload dan hapus dokumen sudah dikunci.
                    </div>
                @endif

                <div class="bg-white rounded-lg border p-6">
                    <div class="mb-4 border-b border-gray-200 overflow-x-auto">
                        <ul class="flex -mb-px text-sm font-medium text-center"
                            id="dosen-standar-tab"
                            data-tabs-toggle="#dosen-standar-tab-content"
                            data-tabs-active-classes="text-blue-600 border-blue-600"
                            data-tabs-inactive-classes="text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300"
                            role="tablist">
                            @foreach ($pemetaanStandar as $index => $standar)
                                <li class="me-2" role="presentation">
                                    <button
                                        onclick="localStorage.setItem('activeTabDosenBukti', 'content-{{ $standar->standar_mutu_id }}')"
                                        class="inline-block p-4 border-b-2 rounded-t-lg whitespace-nowrap"
                                        id="tab-{{ $standar->standar_mutu_id }}"
                                        data-tabs-target="#content-{{ $standar->standar_mutu_id }}"
                                        type="button"
                                        role="tab"
                                        aria-controls="content-{{ $standar->standar_mutu_id }}"
                                        aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                        {{ $standar->standar_mutu->nama_standar_mutu ?? '-' }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div id="dosen-standar-tab-content">
                        @foreach ($pemetaanStandar as $index => $standar)
                            @php
                                $subStandarList = $uptSubStandar
                                    ->where('upt_standar_mutu_id', $standar->upt_standar_mutu_id)
                                    ->sortBy('urutan');
                            @endphp

                            <div
                                id="content-{{ $standar->standar_mutu_id }}"
                                class="{{ $index == 0 ? '' : 'hidden' }} rounded-lg bg-gray-50 p-4"
                                role="tabpanel"
                                aria-labelledby="tab-{{ $standar->standar_mutu_id }}">

                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $standar->standar_mutu->nama_standar_mutu ?? '-' }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Pilih item yang sesuai dengan dokumen Anda. Auditee akan menilai kelayakan bukti sebelum dipakai oleh auditor.
                                    </p>
                                </div>

                                <div id="accordion-dosen-{{ $standar->standar_mutu_id }}"
                                    data-accordion="collapse"
                                    class="space-y-4">
                                    @forelse ($subStandarList as $sub)
                                        @php
                                            $items = ($uptItemSubStandar[$sub->upt_sub_standar_id] ?? collect())
                                                ->sortBy([['urutan', 'asc'], ['created_at', 'asc']]);
                                            $headingId = 'heading-dosen-' . $sub->upt_sub_standar_id;
                                            $bodyId = 'body-dosen-' . $sub->upt_sub_standar_id;
                                        @endphp

                                        <div id="sub-{{ $sub->upt_sub_standar_id }}" class="border rounded-xl overflow-hidden bg-white">
                                            <h2 id="{{ $headingId }}">
                                                <button type="button"
                                                    class="flex items-center justify-between w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-left"
                                                    data-accordion-target="#{{ $bodyId }}"
                                                    aria-expanded="false"
                                                    aria-controls="{{ $bodyId }}">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-800">{{ $sub->nama_sub_standar }}</h4>
                                                        <p class="text-xs text-gray-500 mt-1">Total item: {{ $items->count() }}</p>
                                                    </div>
                                                    <i data-accordion-icon class="bi bi-chevron-down transition-transform"></i>
                                                </button>
                                            </h2>

                                            <div id="{{ $bodyId }}" class="hidden" aria-labelledby="{{ $headingId }}">
                                                <div class="p-4">
                                                    @php $nomorLevel1 = 0; @endphp

                                                    @forelse ($items as $item)
                                                        @php
                                                            $level = $item->level ?? 1;
                                                            $levelClass = match ($level) {
                                                                1 => '',
                                                                2 => 'ml-6',
                                                                3 => 'ml-12',
                                                                4 => 'ml-16',
                                                                default => 'ml-20',
                                                            };
                                                            $buktiList = $buktiDukung[$item->upt_item_sub_standar_id] ?? collect();
                                                        @endphp

                                                        <div id="item-{{ $item->upt_item_sub_standar_id }}"
                                                            class="mb-4 rounded-lg border bg-white p-4 {{ $levelClass }}">
                                                            <div class="flex items-start gap-3">
                                                                <div class="text-sm font-semibold text-gray-500 min-w-[28px]">
                                                                    @if ($level == 1)
                                                                        @php $nomorLevel1++; @endphp
                                                                        {{ $nomorLevel1 }}.
                                                                    @else
                                                                        &rarr;
                                                                    @endif
                                                                </div>

                                                                <div class="flex-1">
                                                                    <p class="text-md font-medium text-gray-800">{{ $item->nama_item }}</p>

                                                                    <div class="mt-4 rounded-lg border bg-gray-50 p-4">
                                                                        <div class="flex items-center justify-between mb-3">
                                                                            <h5 class="text-sm font-semibold text-gray-700">Dokumen Saya</h5>
                                                                            <span class="text-xs px-2 py-1 rounded-full {{ $buktiList->count() > 0 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                                                {{ $buktiList->count() > 0 ? $buktiList->count() . ' file' : 'Belum ada' }}
                                                                            </span>
                                                                        </div>

                                                                        @if ($buktiList->count() > 0)
                                                                            <div class="space-y-2 mb-4">
                                                                                @foreach ($buktiList as $bukti)
                                                                                    @php
                                                                                        $statusClass = match ($bukti->status_validasi) {
                                                                                            'diterima' => 'bg-green-100 text-green-700',
                                                                                            'ditolak' => 'bg-red-100 text-red-700',
                                                                                            default => 'bg-yellow-100 text-yellow-700',
                                                                                        };
                                                                                    @endphp
                                                                                    <div class="bg-white border rounded px-3 py-2">
                                                                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                                                                            <div class="min-w-0">
                                                                                                <p class="text-sm font-medium text-gray-800 break-all">{{ $bukti->nama_file }}</p>
                                                                                                @if ($bukti->keterangan)
                                                                                                    <p class="text-xs text-gray-500 mt-1">{{ $bukti->keterangan }}</p>
                                                                                                @endif
                                                                                                @if ($bukti->catatan_validasi)
                                                                                                    <p class="text-xs text-red-600 mt-1">Catatan auditee: {{ $bukti->catatan_validasi }}</p>
                                                                                                @endif
                                                                                            </div>
                                                                                            <div class="flex flex-wrap items-center gap-2">
                                                                                                <span class="text-xs px-2 py-1 rounded-full {{ $statusClass }}">
                                                                                                    {{ ucfirst($bukti->status_validasi ?? 'menunggu') }}
                                                                                                </span>
                                                                                                <button type="button"
                                                                                                    onclick="openSmartPreview(
                                                                                                        '{{ route('dosen.bukti_dukung.preview', $bukti->jawaban_id) }}',
                                                                                                        '{{ route('dosen.bukti_dukung.download', $bukti->jawaban_id) }}',
                                                                                                        '{{ strtolower(pathinfo($bukti->nama_file, PATHINFO_EXTENSION)) }}',
                                                                                                        '{{ $bukti->nama_file }}'
                                                                                                    )"
                                                                                                    class="text-sm px-3 py-1 bg-blue-500 hover:bg-blue-700 text-white rounded">
                                                                                                    Lihat
                                                                                                </button>
                                                                                                @if (!$rkaFinal && $bukti->status_validasi !== 'diterima')
                                                                                                    <form action="{{ route('dosen.bukti_dukung.hapus', $bukti->jawaban_id) }}" method="POST">
                                                                                                        @csrf
                                                                                                        @method('delete')
                                                                                                        <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                                                                                        <input type="hidden" name="open_accordion" value="{{ $bodyId }}">
                                                                                                        <input type="hidden" name="target_scroll" value="item-{{ $item->upt_item_sub_standar_id }}">
                                                                                                        <button type="submit" class="text-sm px-3 py-1 bg-red-500 hover:bg-red-700 text-white rounded">
                                                                                                            Hapus
                                                                                                        </button>
                                                                                                    </form>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <p class="text-xs text-gray-500 mb-4">Belum ada dokumen yang Anda upload untuk item ini.</p>
                                                                        @endif

                                                                        @if (!$rkaFinal)
                                                                        <form action="{{ route('dosen.bukti_dukung.upload') }}"
                                                                            method="POST"
                                                                            enctype="multipart/form-data"
                                                                            class="formUploadBukti space-y-3">
                                                                            @csrf
                                                                            <input type="hidden" name="upt_item_sub_standar_id" value="{{ $item->upt_item_sub_standar_id }}">
                                                                            <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                                                            <input type="hidden" name="open_accordion" value="{{ $bodyId }}">
                                                                            <input type="hidden" name="target_scroll" value="item-{{ $item->upt_item_sub_standar_id }}">

                                                                            <input type="file"
                                                                                name="file_bukti[]"
                                                                                multiple
                                                                                data-max-file-size="5242880"
                                                                                data-allowed-extensions="pdf,doc,docx,xls,xlsx,jpg,jpeg,png"
                                                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                                                                class="block w-full text-sm border rounded-lg cursor-pointer bg-white"
                                                                                required>
                                                                            <p data-file-size-error class="mt-2 hidden text-sm font-medium text-red-600"></p>
                                                                            <p class="mt-2 text-xs text-gray-500">
                                                                                PDF, Word, Excel, JPG, JPEG, atau PNG. Maksimal 5 MB per file.
                                                                            </p>

                                                                            <textarea
                                                                                name="keterangan"
                                                                                rows="2"
                                                                                placeholder="Keterangan dokumen, contoh: Publikasi jurnal 2025 sebagai anggota penelitian..."
                                                                                class="w-full border border-gray-300 rounded-lg text-sm p-3 focus:ring-blue-500 focus:border-blue-500"></textarea>

                                                                            <button type="submit"
                                                                                class="btnUpload bg-green-500 hover:bg-green-700 text-white text-sm px-4 py-2 rounded flex items-center gap-2">
                                                                                <span class="textUpload">Upload Dokumen</span>
                                                                                <svg class="spinnerUpload hidden animate-spin h-4 w-4 text-white"
                                                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </form>
                                                                        @else
                                                                            <p class="rounded border border-green-100 bg-green-50 p-3 text-xs text-green-700">
                                                                                RKA final. Upload dokumen untuk item ini sudah dikunci.
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="bg-yellow-50 text-yellow-800 text-sm rounded-lg p-4">
                                                            Belum ada item pada sub standar ini.
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="bg-yellow-50 text-yellow-800 text-sm rounded-lg p-4">
                                            Belum ada sub standar pada standar ini.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div id="previewModal" class="fixed inset-0 bg-black/70 hidden justify-center items-center z-50">
        <div class="bg-white w-[90%] h-[90%] rounded-lg overflow-hidden relative">
            <div class="flex justify-between items-center px-4 py-3 border-b">
                <h3 id="previewTitle" class="font-semibold text-sm text-gray-700 truncate">Preview File</h3>
                <button onclick="closeSmartPreview()" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded">X</button>
            </div>
            <div id="previewLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                <p class="mt-3 text-gray-600 text-sm">Memuat preview...</p>
            </div>
            <div id="previewError" class="hidden h-[calc(100%-52px)] flex flex-col items-center justify-center text-center px-4">
                <p class="text-red-500 font-semibold mb-2">Preview tidak tersedia</p>
                <p id="previewErrorText" class="text-gray-500 text-sm mb-4">File ini tidak bisa ditampilkan langsung.</p>
                <a id="previewDownloadLink" href="#" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">Download File</a>
            </div>
            <iframe id="previewFrame" class="hidden w-full h-[calc(100%-52px)]"></iframe>
            <div id="previewImageWrapper" class="hidden w-full h-[calc(100%-52px)] bg-gray-100 items-center justify-center overflow-auto">
                <img id="previewImage" src="" class="max-w-full max-h-full object-contain">
            </div>
        </div>
    </div>

    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg flex flex-col items-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
            <p id="loadingText" class="mt-3 text-gray-600">Memproses...</p>
        </div>
    </div>

    <script>
        const formatFileSizeMb = (bytes) => (bytes / 1024 / 1024).toFixed(2).replace('.', ',');

        function validateUploadFiles(form) {
            const input = form.querySelector('[data-max-file-size]');
            const errorElement = form.querySelector('[data-file-size-error]');

            if (!input) {
                return true;
            }

            const maxSize = Number(input.dataset.maxFileSize || 5242880);
            const allowedExtensions = (input.dataset.allowedExtensions || '')
                .split(',')
                .map((extension) => extension.trim().toLowerCase())
                .filter(Boolean);
            const unsupportedFile = Array.from(input.files || []).find((file) => {
                const extension = file.name.split('.').pop().toLowerCase();

                return allowedExtensions.length > 0 && !allowedExtensions.includes(extension);
            });
            const oversizedFile = Array.from(input.files || []).find((file) => file.size > maxSize);

            if (unsupportedFile) {
                const message = `File "${unsupportedFile.name}" tidak didukung. Gunakan PDF, Word, Excel, JPG, JPEG, atau PNG.`;

                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                }

                input.setCustomValidity(message);
                input.reportValidity();
                return false;
            }

            if (oversizedFile) {
                const message = `File "${oversizedFile.name}" berukuran ${formatFileSizeMb(oversizedFile.size)} MB. Maksimal 5 MB per file.`;

                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.classList.remove('hidden');
                }

                input.setCustomValidity(message);
                input.reportValidity();
                return false;
            }

            if (errorElement) {
                errorElement.textContent = '';
                errorElement.classList.add('hidden');
            }

            input.setCustomValidity('');
            return true;
        }

        document.querySelectorAll('.formUploadBukti [data-max-file-size]').forEach(function(input) {
            input.addEventListener('change', function() {
                validateUploadFiles(input.closest('.formUploadBukti'));
            });
        });

        document.querySelectorAll('.formUploadBukti').forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!validateUploadFiles(form)) {
                    event.preventDefault();
                    return;
                }

                document.getElementById('loadingOverlay').classList.remove('hidden');
                document.getElementById('loadingText').textContent = 'Mengupload dokumen...';
                const btn = form.querySelector('.btnUpload');
                const text = form.querySelector('.textUpload');
                const spinner = form.querySelector('.spinnerUpload');
                btn.disabled = true;
                text.textContent = 'Mengupload...';
                spinner.classList.remove('hidden');
            });
        });

        let previewTimeout;

        function openSmartPreview(previewUrl, downloadUrl, extension, fileName) {
            const modal = document.getElementById('previewModal');
            const title = document.getElementById('previewTitle');
            const loading = document.getElementById('previewLoading');
            const error = document.getElementById('previewError');
            const errorText = document.getElementById('previewErrorText');
            const downloadLink = document.getElementById('previewDownloadLink');
            const frame = document.getElementById('previewFrame');
            const imageWrapper = document.getElementById('previewImageWrapper');
            const image = document.getElementById('previewImage');

            clearTimeout(previewTimeout);
            frame.onload = null;
            image.onload = null;
            image.onerror = null;
            title.textContent = fileName;
            downloadLink.href = downloadUrl;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            frame.classList.add('hidden');
            imageWrapper.classList.add('hidden');
            imageWrapper.classList.remove('flex');
            errorText.textContent = 'File ini tidak bisa ditampilkan langsung.';
            frame.src = 'about:blank';
            image.src = '';

            const pdfFiles = ['pdf'];
            const imageFiles = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            const textFiles = ['txt', 'csv'];
            const officeFiles = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

            if (pdfFiles.includes(extension) || textFiles.includes(extension)) {
                frame.src = previewUrl;
                frame.onload = function() {
                    clearTimeout(previewTimeout);
                    loading.classList.add('hidden');
                    frame.classList.remove('hidden');
                };
                return;
            }

            if (imageFiles.includes(extension)) {
                image.src = previewUrl;
                image.onload = function() {
                    clearTimeout(previewTimeout);
                    loading.classList.add('hidden');
                    imageWrapper.classList.remove('hidden');
                    imageWrapper.classList.add('flex');
                };
                image.onerror = function() {
                    clearTimeout(previewTimeout);
                    loading.classList.add('hidden');
                    error.classList.remove('hidden');
                    errorText.textContent = 'Gambar gagal ditampilkan.';
                };
                return;
            }

            loading.classList.add('hidden');
            error.classList.remove('hidden');
            errorText.textContent = officeFiles.includes(extension)
                ? 'File Word, Excel, atau PowerPoint tidak bisa dipreview langsung di browser.'
                : 'Format file ini tidak mendukung preview langsung.';
        }

        function closeSmartPreview() {
            const modal = document.getElementById('previewModal');
            const frame = document.getElementById('previewFrame');
            const image = document.getElementById('previewImage');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            clearTimeout(previewTimeout);
            frame.onload = null;
            image.onload = null;
            image.onerror = null;
            frame.src = 'about:blank';
            image.src = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = @json(session('active_tab')) || localStorage.getItem('activeTabDosenBukti');
            const openAccordion = @json(session('open_accordion'));
            const targetScroll = @json(session('target_scroll'));

            if (activeTab) {
                const tabButton = document.querySelector(`[data-tabs-target="#${activeTab}"]`);
                if (tabButton) {
                    setTimeout(() => tabButton.click(), 200);
                }
            }

            if (openAccordion) {
                setTimeout(() => {
                    const body = document.getElementById(openAccordion);
                    if (body) body.classList.remove('hidden');
                }, 400);
            }

            if (targetScroll) {
                setTimeout(() => {
                    const target = document.getElementById(targetScroll);
                    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 700);
            }
        });
    </script>
    @include('layouts.partials.back-to-top')
</x-app-layout>
