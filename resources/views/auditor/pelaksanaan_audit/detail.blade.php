<x-app-layout>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>

    @include('auditor.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            @if ($errors->any())
                <div id="alert-error"
                    class="mb-4 rounded-lg bg-red-100 p-4 text-red-700 text-sm transition-opacity duration-500">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Detail Pemetaan Standar Mutu</h1>
                            <p class="text-sm text-gray-600 mt-1">UPT: <span
                                    class="font-medium">{{ $upt->nama_upt ?? '-' }}</span></p>
                            <p class="text-sm text-gray-600 mt-1">Periode: <span
                                    class="font-medium">{{ $periode->tahun ?? '-' }}</span></p>
                        </div>
                        @foreach ($buktiDukung as $item)
                            tes
                        @endforeach
                        <div class="flex flex-col">

                            {{ $auditee->auditee_id }}
                            {{ $upt->nama_upt }}
                            {{ $upt->upt_id }}

                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('auditee.ami') }}"
                                class="flex items-center gap-2 bg-gray-500 hover:bg-gray-700 text-white text-sm px-3 py-2 rounded">
                                <i class="bi bi-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if ($pemetaanStandar->count() > 0)
                <div class="bg-white shadow-xs rounded-lg border border-default p-6">

                    {{-- TAB BAR --}}
                    <div class="mb-4 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="auditee-standar-tab"
                            data-tabs-toggle="#auditee-standar-tab-content"
                            data-tabs-active-classes="text-blue-600 border-blue-600"
                            data-tabs-inactive-classes="text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300"
                            role="tablist">
                            @foreach ($pemetaanStandar as $index => $standar)
                                <li class="me-2" role="presentation">
                                    <button
                                        onclick="localStorage.setItem('activeTabAuditee', 'content-{{ $standar->standar_mutu_id }}')"
                                        class="inline-block p-4 border-b-2 rounded-t-lg"
                                        id="tab-{{ $standar->standar_mutu_id }}"
                                        data-tabs-target="#content-{{ $standar->standar_mutu_id }}" type="button"
                                        role="tab" aria-controls="content-{{ $standar->standar_mutu_id }}"
                                        aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                        {{ $standar->standar_mutu->nama_standar_mutu ?? '-' }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- TAB CONTENT --}}
                    <div id="auditee-standar-tab-content">
                        @foreach ($pemetaanStandar as $index => $standar)
                            <div id="content-{{ $standar->standar_mutu_id }}"
                                class="{{ $index == 0 ? '' : 'hidden' }} rounded-lg bg-gray-50 p-4" role="tabpanel"
                                aria-labelledby="tab-{{ $standar->standar_mutu_id }}">

                                <div class="mb-4">
                                    <h2 class="text-lg font-semibold text-gray-800">
                                        {{ $standar->standar_mutu->nama_standar_mutu ?? '-' }}
                                    </h2>
                                    <p class="text-sm text-gray-500">Silakan periksa item standar dan isi penilaian di
                                        bawah ini.</p>
                                </div>

                                {{-- ACCORDION LEVEL 1: SUB STANDAR --}}
                                <div id="accordion-sub-{{ $standar->standar_mutu_id }}" data-accordion="collapse">
                                    {{-- Filter sub standar yang hanya milik standar_mutu_id saat ini --}}
                                    @forelse ($uptSubStandar->where('standar_mutu_id', $standar->standar_mutu_id) as $sub)
                                        <div class="mb-3 border rounded-xl overflow-hidden bg-white shadow-sm">
                                            {{-- Header Sub Standar --}}
                                            <h3 id="heading-sub-{{ $sub->upt_sub_standar_id }}">
                                                <button type="button"
                                                    class="flex items-center justify-between w-full p-4 font-semibold text-left text-gray-800 bg-gray-100 hover:bg-gray-200 transition-all"
                                                    data-accordion-target="#body-sub-{{ $sub->upt_sub_standar_id }}"
                                                    aria-expanded="false">
                                                    <span>{{ $sub->nama_sub_standar }}</span>
                                                    <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0"
                                                        fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                                    </svg>
                                                </button>
                                            </h3>

                                            {{-- Body Sub Standar --}}
                                            <div id="body-sub-{{ $sub->upt_sub_standar_id }}" class="hidden"
                                                aria-labelledby="heading-sub-{{ $sub->upt_sub_standar_id }}">
                                                <div class="p-4 border-t">
                                                    @php
                                                        $items =
                                                            $uptItemSubStandar[$sub->upt_sub_standar_id] ?? collect();
                                                        $nomorLevel1 = 0;
                                                    @endphp

                                                    {{-- ACCORDION LEVEL 2: ITEM SUB STANDAR --}}

                                                    <div id="accordion-item-{{ $sub->upt_sub_standar_id }}"
                                                        data-accordion="collapse">
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

                                                                $listBukti =
                                                                    $buktiDukung[$item->upt_item_sub_standar_id] ??
                                                                    collect();
                                                                $dataJawaban =
                                                                    $jawabanAudit[$item->upt_item_sub_standar_id] ??
                                                                    null;
                                                                $isAnswered = !is_null($dataJawaban);
                                                            @endphp

                                                            <div
                                                                class="mb-4 rounded-lg border transition-colors {{ $isAnswered ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white' }} {{ $levelClass }}">

                                                                {{-- Header Item --}}
                                                                <div class="flex items-start gap-3 p-4">
                                                                    <div
                                                                        class="text-sm font-semibold {{ $isAnswered ? 'text-green-600' : 'text-gray-500' }} min-w-[28px]">
                                                                        @if ($level == 1)
                                                                            @php $nomorLevel1++; @endphp
                                                                            {{ $nomorLevel1 }}.
                                                                        @else
                                                                            ↳
                                                                        @endif
                                                                    </div>

                                                                    <div class="flex-1">
                                                                        <p
                                                                            class="text-md font-medium {{ $isAnswered ? 'text-green-900' : 'text-gray-800' }} mb-3">
                                                                            {{ $item->nama_item }}
                                                                        </p>

                                                                        {{-- Accordion Bukti & Form --}}
                                                                        <div
                                                                            class="rounded-xl border {{ $isAnswered ? 'border-green-200' : 'border-gray-200' }} bg-gray-50 overflow-hidden shadow-sm">
                                                                            <h4
                                                                                id="heading-eval-{{ $item->upt_item_sub_standar_id }}">
                                                                                <button type="button"
                                                                                    class="flex items-center justify-between w-full px-5 py-3 bg-white hover:bg-gray-50 transition-all"
                                                                                    data-accordion-target="#body-eval-{{ $item->upt_item_sub_standar_id }}"
                                                                                    aria-expanded="false">
                                                                                    <span
                                                                                        class="flex items-center text-xs font-bold text-gray-800 uppercase tracking-wider">
                                                                                        <i
                                                                                            class="fas fa-edit mr-2 {{ $isAnswered ? 'text-green-600' : 'text-indigo-600' }}"></i>
                                                                                        Bukti & Penilaian
                                                                                    </span>
                                                                                    <div
                                                                                        class="flex items-center gap-3">
                                                                                        <span
                                                                                            class="text-xs font-semibold px-3 py-1 rounded-full {{ $listBukti->count() > 0 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                                                                            {{ $listBukti->count() > 0 ? $listBukti->count() . ' File' : 'Belum Ada' }}
                                                                                        </span>
                                                                                        <svg data-accordion-icon
                                                                                            class="w-3 h-3 rotate-180"
                                                                                            fill="none"
                                                                                            viewBox="0 0 10 6">
                                                                                            <path stroke="currentColor"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M9 5 5 1 1 5" />
                                                                                        </svg>
                                                                                    </div>
                                                                                </button>
                                                                            </h4>

                                                                            <div id="body-eval-{{ $item->upt_item_sub_standar_id }}"
                                                                                class="hidden p-5 bg-white border-t {{ $isAnswered ? 'border-green-100' : 'border-gray-100' }}">
                                                                                {{-- FORM INPUT (Edit Mode) --}}
                                                                                <form
                                                                                    action="{{ route('auditor.pelaksanaan_audit.penilaian', $item->upt_item_sub_standar_id) }}"
                                                                                    method="POST" class="space-y-6">
                                                                                    @csrf
                                                                                    <input type="hidden"
                                                                                        name="upt_item_sub_standar_id"
                                                                                        value="{{ $item->upt_item_sub_standar_id }}">

                                                                                    {{-- Preview Bukti --}}
                                                                                    <div
                                                                                        class="bg-blue-50/50 p-3 rounded-lg border border-blue-100">
                                                                                        <p
                                                                                            class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-2 flex items-center">
                                                                                            <i
                                                                                                class="fas fa-paperclip mr-2"></i>
                                                                                            Dokumen Bukti Pendukung
                                                                                        </p>
                                                                                        <div
                                                                                            class="flex flex-wrap gap-3">
                                                                                            @forelse($listBukti as $dokumen)
                                                                                                <a href="{{ asset('storage/' . $dokumen->path) }}"
                                                                                                    target="_blank"
                                                                                                    class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-200 rounded-md text-sm text-blue-700 hover:bg-blue-50 hover:text-blue-800 transition-colors shadow-sm group">
                                                                                                    <i
                                                                                                        class="fas fa-file-pdf mr-2 text-red-500 group-hover:scale-110 transition-transform"></i>
                                                                                                    <span
                                                                                                        class="truncate max-w-[200px]">{{ $dokumen->nama_file ?? 'Lihat Dokumen' }}</span>
                                                                                                </a>
                                                                                            @empty
                                                                                                <p
                                                                                                    class="text-sm text-gray-400 italic">
                                                                                                    Tidak ada dokumen
                                                                                                    bukti yang diunggah.
                                                                                                </p>
                                                                                            @endforelse
                                                                                        </div>
                                                                                    </div>

                                                                                    <hr class="border-gray-100">

                                                                                    {{-- Layout Utama Form --}}
                                                                                    <div class="space-y-5">
                                                                                        {{-- Radio Jawaban dengan Style Card --}}
                                                                                        <div>
                                                                                            <label
                                                                                                class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">
                                                                                                Status Capaian /
                                                                                                Pemenuhan Standar
                                                                                            </label>
                                                                                            <div
                                                                                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                                                {{-- Opsi YA --}}
                                                                                                <label
                                                                                                    class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all hover:bg-gray-50 {{ $isAnswered && $dataJawaban->jawaban == '1' ? 'border-green-500 bg-green-50 ring-1 ring-green-500' : 'border-gray-200' }}">
                                                                                                    <input
                                                                                                        type="radio"
                                                                                                        name="jawaban"
                                                                                                        value="Ya"
                                                                                                        class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                                                                                                        {{ $isAnswered && $dataJawaban->jawaban == '1' ? 'checked' : '' }}>
                                                                                                    <div
                                                                                                        class="ml-4">
                                                                                                        <span
                                                                                                            class="block text-sm font-bold {{ $isAnswered && $dataJawaban->jawaban == '1' ? 'text-green-900' : 'text-gray-700' }}">Ya
                                                                                                            /
                                                                                                            Terpenuhi</span>
                                                                                                        <span
                                                                                                            class="block text-xs text-gray-500">Kriteria
                                                                                                            standar
                                                                                                            telah
                                                                                                            tercapai
                                                                                                            sesuai
                                                                                                            bukti.</span>
                                                                                                    </div>
                                                                                                </label>

                                                                                                {{-- Opsi TIDAK --}}
                                                                                                <label
                                                                                                    class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all hover:bg-gray-50 {{ $isAnswered && $dataJawaban->jawaban == '0' ? 'border-red-500 bg-red-50 ring-1 ring-red-500' : 'border-gray-200' }}">
                                                                                                    <input
                                                                                                        type="radio"
                                                                                                        name="jawaban"
                                                                                                        value="Tidak"
                                                                                                        class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500"
                                                                                                        {{ $isAnswered && $dataJawaban->jawaban == '0' ? 'checked' : '' }}>
                                                                                                    <div
                                                                                                        class="ml-4">
                                                                                                        <span
                                                                                                            class="block text-sm font-bold {{ $isAnswered && $dataJawaban->jawaban == '0' ? 'text-red-900' : 'text-gray-700' }}">Tidak
                                                                                                            / Belum
                                                                                                            Terpenuhi</span>
                                                                                                        <span
                                                                                                            class="block text-xs text-gray-500">Kriteria
                                                                                                            standar
                                                                                                            belum atau
                                                                                                            tidak
                                                                                                            terpenuhi.</span>
                                                                                                    </div>
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>

                                                                                        {{-- Catatan (Sekarang di bawah dan lebar penuh) --}}
                                                                                        <div>
                                                                                            <label
                                                                                                class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">
                                                                                                Catatan Audit &
                                                                                                Rekomendasi
                                                                                            </label>
                                                                                            <textarea name="catatan" rows="4"
                                                                                                class="w-full text-sm border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-gray-400"
                                                                                                placeholder="Berikan deskripsi temuan atau alasan penilaian di sini...">{{ $isAnswered ? $dataJawaban->catatan : '' }}</textarea>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- Footer/Tombol --}}
                                                                                    <div
                                                                                        class="pt-4 flex items-center justify-between border-t border-gray-100">
                                                                                        <p
                                                                                            class="text-xs text-gray-400 italic">
                                                                                            <i
                                                                                                class="fas fa-info-circle mr-1"></i>
                                                                                            Pastikan data sudah benar
                                                                                            sebelum menyimpan.
                                                                                        </p>
                                                                                        <button type="submit"
                                                                                            class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-widest rounded-md hover:shadow-md transition-all active:scale-95">
                                                                                            <i
                                                                                                class="fas fa-save mr-2 text-sm"></i>
                                                                                            {{ $isAnswered ? 'Update Penilaian' : 'Simpan Penilaian' }}
                                                                                        </button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div
                                                                class="p-4 text-sm text-gray-500 bg-gray-50 rounded-lg">
                                                                Belum ada item.</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-sm text-yellow-800 bg-yellow-50 rounded-lg">Belum ada sub
                                            standar.</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-4 text-sm text-yellow-800 bg-yellow-50 rounded-lg">
                    Belum ada pemetaan standar mutu untuk UPT ini.
                </div>
            @endif
        </div>
    </div>

    <button id="backToTop" type="button"
        class="hidden fixed bottom-6 right-6 z-50 p-3 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
    </button>


    <script>
        setTimeout(() => {
            const error = document.getElementById('alert-error');
            const success = document.getElementById('alert-success');

            if (error) {
                error.style.opacity = '0';
                setTimeout(() => error.remove(), 500);
            }

            if (success) {
                success.style.opacity = '0';
                setTimeout(() => success.remove(), 500);
            }
        }, 3000); // 3 detik hilang

        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = localStorage.getItem('activeTabAuditee');

            if (activeTab) {
                const tabButton = document.querySelector(`[data-tabs-target="#${activeTab}"]`);

                if (tabButton) {
                    tabButton.click();
                }
            }

            if (window.location.hash) {
                setTimeout(() => {
                    const target = document.querySelector(window.location.hash);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }, 500);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Ambil ID dari session Laravel
            const activeItem = @json(session('active_item'));
            const activeItemId = 'accordion-item-' + activeItem;

            if (activeItemId) {
                // 1. Cari target accordion body yang harus dibuka
                const targetBody = document.getElementById(`body-eval-${activeItemId}`);
                const targetHeading = document.getElementById(`heading-eval-${activeItemId}`);

                if (targetBody && targetHeading) {
                    // 2. Trigger klik pada button heading untuk membuka accordion
                    // Kita gunakan setTimeout agar Flowbite selesai inisialisasi terlebih dahulu
                    setTimeout(() => {
                        const btn = targetHeading.querySelector('button');
                        if (btn && btn.getAttribute('aria-expanded') === 'false') {
                            btn.click();
                        }

                        // 3. Scroll ke item tersebut
                        const container = document.getElementById(`item-penilaian-${activeItemId}`);
                        if (container) {
                            container.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            // Opsional: Beri efek highlight sejenak
                            container.classList.add('ring-2', 'ring-blue-400');
                            setTimeout(() => {
                                container.classList.remove('ring-2', 'ring-blue-400');
                            }, 2000);
                        }
                    }, 500);
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = @json(session('active_tab'));

            if (activeTab) {
                const tabButton = document.querySelector(`[data-tabs-target="#${activeTab}"]`);

                if (tabButton) {
                    setTimeout(() => {
                        tabButton.click();

                        if (window.location.hash) {
                            setTimeout(() => {
                                const target = document.querySelector(window.location.hash);

                                if (target) {
                                    target.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }
                            }, 300);
                        }
                    }, 200);
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const backToTopButton = document.getElementById('backToTop');

            // tampilkan tombol saat scroll ke bawah
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTopButton.classList.remove('hidden');
                } else {
                    backToTopButton.classList.add('hidden');
                }
            });

            // klik tombol → scroll ke atas
            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</x-app-layout>
