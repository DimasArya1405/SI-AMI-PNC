<x-app-layout>
    @include('admin.sidebar')

    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">

            {{-- Header --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">Detail Pemetaan Standar Mutu</h1>
                            <p class="text-sm text-gray-600 mt-1">
                                UPT: <span class="font-medium">{{ $upt->nama_upt }}</span>
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.ami.upt_standar_mutu') }}"
                                class="flex items-center gap-2 bg-gray-500 hover:bg-gray-700 text-white text-sm px-3 py-2 rounded">
                                <i class="bi bi-arrow-left"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs Standar --}}
            <div class="bg-white shadow-xs rounded-lg border border-default p-6">
                @if ($pemetaanStandar->count() > 0)

                <div class="mb-4 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center"
                        id="default-tab"
                        data-tabs-toggle="#default-tab-content"
                        data-tabs-active-classes="text-blue-600 border-blue-600"
                        data-tabs-inactive-classes="text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300"
                        role="tablist">

                        @foreach ($pemetaanStandar as $index => $standar)
                        <li class="me-2" role="presentation">
                            <button
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

                <div id="default-tab-content">
                    @foreach ($pemetaanStandar as $index => $standar)
                    <div class="{{ $index == 0 ? '' : 'hidden' }} rounded-lg bg-gray-50 p-4"
                        id="content-{{ $standar->standar_mutu_id }}"
                        role="tabpanel"
                        aria-labelledby="tab-{{ $standar->standar_mutu_id }}">

                        {{-- Info Standar --}}
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-white border rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Nama UPT</p>
                                <p class="text-sm font-medium text-gray-900">{{ $upt->nama_upt }}</p>
                            </div>

                            <div class="bg-white border rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Periode</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $periode->tahun ?? '-' }}
                                </p>
                            </div>

                            <div class="bg-white border rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Standar Mutu</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $standar->standar_mutu->nama_standar_mutu ?? '-' }}
                                </p>
                            </div>

                            <div class="bg-white border rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Jumlah Sub Standar</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $uptSubStandar->where('standar_mutu_id', $standar->standar_mutu_id)->count() }}
                                </p>
                            </div>
                        </div>

                        {{-- Tombol tambah sub standar --}}
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">
                                    Daftar Sub Standar
                                </h2>
                                <p class="text-sm text-gray-500">
                                    Kelola sub standar dan item untuk UPT ini pada standar yang dipilih.
                                </p>
                            </div>

                            <button
                                data-modal-target="modal-tambah-sub-{{ $standar->standar_mutu_id }}"
                                data-modal-toggle="modal-tambah-sub-{{ $standar->standar_mutu_id }}"
                                class="bg-green-500 hover:bg-green-700 text-white text-sm px-4 py-2 rounded flex items-center gap-2">
                                <i class="bi bi-plus"></i>
                                Tambah Sub Standar
                            </button>
                        </div>

                        {{-- List Sub Standar Accordion --}}
                        @php
                        $subStandarPerStandar = $uptSubStandar->where('standar_mutu_id', $standar->standar_mutu_id);
                        @endphp

                        <div id="accordion-sub-{{ $standar->standar_mutu_id }}"
                            data-accordion="collapse"
                            class="space-y-3">

                            @forelse ($subStandarPerStandar as $sub)
                            @php
                            $items = ($uptItemSubStandar[$sub->upt_sub_standar_id] ?? collect())->sortBy([
                            ['urutan', 'asc'],
                            ['created_at', 'asc'],
                            ]);

                            $headingId = 'accordion-heading-' . $sub->upt_sub_standar_id;
                            $bodyId = 'accordion-body-' . $sub->upt_sub_standar_id;
                            @endphp

                            <div id="sub-{{ $sub->upt_sub_standar_id }}" class="bg-white border border-gray-200 rounded-lg overflow-hidden">

                                @php
                                $headingId = 'heading-' . $sub->upt_sub_standar_id;
                                $bodyId = 'body-' . $sub->upt_sub_standar_id;
                                @endphp

                                {{-- Header Sub Standar --}}
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">

                                    {{-- Trigger Accordion --}}
                                    <button type="button"
                                        class="flex-1 text-left"
                                        data-accordion-target="#{{ $bodyId }}"
                                        aria-expanded="false"
                                        aria-controls="{{ $bodyId }}">

                                        <h3 class="text-sm font-semibold text-gray-900">
                                            {{ $sub->nama_sub_standar }}
                                        </h3>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Total item:
                                            {{ isset($uptItemSubStandar[$sub->upt_sub_standar_id]) ? $uptItemSubStandar[$sub->upt_sub_standar_id]->count() : 0 }}
                                        </p>
                                    </button>

                                    {{-- Tombol Aksi --}}
                                    <div class="flex items-center gap-2 ml-4">
                                        <button type="button"
                                            data-modal-target="modal-tambah-item-{{ $sub->upt_sub_standar_id }}"
                                            data-modal-toggle="modal-tambah-item-{{ $sub->upt_sub_standar_id }}"
                                            class="bg-green-500 hover:bg-green-700 text-white text-xs px-3 py-2 rounded">
                                            <i class="bi bi-plus"></i> Item Utama
                                        </button>

                                        <button type="button"
                                            data-modal-target="modal-edit-sub-{{ $sub->upt_sub_standar_id }}"
                                            data-modal-toggle="modal-edit-sub-{{ $sub->upt_sub_standar_id }}"
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-2 rounded">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button type="button"
                                            data-modal-target="modal-hapus-sub-{{ $sub->upt_sub_standar_id }}"
                                            data-modal-toggle="modal-hapus-sub-{{ $sub->upt_sub_standar_id }}"
                                            class="bg-red-500 hover:bg-red-700 text-white text-xs px-3 py-2 rounded">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <button type="button"
                                            data-accordion-target="#{{ $bodyId }}"
                                            aria-expanded="false"
                                            aria-controls="{{ $bodyId }}"
                                            class="text-gray-600 hover:text-gray-900 px-2">
                                            <i data-accordion-icon class="bi bi-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Body Accordion --}}
                                <div id="{{ $bodyId }}"
                                    class="hidden"
                                    aria-labelledby="{{ $headingId }}">

                                    <div class="p-4 border-t border-gray-200">

                                        @if ($items->count() > 0)
                                        <div class="space-y-3">
                                            @php
                                            $nomorLevel1 = 0;
                                            @endphp

                                            @foreach ($items as $item)
                                            @php
                                            $level = $item->level ?? 1;

                                            $levelClass = match($level) {
                                            1 => '',
                                            2 => 'ml-6',
                                            3 => 'ml-12',
                                            4 => 'ml-16',
                                            default => 'ml-20',
                                            };
                                            @endphp

                                            <div id="item-{{ $item->upt_item_sub_standar_id }}"
                                                class="border rounded-lg p-3 bg-white {{ $levelClass }}">

                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="flex-1">
                                                        <div class="flex items-start gap-2">
                                                            <span class="text-sm font-medium text-gray-500 min-w-[20px]">
                                                                @if ($level == 1)
                                                                @php $nomorLevel1++; @endphp
                                                                {{ $nomorLevel1 }}.
                                                                @else
                                                                ↳
                                                                @endif
                                                            </span>

                                                            <div class="flex-1">
                                                                <p class="text-sm text-gray-900 leading-6">
                                                                    {{ $item->nama_item }}
                                                                </p>
                                                                <p class="text-xs text-gray-400 mt-1">
                                                                    Level {{ $level }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <button
                                                            data-modal-target="modal-tambah-anak-item-{{ $item->upt_item_sub_standar_id }}"
                                                            data-modal-toggle="modal-tambah-anak-item-{{ $item->upt_item_sub_standar_id }}"
                                                            class="bg-green-500 hover:bg-green-700 text-white text-xs px-3 py-2 rounded">
                                                            <i class="bi bi-diagram-3"></i> Sub Item
                                                        </button>

                                                        <button
                                                            data-modal-target="modal-edit-item-{{ $item->upt_item_sub_standar_id }}"
                                                            data-modal-toggle="modal-edit-item-{{ $item->upt_item_sub_standar_id }}"
                                                            class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs px-3 py-2 rounded">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>

                                                        <button
                                                            data-modal-target="modal-hapus-item-{{ $item->upt_item_sub_standar_id }}"
                                                            data-modal-toggle="modal-hapus-item-{{ $item->upt_item_sub_standar_id }}"
                                                            class="bg-red-500 hover:bg-red-700 text-white text-xs px-3 py-2 rounded">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal tambah anak item --}}
                                            <div id="modal-tambah-anak-item-{{ $item->upt_item_sub_standar_id }}" tabindex="-1" aria-hidden="true"
                                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                <div class="relative p-4 w-full max-w-2xl max-h-full">
                                                    <div class="relative bg-white rounded-lg shadow">
                                                        <div class="flex items-center justify-between p-4 border-b rounded-t">
                                                            <h3 class="text-lg font-semibold text-gray-900">Tambah Anak Item</h3>
                                                            <button type="button"
                                                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                                                data-modal-hide="modal-tambah-anak-item-{{ $item->upt_item_sub_standar_id }}">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </div>

                                                        <form action="{{ route('admin.ami.upt_item_sub_standar_mutu.tambah') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="open_accordion" value="body-{{ $sub->upt_sub_standar_id }}">
                                                            <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                                            <input type="hidden" name="target_scroll" value="item-{{ $item->upt_item_sub_standar_id }}">
                                                            <input type="hidden" name="upt_id" value="{{ $upt->upt_id }}">
                                                            <input type="hidden" name="upt_sub_standar_id" value="{{ $sub->upt_sub_standar_id }}">
                                                            <input type="hidden" name="parent_upt_item_id" value="{{ $item->upt_item_sub_standar_id }}">
                                                            <input type="hidden" name="periode_id" value="{{ $periode_id }}">

                                                            <div class="p-4 space-y-4">
                                                                <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-lg p-3">
                                                                    Parent: <span class="font-medium">{{ $item->nama_item }}</span>
                                                                </div>

                                                                <div>
                                                                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama Item</label>
                                                                    <textarea name="nama_item" rows="4"
                                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                                                        required></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center justify-end p-4 border-t gap-2">
                                                                <button type="button"
                                                                    data-modal-hide="modal-tambah-anak-item-{{ $item->upt_item_sub_standar_id }}"
                                                                    class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                                    Batal
                                                                </button>
                                                                <button type="submit"
                                                                    class="px-4 py-2 text-sm bg-green-500 hover:bg-green-700 text-white rounded">
                                                                    Simpan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal edit item --}}
                                            <div id="modal-edit-item-{{ $item->upt_item_sub_standar_id }}" tabindex="-1" aria-hidden="true"
                                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                <div class="relative p-4 w-full max-w-2xl max-h-full">
                                                    <div class="relative bg-white rounded-lg shadow">
                                                        <div class="flex items-center justify-between p-4 border-b rounded-t">
                                                            <h3 class="text-lg font-semibold text-gray-900">Edit Item</h3>
                                                            <button type="button"
                                                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                                                data-modal-hide="modal-edit-item-{{ $item->upt_item_sub_standar_id }}">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </div>

                                                        <form action="{{ route('admin.ami.upt_item_sub_standar_mutu.edit') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="open_accordion" value="body-{{ $sub->upt_sub_standar_id }}">
                                                            <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                                            <input type="hidden" name="target_scroll" value="item-{{ $item->upt_item_sub_standar_id }}">
                                                            <input type="hidden" name="upt_item_sub_standar_id" value="{{ $item->upt_item_sub_standar_id }}">

                                                            <div class="p-4 space-y-4">
                                                                <div>
                                                                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama Item</label>
                                                                    <textarea name="nama_item" rows="4"
                                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                                                        required>{{ $item->nama_item }}</textarea>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center justify-end p-4 border-t gap-2">
                                                                <button type="button"
                                                                    data-modal-hide="modal-edit-item-{{ $item->upt_item_sub_standar_id }}"
                                                                    class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                                    Batal
                                                                </button>
                                                                <button type="submit"
                                                                    class="px-4 py-2 text-sm bg-yellow-400 hover:bg-yellow-500 text-white rounded">
                                                                    Update
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal hapus item --}}
                                            <div id="modal-hapus-item-{{ $item->upt_item_sub_standar_id }}" tabindex="-1" aria-hidden="true"
                                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                <div class="relative p-4 w-full max-w-md max-h-full">
                                                    <div class="relative bg-white rounded-lg shadow">
                                                        <div class="p-6 text-center">
                                                            <i class="bi bi-exclamation-triangle text-red-500 text-4xl"></i>
                                                            <h3 class="mt-4 mb-2 text-lg font-medium text-gray-900">Hapus Item?</h3>
                                                            <p class="mb-5 text-sm text-gray-500">
                                                                Data item akan dihapus dari pemetaan UPT ini.
                                                            </p>

                                                            <form action="{{ route('admin.ami.upt_item_sub_standar_mutu.hapus') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="target_scroll" value="sub-{{ $sub->upt_sub_standar_id }}">
                                                                <input type="hidden" name="open_accordion" value="body-{{ $sub->upt_sub_standar_id }}">
                                                                <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">

                                                                @if ($item->parent_upt_item_id)
                                                                <input type="hidden" name="target_scroll" value="item-{{ $item->parent_upt_item_id }}">
                                                                @else
                                                                <input type="hidden" name="target_scroll" value="sub-{{ $sub->upt_sub_standar_id }}">
                                                                @endif
                                                                <input type="hidden" name="upt_item_sub_standar_id" value="{{ $item->upt_item_sub_standar_id }}">

                                                                <div class="flex justify-center gap-2">
                                                                    <button type="button"
                                                                        data-modal-hide="modal-hapus-item-{{ $item->upt_item_sub_standar_id }}"
                                                                        class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                                        Batal
                                                                    </button>
                                                                    <button type="submit"
                                                                        class="px-4 py-2 text-sm bg-red-500 hover:bg-red-700 text-white rounded">
                                                                        Hapus
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @else
                                        <div class="bg-yellow-50 text-yellow-800 text-sm rounded-lg p-4">
                                            Belum ada item pada sub standar ini.
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            {{-- Modal tambah item utama --}}
                            <div id="modal-tambah-item-{{ $sub->upt_sub_standar_id }}" tabindex="-1" aria-hidden="true"
                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative p-4 w-full max-w-2xl max-h-full">
                                    <div class="relative bg-white rounded-lg shadow">
                                        <div class="flex items-center justify-between p-4 border-b rounded-t">
                                            <h3 class="text-lg font-semibold text-gray-900">Tambah Item Utama</h3>
                                            <button type="button"
                                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                                data-modal-hide="modal-tambah-item-{{ $sub->upt_sub_standar_id }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        <form action="{{ route('admin.ami.upt_item_sub_standar_mutu.tambah') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="open_accordion" value="body-{{ $sub->upt_sub_standar_id }}">
                                            <input type="hidden" name="target_scroll" value="sub-{{ $sub->upt_sub_standar_id }}">
                                            <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                            <input type="hidden" name="target_scroll" value="sub-{{ $sub->upt_sub_standar_id }}">
                                            <input type="hidden" name="upt_id" value="{{ $upt->upt_id }}">
                                            <input type="hidden" name="upt_sub_standar_id" value="{{ $sub->upt_sub_standar_id }}">
                                            <input type="hidden" name="parent_upt_item_id" value="">
                                            <input type="hidden" name="periode_id" value="{{ $periode_id }}">

                                            <div class="p-4 space-y-4">
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama Item</label>
                                                    <textarea name="nama_item" rows="4"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                                        required></textarea>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-end p-4 border-t gap-2">
                                                <button type="button"
                                                    data-modal-hide="modal-tambah-item-{{ $sub->upt_sub_standar_id }}"
                                                    class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 text-sm bg-green-500 hover:bg-green-700 text-white rounded">
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal edit sub standar --}}
                            <div id="modal-edit-sub-{{ $sub->upt_sub_standar_id }}" tabindex="-1" aria-hidden="true"
                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative p-4 w-full max-w-2xl max-h-full">
                                    <div class="relative bg-white rounded-lg shadow">
                                        <div class="flex items-center justify-between p-4 border-b rounded-t">
                                            <h3 class="text-lg font-semibold text-gray-900">Edit Sub Standar</h3>
                                            <button type="button"
                                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                                data-modal-hide="modal-edit-sub-{{ $sub->upt_sub_standar_id }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        <form action="{{ route('admin.ami.upt_sub_standar_mutu.edit') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                            <input type="hidden" name="target_scroll" value="sub-{{ $sub->upt_sub_standar_id }}">
                                            <input type="hidden" name="upt_sub_standar_id" value="{{ $sub->upt_sub_standar_id }}">

                                            <div class="p-4 space-y-4">
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-900">Nama Sub Standar</label>
                                                    <input type="text" name="nama_sub_standar"
                                                        value="{{ $sub->nama_sub_standar }}"
                                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-end p-4 border-t gap-2">
                                                <button type="button"
                                                    data-modal-hide="modal-edit-sub-{{ $sub->upt_sub_standar_id }}"
                                                    class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                    class="px-4 py-2 text-sm bg-yellow-400 hover:bg-yellow-500 text-white rounded">
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal hapus sub standar --}}
                            <div id="modal-hapus-sub-{{ $sub->upt_sub_standar_id }}" tabindex="-1" aria-hidden="true"
                                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative p-4 w-full max-w-md max-h-full">
                                    <div class="relative bg-white rounded-lg shadow">
                                        <div class="p-6 text-center">
                                            <i class="bi bi-exclamation-triangle text-red-500 text-4xl"></i>
                                            <h3 class="mt-4 mb-2 text-lg font-medium text-gray-900">Hapus Sub Standar?</h3>
                                            <p class="mb-5 text-sm text-gray-500">
                                                Sub standar beserta item di bawahnya akan ikut terhapus.
                                            </p>

                                            <form action="{{ route('admin.ami.upt_sub_standar_mutu.hapus') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                                <input type="hidden" name="target_scroll" value="content-{{ $standar->standar_mutu_id }}">
                                                <input type="hidden" name="upt_sub_standar_id" value="{{ $sub->upt_sub_standar_id }}">

                                                <div class="flex justify-center gap-2">
                                                    <button type="button"
                                                        data-modal-hide="modal-hapus-sub-{{ $sub->upt_sub_standar_id }}"
                                                        class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                        Batal
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-sm bg-red-500 hover:bg-red-700 text-white rounded">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @empty
                            <div class="bg-yellow-50 text-yellow-800 text-sm rounded-lg p-4">
                                Belum ada sub standar pada standar ini.
                            </div>
                            @endforelse
                        </div>

                        {{-- Modal tambah sub standar --}}
                        <div id="modal-tambah-sub-{{ $standar->standar_mutu_id }}" tabindex="-1" aria-hidden="true"
                            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-2xl max-h-full">
                                <div class="relative bg-white rounded-lg shadow">
                                    <div class="flex items-center justify-between p-4 border-b rounded-t">
                                        <h3 class="text-lg font-semibold text-gray-900">Tambah Sub Standar</h3>
                                        <button type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                            data-modal-hide="modal-tambah-sub-{{ $standar->standar_mutu_id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.ami.upt_sub_standar_mutu.tambah') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="active_tab" value="content-{{ $standar->standar_mutu_id }}">
                                        <input type="hidden" name="target_scroll" value="content-{{ $standar->standar_mutu_id }}">
                                        <input type="hidden" name="upt_id" value="{{ $upt->upt_id }}">
                                        <input type="hidden" name="standar_mutu_id" value="{{ $standar->standar_mutu_id }}">
                                        <input type="hidden" name="periode_id" value="{{ $periode_id }}">

                                        <div class="p-4 space-y-4">
                                            <div>
                                                <label class="block mb-2 text-sm font-medium text-gray-900">Nama Sub Standar</label>
                                                <input type="text" name="nama_sub_standar"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end p-4 border-t gap-2">
                                            <button type="button"
                                                data-modal-hide="modal-tambah-sub-{{ $standar->standar_mutu_id }}"
                                                class="px-4 py-2 text-sm bg-gray-500 hover:bg-gray-700 text-white rounded">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 text-sm bg-green-500 hover:bg-green-700 text-white rounded">
                                                Simpan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

                @else
                <div class="p-4 text-sm text-yellow-800 bg-yellow-50 rounded-lg">
                    Belum ada pemetaan standar mutu untuk UPT ini.
                </div>
                @endif
            </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = @json(session('active_tab'));
            const targetScroll = @json(session('target_scroll'));
            const openAccordion = @json(session('open_accordion'));

            function openTargetAccordion() {
                if (!openAccordion) return;

                const body = document.getElementById(openAccordion);

                if (body) {
                    body.classList.remove('hidden');

                    const triggerButtons = document.querySelectorAll(`[data-accordion-target="#${openAccordion}"]`);

                    triggerButtons.forEach(button => {
                        button.setAttribute('aria-expanded', 'true');

                        const icon = button.querySelector('[data-accordion-icon]');
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

                    target.classList.add('ring-2', 'ring-blue-400', 'ring-offset-2');

                    setTimeout(() => {
                        target.classList.remove('ring-2', 'ring-blue-400', 'ring-offset-2');
                    }, 2000);
                }
            }

            function runAfterReload() {
                openTargetAccordion();

                setTimeout(() => {
                    scrollToTarget();
                }, 300);
            }

            if (activeTab) {
                const tabButton = document.querySelector(`[data-tabs-target="#${activeTab}"]`);

                if (tabButton) {
                    setTimeout(() => {
                        tabButton.click();
                        setTimeout(runAfterReload, 400);
                    }, 200);
                } else {
                    setTimeout(runAfterReload, 400);
                }
            } else {
                setTimeout(runAfterReload, 400);
            }
        });

        // JS BACK TO TOP
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