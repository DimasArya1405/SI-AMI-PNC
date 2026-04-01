<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('Data Periode') }}
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
                        Tambah Data Periode
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
                <form action="{{ route('admin.periode.tambah') }}" method="post">
                    @csrf
                    @method('post')
                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <div class="col-span-2">
                            <label for="nama_prodi" class="block mb-2.5 text-sm font-medium text-heading">Tahun</label>
                            @php
                                $currentYear = date('Y');
                                // $beforeYear = $currentYear - 2;
                                $count = $currentYear + 3;
                            @endphp
                            <select id="" name="tahun"
                            class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body">
                            <option selected="">Pilih Tahun</option>
                            @for ($i = $currentYear -2; $i < $count; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                            </select>
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
                            Tambah Prodi Baru
                        </button>
                        <button data-modal-hide="modal-tambah" type="button"
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
                    <h3 class="mb-6 text-body">Apakah anda yakin akan menghapus data periode ini?</h3>
                    <form action="{{ route('admin.periode.hapus') }}" method="post">
                        @csrf
                        @method('delete')
                        <input type="text" name="periode_id" id="periode_id" >
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
            $(document).on('click', '.button-hapus', function() {
                let periode_id = $(this).data('id');
                $('#periode_id').val(periode_id);

                $('#modal-hapus').removeClass('hidden').addClass('flex');
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
    @stack('js')
</x-app-layout>
