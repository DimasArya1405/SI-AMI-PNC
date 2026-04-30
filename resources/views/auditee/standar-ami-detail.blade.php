<x-app-layout>
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>

    @include('auditee.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            @if ($errors->any())
            <div id="alert-error" class="mb-4 rounded-lg bg-red-100 p-4 text-red-700 text-sm transition-opacity duration-500">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Detail Pemetaan Standar Mutu</h1>
                            <p class="text-sm text-gray-600 mt-1">UPT: <span class="font-medium">{{ $upt->nama_upt ?? '-' }}</span></p>
                            <p class="text-sm text-gray-600 mt-1">Periode: <span class="font-medium">{{ $periode->tahun ?? '-' }}</span></p>
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
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center"
                        id="auditee-standar-tab"
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

                {{-- TAB CONTENT --}}
                <div id="auditee-standar-tab-content">
                    @foreach ($pemetaanStandar as $index => $standar)
                    @php
                    $subStandarList = $uptSubStandar
                    ->where('standar_mutu_id', $standar->standar_mutu_id)
                    ->sortBy('urutan');
                    @endphp

                    <div
                        id="content-{{ $standar->standar_mutu_id }}"
                        class="{{ $index == 0 ? '' : 'hidden' }} rounded-lg bg-gray-50 p-4"
                        role="tabpanel"
                        aria-labelledby="tab-{{ $standar->standar_mutu_id }}">

                        <div class="mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">
                                {{ $standar->standar_mutu->nama_standar_mutu ?? '-' }}
                            </h2>
                            <p class="text-sm text-gray-500">
                                Silakan upload bukti dukung sesuai item standar yang tersedia.
                            </p>
                        </div>

                        <div id="accordion-auditee-{{ $standar->standar_mutu_id }}"
                            data-accordion="collapse"
                            class="space-y-4">

                            @forelse ($subStandarList as $sub)
                            @php
                            $items = ($uptItemSubStandar[$sub->upt_sub_standar_id] ?? collect())->sortBy([
                            ['urutan', 'asc'],
                            ['created_at', 'asc'],
                            ]);

                            $headingId = 'heading-auditee-' . $sub->upt_sub_standar_id;
                            $bodyId = 'body-auditee-' . $sub->upt_sub_standar_id;
                            @endphp

                            <div id="sub-{{ $sub->upt_sub_standar_id }}"
                                class="border rounded-xl overflow-hidden bg-white">

                                {{-- Header Accordion --}}
                                <h2 id="{{ $headingId }}">
                                    <button type="button"
                                        class="flex items-center justify-between w-full px-4 py-3 bg-gray-100 hover:bg-gray-200 text-left"
                                        data-accordion-target="#{{ $bodyId }}"
                                        aria-expanded="false"
                                        aria-controls="{{ $bodyId }}">

                                        <div>
                                            <h3 class="font-semibold text-gray-800">
                                                {{ $sub->nama_sub_standar }}
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Total item: {{ $items->count() }}
                                            </p>
                                        </div>

                                        <i data-accordion-icon class="bi bi-chevron-down transition-transform"></i>
                                    </button>
                                </h2>

                                {{-- Body Accordion --}}
                                <div id="{{ $bodyId }}"
                                    class="hidden"
                                    aria-labelledby="{{ $headingId }}">

                                    <div class="p-4">
                                        @php
                                        $nomorLevel1 = 0;
                                        @endphp

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
                                                    ↳
                                                    @endif
                                                </div>

                                                <div class="flex-1">
                                                    <p class="text-md font-medium text-gray-800">
                                                        {{ $item->nama_item }}
                                                    </p>

                                                    <div class="mt-4 rounded-lg border bg-gray-50 p-4">
                                                        <div class="flex items-center justify-between mb-3">
                                                            <h4 class="text-sm font-semibold text-gray-700">
                                                                Bukti Dukung
                                                            </h4>

                                                            <span class="text-xs px-2 py-1 rounded-full {{ $buktiList->count() > 0 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                                {{ $buktiList->count() > 0 ? $buktiList->count() . ' file' : 'Belum ada' }}
                                                            </span>
                                                        </div>

                                                        @if ($buktiList->count() > 0)
                                                        <div class="space-y-2 mb-4">
                                                            @foreach ($buktiList as $bukti)
                                                            <div class="flex items-center justify-between bg-white border rounded px-3 py-2">
                                                                <p class="text-sm font-medium text-gray-800">
                                                                    {{ $bukti->nama_file }}
                                                                </p>

                                                                <div class="flex items-center gap-2">
                                                                    <a href="{{ asset('storage/' . $bukti->file_path) }}"
                                                                        target="_blank"
                                                                        class="text-sm px-3 py-1 bg-blue-500 hover:bg-blue-700 text-white rounded">
                                                                        Lihat
                                                                    </a>

                                                                    @if (!$status_periode)
                                                                    <button type="button"
                                                                        data-modal-target="modal-hapus-bukti"
                                                                        data-modal-toggle="modal-hapus-bukti"
                                                                        class="button-hapus-bukti text-sm px-3 py-1 bg-red-500 hover:bg-red-700 text-white rounded"
                                                                        data-dokumen-id="{{ $bukti->dokumen_id }}"
                                                                        data-nama-file="{{ $bukti->nama_file }}"
                                                                        data-active-tab="content-{{ $standar->standar_mutu_id }}"
                                                                        data-open-accordion="{{ $bodyId }}"
                                                                        data-target-scroll="item-{{ $item->upt_item_sub_standar_id }}">
                                                                        Hapus
                                                                    </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        @else
                                                        <p class="text-xs text-gray-500 mb-4">
                                                            Belum ada bukti dukung untuk item ini.
                                                        </p>
                                                        @endif

                                                        @if (!$status_periode)
                                                        <form action="{{ route('auditee.bukti_dukung.upload') }}"
                                                            method="POST"
                                                            enctype="multipart/form-data"
                                                            class="space-y-3">
                                                            @csrf

                                                            <input type="hidden" name="upt_item_sub_standar_id" value="{{ $item->upt_item_sub_standar_id }}">
                                                            <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                                            <input type="hidden" name="open_accordion" value="{{ $bodyId }}">
                                                            <input type="hidden" name="target_scroll" value="item-{{ $item->upt_item_sub_standar_id }}">
                                                            <input type="hidden" name="periode_id" value="{{ $periode->id }}">

                                                            <input type="file"
                                                                name="file_bukti[]"
                                                                multiple
                                                                class="block w-full text-sm border rounded-lg cursor-pointer bg-white"
                                                                required>

                                                            <button type="submit"
                                                                class="bg-green-500 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">
                                                                Upload Bukti
                                                            </button>
                                                        </form>
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
            @else
            <div class="p-4 text-sm text-yellow-800 bg-yellow-50 rounded-lg">
                Belum ada pemetaan standar mutu untuk UPT ini.
            </div>
            @endif
        </div>
    </div>

    <button
        id="backToTop"
        type="button"
        class="hidden fixed bottom-6 right-6 z-50 p-3 rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-lg transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round"
                stroke-linejoin="round"
                d="M5 15l7-7 7 7" />
        </svg>
    </button>

    {{-- Modal Hapus Bukti --}}
    <div id="modal-hapus-bukti" tabindex="-1"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <button type="button"
                    class="absolute top-3 end-2.5 text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="modal-hapus-bukti">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>

                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-fg-disabled w-12 h-12" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>

                    <h3 class="mb-2 text-body">Apakah Anda yakin akan menghapus bukti dukung ini?</h3>

                    <p class="mb-2 text-sm text-gray-500">
                        File yang akan dihapus:
                    </p>

                    <p id="nama_file_hapus_bukti" class="mb-6 text-sm font-semibold text-gray-800 break-all">
                        -
                    </p>

                    <form id="form-hapus-bukti" method="post">
                        @csrf
                        @method('delete')

                        <input type="hidden" name="open_accordion" id="open_accordion_hapus_bukti">
                        <input type="hidden" name="target_scroll" id="target_scroll_hapus_bukti">
                        <input type="hidden" name="active_tab" id="active_tab_hapus_bukti">

                        <div class="flex items-center space-x-4 justify-center">
                            <button data-modal-hide="modal-hapus-bukti" type="submit"
                                class="text-white transition duration-300 ease-in-out bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                                Iya, saya yakin
                            </button>

                            <button data-modal-hide="modal-hapus-bukti" type="button"
                                class="text-body transition duration-300 ease-in-out bg-white box-border border border-default-medium hover:bg-gray-200 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                                Tidak, Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JS ACORDION
        document.addEventListener('DOMContentLoaded', function() {
            const openAccordion = @json(session('open_accordion'));
            const targetScroll = @json(session('target_scroll'));

            function openTargetAccordion() {
                if (!openAccordion) return;

                const body = document.getElementById(openAccordion);

                if (body) {
                    body.classList.remove('hidden');

                    const triggers = document.querySelectorAll(
                        `[data-accordion-target="#${openAccordion}"]`
                    );

                    triggers.forEach(btn => {
                        btn.setAttribute('aria-expanded', 'true');

                        const icon = btn.querySelector('[data-accordion-icon]');
                        if (icon) {
                            icon.classList.add('rotate-180');
                        }
                    });
                }
            }

            function scrollToTarget() {
                if (!targetScroll) return;

                const target = document.getElementById(targetScroll);

                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    target.classList.add('ring-2', 'ring-blue-400');

                    setTimeout(() => {
                        target.classList.remove('ring-2', 'ring-blue-400');
                    }, 2000);
                }
            }

            // penting: kasih delay supaya Flowbite selesai init
            setTimeout(() => {
                openTargetAccordion();

                setTimeout(() => {
                    scrollToTarget();
                }, 300);

            }, 400);
        });

        // JS Modal Hapus Bukti
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.button-hapus-bukti');

            const form = document.getElementById('form-hapus-bukti');
            const namaFileText = document.getElementById('nama_file_hapus_bukti');
            const activeTabInput = document.getElementById('active_tab_hapus_bukti');
            const openAccordionInput = document.getElementById('open_accordion_hapus_bukti');
            const targetScrollInput = document.getElementById('target_scroll_hapus_bukti');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const dokumenId = this.dataset.dokumenId;
                    const namaFile = this.dataset.namaFile;

                    let actionUrl = "{{ route('auditee.bukti_dukung.hapus', ':id') }}";
                    actionUrl = actionUrl.replace(':id', dokumenId);

                    form.setAttribute('action', actionUrl);
                    namaFileText.textContent = namaFile;

                    activeTabInput.value = this.dataset.activeTab;
                    openAccordionInput.value = this.dataset.openAccordion;
                    targetScrollInput.value = this.dataset.targetScroll;
                });
            });
        });

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