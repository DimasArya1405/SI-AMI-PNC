<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('Data UPT') }}
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
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Tambah Data UPT
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-tambah">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                {{-- Form --}}
                <form action="{{ route('admin.upt.tambah') }}" method="POST">
                    @csrf
                    @method('post')

                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        {{-- Kategori --}}
                        <div class="col-span-2">
                            <label class="block mb-2.5 text-sm font-medium text-heading">
                                Kategori UPT
                            </label>

                            <div class="flex items-center gap-6">
                                <div class="flex items-center">
                                    <input
                                        type="radio"
                                        name="kategori_upt"
                                        id="kategori_prodi"
                                        value="Prodi"
                                        class="kategori-upt w-4 h-4 text-brand bg-neutral-secondary-medium border-default-medium focus:ring-brand"
                                        required>
                                    <label for="kategori_prodi" class="ms-2 text-sm text-heading">
                                        Prodi
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input
                                        type="radio"
                                        name="kategori_upt"
                                        id="kategori_unit"
                                        value="Unit/Bagian"
                                        class="kategori-upt w-4 h-4 text-brand bg-neutral-secondary-medium border-default-medium focus:ring-brand"
                                        required>
                                    <label for="kategori_unit" class="ms-2 text-sm text-heading">
                                        Unit/Bagian
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Wrapper Prodi --}}
                        <div class="col-span-2 hidden" id="wrapper-prodi">
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Pilih Prodi
                            </label>

                            <button id="dropdownSearchButtonProdi"
                                data-dropdown-toggle="dropdownSearchProdi"
                                data-dropdown-placement="bottom"
                                type="button"
                                class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300">
                                <span id="prodi-selected-text">Pilih prodi</span>
                                <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>

                            <div id="dropdownSearchProdi"
                                class="z-10 hidden bg-white rounded-lg shadow w-full border border-gray-200">
                                <div class="p-3 border-b border-gray-200">
                                    <label class="sr-only" for="input-group-search-prodi">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="input-group-search-prodi"
                                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Cari prodi">
                                    </div>
                                </div>

                                <ul class="max-h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700"
                                    aria-labelledby="dropdownSearchButtonProdi" id="prodi-list">
                                    @foreach ($prodi as $item)
                                    <li class="py-2 prodi-item">
                                        <div class="flex items-center">
                                            <input id="prodi-{{ $item->prodi_id }}"
                                                type="radio"
                                                name="prodi_id"
                                                value="{{ $item->prodi_id }}"
                                                class="prodi-radio w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                            <label for="prodi-{{ $item->prodi_id }}"
                                                class="ms-2 text-sm font-medium text-gray-900 prodi-label">
                                                {{ $item->nama_prodi }}
                                            </label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <p class="mt-2 text-sm text-gray-500">Pilih satu prodi dari data master prodi.</p>
                        </div>

                        {{-- Wrapper Unit/Bagian --}}
                        <div class="col-span-2 hidden" id="wrapper-unit">
                            <div class="mb-4">
                                <label for="nama_upt" class="block mb-2.5 text-sm font-medium text-heading">
                                    Nama UPT
                                </label>
                                <input type="text" name="nama_upt" id="nama_upt"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body">
                            </div>

                            <div>
                                <label for="kode_upt" class="block mb-2.5 text-sm font-medium text-heading">
                                    Kode UPT
                                </label>
                                <input type="text" name="kode_upt" id="kode_upt"
                                    class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body">
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-500 hover:bg-blue-700 box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none transition duration-200 ease-in-out">
                            <svg class="w-4 h-4 me-1.5 -ms-0.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg>
                            Tambah UPT Baru
                        </button>

                        <button data-modal-hide="modal-tambah" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modal-edit" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Edit Data upt
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
                <!-- Modal body -->
                <form action="{{ route('admin.upt.edit') }}" method="post">
                    @csrf
                    @method('put')
                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <input type="hidden" name="upt_id" id="upt_id_edit"
                            class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                            required="">
                        <div class="col-span-2">
                            <label for="nama_upt" class="block mb-2.5 text-sm font-medium text-heading">Nama UPT</label>
                            <input type="text" name="nama_upt" id="nama_upt_edit"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="kode_upt" class="block mb-2.5 text-sm font-medium text-heading">Kode UPT</label>
                            <input type="text" name="kode_upt" id="kode_upt_edit"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="kategori_upt" class="block mb-2.5 text-sm font-medium text-heading">Kategori UPT</label>
                            <input type="text" name="kategori_upt" id="kategori_upt_edit"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center  text-white bg-blue-500 hover:bg-brand-strong box-border border border-transparent focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none hover:bg-blue-700 transition duration-200 ease-in-out">
                            Simpan
                        </button>
                        <button data-modal-hide="modal-edit" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Batal</button>
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
                    <h3 class="mb-6 text-body">Apakah anda yakin akan menghapus data upt ini?</h3>
                    <form action="{{ route('admin.upt.hapus') }}" method="post">
                        @csrf
                        @method('delete')
                        <input type="text" name="upt_id" id="upt_id_hapus" hidden>
                        <div class="flex items-center space-x-4 justify-center">
                            <button data-modal-hide="modal-hapus" type="submit"
                                class="text-white transition duration-300 ease-in-out bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                                Iya, saya yakin
                            </button>
                            <button data-modal-hide="modal-hapus" type="button"
                                class="text-body transition duration-300 ease-in-out bg-white box-border border border-default-medium hover:bg-gray-200 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Tidak,
                                Batal</button>
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
            const kategoriRadios = document.querySelectorAll('.kategori-upt');
            const wrapperProdi = document.getElementById('wrapper-prodi');
            const wrapperUnit = document.getElementById('wrapper-unit');

            const namaUptInput = document.getElementById('nama_upt');
            const kodeUptInput = document.getElementById('kode_upt');

            function toggleKategoriUpt() {
                const selected = document.querySelector('.kategori-upt:checked')?.value;

                if (selected === 'Prodi') {
                    wrapperProdi.classList.remove('hidden');
                    wrapperUnit.classList.add('hidden');

                    namaUptInput.value = '';
                    kodeUptInput.value = '';
                } else if (selected === 'Unit/Bagian') {
                    wrapperProdi.classList.add('hidden');
                    wrapperUnit.classList.remove('hidden');
                } else {
                    wrapperProdi.classList.add('hidden');
                    wrapperUnit.classList.add('hidden');
                }
            }

            kategoriRadios.forEach(radio => {
                radio.addEventListener('change', toggleKategoriUpt);
            });

            toggleKategoriUpt();

            const prodiRadios = document.querySelectorAll('.prodi-radio');
            const prodiSelectedText = document.getElementById('prodi-selected-text');
            const searchProdiInput = document.getElementById('input-group-search-prodi');
            const prodiItems = document.querySelectorAll('.prodi-item');

            function updateProdiSelectedText() {
                const checked = document.querySelector('.prodi-radio:checked');

                if (!checked) {
                    prodiSelectedText.textContent = 'Pilih prodi';
                    return;
                }

                const label = checked.closest('.flex.items-center').querySelector('.prodi-label').textContent.trim();
                prodiSelectedText.textContent = label;
            }

            prodiRadios.forEach(item => {
                item.addEventListener('change', updateProdiSelectedText);
            });

            if (searchProdiInput) {
                searchProdiInput.addEventListener('keyup', function() {
                    const value = this.value.toLowerCase();

                    prodiItems.forEach(item => {
                        const label = item.querySelector('.prodi-label').textContent.toLowerCase();
                        item.style.display = label.includes(value) ? '' : 'none';
                    });
                });
            }

            updateProdiSelectedText();
        });

        $(document).on('click', '.button-edit', function() {
            let upt_id = $(this).attr('data-id');
            let nama_upt = $(this).attr('data-nama-upt');
            let kode_upt = $(this).attr('data-kode-upt');
            let kategori_upt = $(this).attr('data-kategori-upt');

            $('#upt_id_edit').val(upt_id);
            $('#nama_upt_edit').val(nama_upt);
            $('#kode_upt_edit').val(kode_upt);
            $('#kategori_upt_edit').val(kategori_upt);

            $('#modal-edit').removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '.button-hapus', function() {
            let upt_id = $(this).data('id');
            $('#upt_id_hapus').val(upt_id);

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