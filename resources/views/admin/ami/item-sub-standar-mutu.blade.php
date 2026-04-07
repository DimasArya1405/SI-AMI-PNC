<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex items-center justify-between">

                    <div>
                        Data Item
                        {{ $sub_standar->standar_mutu->nama_standar_mutu ?? '-' }}
                        →
                        {{ $sub_standar->nama_sub_standar ?? '-' }}
                    </div>

                    <div class="flex gap-2">

                        <a href="{{ route('admin.ami.sub_standar_mutu', $sub_standar->standar_mutu_id) }}"
                            class="flex items-center gap-2 bg-gray-500 hover:bg-gray-700 text-white text-sm px-3 py-1 rounded">
                            <i class="bi bi-arrow-left"></i>
                            Sub Standar
                        </a>

                        <a href="{{ route('admin.ami.standar_mutu') }}"
                            class="flex items-center gap-2 bg-blue-500 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded">
                            <i class="bi bi-house"></i>
                            Standar Mutu
                        </a>

                    </div>

                </div>
            </div>
            <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                <div class="flex justify-between items-center py-4 mx-4 border-b border-gray-300">
                    <button data-modal-target="modal-tambah" data-modal-toggle="modal-tambah"
                        class="flex items-center gap-2 bg-green-500 hover:bg-green-700 transition duration-200 ease-in-out text-white py-1 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="button">
                        <i class="bi bi-plus"></i> <span class="text-sm">Tambah Data</span>
                    </button>
                </div>

                <div class="dt-responsive table-responsive p-4 pt-4">
                    {!! $dataTable->table(['class' => 'table table-striped table-bordered align-middle w-100'], true) !!}
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modal-tambah" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Tambah Pernyataan Sub Standar Mutu
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-tambah">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form action="{{ route('admin.item_sub_standar_mutu.tambah') }}" method="post">
                    @csrf
                    @method('post')
                    <input type="hidden" name="sub_standar_id" value="{{ $sub_standar->sub_standar_id }}">
                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <div class="col-span-2">
                            <label for="name" class="block mb-2.5 text-sm font-medium text-heading">Pertanyaan dan Pernyataan</label>
                            <textarea name="nama_item" id="name" rows="4"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                placeholder=""
                                required></textarea>
                        </div>

                        <input type="hidden" name="sub_standar_id" value="{{ $sub_standar->sub_standar_id }}">

                        <div class="col-span-2">
                            <label class="block mb-2.5 text-sm font-medium text-heading">Parent Item</label>

                            {{-- value yang dikirim ke backend --}}
                            <input type="hidden" name="parent_item_id" id="parent_item_id">

                            {{-- tombol dropdown --}}
                            <button id="dropdownParentButton" data-dropdown-toggle="dropdownParentMenu"
                                data-dropdown-placement="bottom"
                                class="w-full flex items-center justify-between bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base px-3 py-2.5"
                                type="button">
                                <span id="dropdownParentLabel">Item Utama</span>
                                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m1 1 4 4 4-4" />
                                </svg>
                            </button>

                            {{-- isi dropdown --}}
                            <div id="dropdownParentMenu"
                                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-full border border-gray-200">

                                <div class="p-3">
                                    <label for="search_parent_item" class="sr-only">Cari Parent Item</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="search_parent_item"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full ps-10 p-2.5"
                                            placeholder="Cari parent item...">
                                    </div>
                                </div>

                                <ul class="max-h-60 overflow-y-auto text-sm text-gray-700" id="parent_item_list">
                                    <li>
                                        <button type="button"
                                            class="parent-item-option inline-flex w-full px-4 py-2 hover:bg-gray-100"
                                            data-value="" data-label="Item Utama">
                                            Item Utama
                                        </button>
                                    </li>

                                    @foreach ($parentItems as $parent)
                                    <li class="parent-item-row">
                                        <button type="button"
                                            class="parent-item-option inline-flex w-full px-4 py-2 hover:bg-gray-100 text-left"
                                            data-value="{{ $parent->item_sub_standar_id }}"
                                            data-label="{{ $parent->nama_item }}">
                                            {{ $parent->nama_item }}
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center  text-white bg-blue-500 hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none hover:bg-blue-700 transition duration-200 ease-in-out">
                            <svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg>
                            Tambah
                        </button>
                        <button data-modal-hide="modal-tambah" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modal-edit" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Edit Pertanyaan dan Pernyataan
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-edit">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <form action="{{ route('admin.item_sub_standar_mutu.edit') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="item_sub_standar_id" id="item_sub_standar_id">

                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <div class="col-span-2">
                            <label for="nama_item" class="block mb-2.5 text-sm font-medium text-heading">
                                Pertanyaan dan Pernyataan
                            </label>
                            <textarea name="nama_item" id="nama_item" rows="4"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required></textarea>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-500 border border-transparent focus:ring-4 shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none hover:bg-blue-700 transition duration-200 ease-in-out">
                            Simpan
                        </button>

                        <button data-modal-hide="modal-edit" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div id="modal-hapus" tabindex="-1"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <button type="button"
                    class="absolute top-3 end-2.5 text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="modal-hapus">
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
                    <h3 class="mb-6 text-body">Apakah anda yakin akan menghapus data ini?</h3>
                    <form action="{{ route('admin.item_sub_standar_mutu.hapus') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <input type="hidden" name="item_sub_standar_id" id="item_sub_standar_id_hapus">

                        <div class="flex items-center space-x-4 justify-center">
                            <button type="submit"
                                class="text-white transition duration-300 ease-in-out bg-blue-500 hover:bg-blue-700 shadow-xs font-medium rounded-base text-sm px-4 py-2.5">
                                Iya, saya yakin
                            </button>

                            <button data-modal-hide="modal-hapus" type="button"
                                class="text-body bg-white border border-default-medium hover:bg-gray-200 rounded-base text-sm px-4 py-2.5">
                                Tidak, Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS --}}
    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== MODAL TAMBAH =====
            const searchInput = document.getElementById('search_parent_item');
            const hiddenInput = document.getElementById('parent_item_id');
            const label = document.getElementById('dropdownParentLabel');
            const options = document.querySelectorAll('.parent-item-option');
            const rows = document.querySelectorAll('#parent_item_list li');

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const keyword = this.value.toLowerCase();

                    rows.forEach((row) => {
                        const text = row.innerText.toLowerCase();
                        row.style.display = text.includes(keyword) ? '' : 'none';
                    });
                });
            }

            options.forEach((option) => {
                option.addEventListener('click', function() {
                    hiddenInput.value = this.dataset.value;
                    label.textContent = this.dataset.label;
                });
            });

            // ===== MODAL EDIT =====
            const searchInputEdit = document.getElementById('search_parent_item_edit');
            const hiddenInputEdit = document.getElementById('parent_item_id_edit');
            const labelEdit = document.getElementById('dropdownParentLabelEdit');
            const optionsEdit = document.querySelectorAll('.parent-item-option-edit');
            const rowsEdit = document.querySelectorAll('#parent_item_list_edit li');

            if (searchInputEdit) {
                searchInputEdit.addEventListener('keyup', function() {
                    const keyword = this.value.toLowerCase();

                    rowsEdit.forEach((row) => {
                        const text = row.innerText.toLowerCase();
                        row.style.display = text.includes(keyword) ? '' : 'none';
                    });
                });
            }

            optionsEdit.forEach((option) => {
                option.addEventListener('click', function() {
                    hiddenInputEdit.value = this.dataset.value;
                    labelEdit.textContent = this.dataset.label;
                });
            });

            // ===== isi data saat klik tombol edit =====
            $(document).on('click', '.button-edit', function() {
                let itemId = $(this).data('id');
                let namaItem = $(this).data('nama_item');
                let parentItemId = $(this).data('parent_item_id') ?? '';
                let parentItemLabel = $(this).data('parent_item_label') ?? 'Item Utama';

                $('#item_sub_standar_id').val(itemId);
                $('#nama_item').val(namaItem);
                $('#parent_item_id_edit').val(parentItemId);
                $('#dropdownParentLabelEdit').text(parentItemLabel);
            });
        });

        $(document).on('click', '.button-edit', function() {
            let item_sub_standar_id = $(this).data('id');
            let nama_item = $(this).data('nama');
            let parent_item_id = $(this).data('parent');

            $('#item_sub_standar_id').val(item_sub_standar_id);
            $('#nama_item').val(nama_item);
            $('#parent_item_id').val(parent_item_id);

            $('#modal-edit').removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '.button-hapus', function() {

            let item_sub_standar_id = $(this).data('id');

            $('#item_sub_standar_id_hapus').val(item_sub_standar_id);

            $('#modal-hapus').removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '[data-modal-hide="modal-edit"]', function() {
            $('#modal-edit').removeClass('flex').addClass('hidden');
        });

        $(document).on('click', '[data-modal-hide="modal-hapus"]', function() {
            $('#modal-hapus').removeClass('flex').addClass('hidden');
        });

        $(document).on('click', '[data-modal-hide="modal-tambah"]', function() {
            $('#modal-tambah').removeClass('flex').addClass('hidden');
        });

        $(document).on('click', '[data-modal-target="modal-tambah"]', function() {
            $('#modal-tambah').removeClass('hidden').addClass('flex');
        });
    </script>
    @endpush
    {!! $dataTable->scripts() !!}
</x-app-layout>