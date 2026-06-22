<x-app-layout>
    @include('admin.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="p-6 text-gray-900">
                    {{ __('Data Akun Kepala P4MP') }}
                </div>
            </div>

            <div class="relative overflow-x-auto rounded-lg border border-default bg-white shadow-xs">
                <div class="mx-4 flex items-center justify-between border-b border-gray-300 py-4">
                    <button data-modal-target="modal-tambah" data-modal-toggle="modal-tambah"
                        class="flex items-center gap-2 rounded bg-green-500 px-4 py-1 text-white transition duration-200 ease-in-out hover:bg-green-700 focus:outline-none focus:shadow-outline"
                        type="button">
                        <i class="bi bi-plus"></i> <span class="text-sm">Tambah Data</span>
                    </button>

                    <!-- <div class="rounded border border-blue-100 bg-blue-50 px-4 py-2 text-sm text-blue-700">
                        {{ $jumlahAktif }} dari {{ $jumlahKepalaP4mp }} akun Kepala P4MP aktif. Sistem hanya memakai satu akun aktif.
                    </div> -->
                </div>

                <div class="dt-responsive table-responsive p-4 pt-4">
                    {!! $dataTable->table(['class' => 'table table-striped table-bordered align-middle w-100'], true) !!}
                </div>
            </div>
        </div>
    </div>

    <div id="modal-tambah" tabindex="-1" aria-hidden="true"
        class="hidden fixed left-0 right-0 top-0 z-50 min-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <div class="relative rounded-base border border-default bg-white p-4 shadow-sm md:p-6">
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">Tambah Data Kepala P4MP</h3>
                    <button type="button"
                        class="ms-auto inline-flex h-9 w-9 items-center justify-center rounded-base bg-transparent text-sm text-body hover:bg-neutral-tertiary hover:text-heading"
                        data-modal-hide="modal-tambah">
                        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <form action="{{ route('admin.kepala_p4mp.tambah') }}" method="post">
                    @csrf
                    @method('post')
                    <div class="grid grid-cols-2 gap-4 py-4 md:py-6">
                        <div class="col-span-2">
                            <label class="mb-2.5 block text-sm font-medium text-heading">Nama</label>
                            <input type="text" name="name"
                                class="block w-full rounded-base border border-default-medium bg-neutral-secondary-medium px-3 py-2.5 text-sm text-heading shadow-xs focus:border-brand focus:ring-brand"
                                required>
                        </div>
                        <div class="col-span-2">
                            <label class="mb-2.5 block text-sm font-medium text-heading">Email</label>
                            <input type="email" name="email"
                                class="block w-full rounded-base border border-default-medium bg-neutral-secondary-medium px-3 py-2.5 text-sm text-heading shadow-xs focus:border-brand focus:ring-brand"
                                required>
                        </div>
                        <div class="col-span-2">
                            <label class="mb-2.5 block text-sm font-medium text-heading">Password</label>
                            <input type="password" name="password"
                                class="block w-full rounded-base border border-default-medium bg-neutral-secondary-medium px-3 py-2.5 text-sm text-heading shadow-xs focus:border-brand focus:ring-brand"
                                required>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center rounded-base border border-transparent bg-blue-500 px-4 py-2.5 text-sm font-medium leading-5 text-white shadow-xs transition duration-200 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-brand-medium">
                            <svg class="-ms-0.5 me-1.5 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg>
                            Tambah Kepala P4MP Baru
                        </button>
                        <button data-modal-hide="modal-tambah" type="button"
                            class="rounded-base border border-gray-400 bg-white px-4 py-2.5 text-sm font-medium leading-5 text-body shadow-xs transition duration-300 ease-in-out hover:bg-gray-200 hover:text-heading focus:outline-none focus:ring-4 focus:ring-neutral-tertiary">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal-edit" tabindex="-1" aria-hidden="true"
        class="hidden fixed left-0 right-0 top-0 z-50 min-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <div class="relative rounded-base border border-default bg-white p-4 shadow-sm md:p-6">
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">Edit Data Kepala P4MP</h3>
                    <button type="button"
                        class="ms-auto inline-flex h-9 w-9 items-center justify-center rounded-base bg-transparent text-sm text-body hover:bg-neutral-tertiary hover:text-heading"
                        data-modal-hide="modal-edit">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.kepala_p4mp.edit') }}" method="post">
                    @csrf
                    @method('put')
                    <div class="grid grid-cols-2 gap-4 py-4 md:py-6">
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="col-span-2">
                            <label class="mb-2.5 block text-sm font-medium text-heading">Nama</label>
                            <input type="text" name="name" id="name"
                                class="block w-full rounded-base border border-default-medium bg-neutral-secondary-medium px-3 py-2.5 text-sm text-heading shadow-xs focus:border-brand focus:ring-brand"
                                required>
                        </div>
                        <div class="col-span-2">
                            <label class="mb-2.5 block text-sm font-medium text-heading">Email</label>
                            <input type="email" name="email" id="email"
                                class="block w-full rounded-base border border-default-medium bg-neutral-secondary-medium px-3 py-2.5 text-sm text-heading shadow-xs focus:border-brand focus:ring-brand"
                                required>
                        </div>
                        <div class="col-span-2">
                            <label class="mb-2.5 block text-sm font-medium text-heading">Password Baru</label>
                            <input type="password" name="password"
                                class="block w-full rounded-base border border-default-medium bg-neutral-secondary-medium px-3 py-2.5 text-sm text-heading shadow-xs focus:border-brand focus:ring-brand"
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="rounded-base border border-transparent bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-xs transition hover:bg-blue-700">
                            Simpan
                        </button>
                        <button data-modal-hide="modal-edit" type="button"
                            class="rounded-base border border-gray-400 bg-white px-4 py-2.5 text-sm font-medium text-body transition hover:bg-gray-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal-hapus" tabindex="-1"
        class="hidden fixed left-0 right-0 top-0 z-50 min-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <div class="relative rounded-base border border-default bg-white p-4 shadow-sm md:p-6">
                <button type="button"
                    class="absolute end-2.5 top-3 ms-auto inline-flex h-9 w-9 items-center justify-center rounded-base bg-transparent text-sm text-body hover:bg-neutral-tertiary hover:text-heading"
                    data-modal-hide="modal-hapus">
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg class="mx-auto mb-4 h-12 w-12 text-fg-disabled" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 id="text-modal-hapus" class="mb-6 text-body">Apakah anda yakin akan menghapus akun Kepala P4MP ini?</h3>
                    <form action="{{ route('admin.kepala_p4mp.hapus') }}" method="post">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="user_id" id="user_id_hapus">
                        <div class="flex items-center justify-center space-x-4">
                            <button data-modal-hide="modal-hapus" type="submit"
                                class="rounded-base border border-transparent bg-blue-500 px-4 py-2.5 text-sm font-medium leading-5 text-white shadow-xs transition duration-300 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-danger-medium">
                                Iya, saya yakin
                            </button>
                            <button data-modal-hide="modal-hapus" type="button"
                                class="rounded-base border border-default-medium bg-white px-4 py-2.5 text-sm font-medium leading-5 text-body shadow-xs transition duration-300 ease-in-out hover:bg-gray-200 hover:text-heading focus:outline-none focus:ring-4 focus:ring-neutral-tertiary">
                                Tidak, Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-aktivasi" tabindex="-1"
        class="hidden fixed left-0 right-0 top-0 z-50 min-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <div class="relative rounded-base border border-default bg-white p-4 shadow-sm md:p-6">
                <button type="button"
                    class="absolute end-2.5 top-3 ms-auto inline-flex h-9 w-9 items-center justify-center rounded-base bg-transparent text-sm text-body hover:bg-neutral-tertiary hover:text-heading"
                    data-modal-hide="modal-aktivasi">
                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg class="mx-auto mb-4 h-12 w-12 text-fg-disabled" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 id="text-modal-aktivasi" class="mb-6 text-body">Apakah anda yakin ingin mengubah status akun Kepala P4MP ini?</h3>
                    <form action="{{ route('admin.kepala_p4mp.aktivasi') }}" method="post">
                        @csrf
                        @method('put')
                        <input type="hidden" name="user_id" id="user_id_aktivasi">
                        <div class="flex items-center justify-center space-x-4">
                            <button type="submit"
                                class="rounded-base border border-transparent bg-blue-500 px-4 py-2.5 text-sm font-medium leading-5 text-white shadow-xs transition duration-300 ease-in-out hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-brand-medium">
                                Iya, lanjutkan
                            </button>
                            <button data-modal-hide="modal-aktivasi" type="button"
                                class="rounded-base border border-default-medium bg-white px-4 py-2.5 text-sm font-medium leading-5 text-body shadow-xs transition duration-300 ease-in-out hover:bg-gray-200 hover:text-heading focus:outline-none focus:ring-4 focus:ring-neutral-tertiary">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(document).on('click', '.button-edit', function() {
                $('#user_id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#email').val($(this).data('email'));
                $('#modal-edit').removeClass('hidden').addClass('flex');
            });

            $(document).on('click', '.button-hapus', function() {
                $('#user_id_hapus').val($(this).data('id'));
                $('#text-modal-hapus').text('Apakah anda yakin akan menghapus akun Kepala P4MP atas nama ' + $(this).data('name') + '?');
                $('#modal-hapus').removeClass('hidden').addClass('flex');
            });

            $(document).on('click', '.button-aktivasi', function() {
                const status = Number($(this).data('status'));
                const name = $(this).data('name');
                const aksi = status === 1 ? 'menonaktifkan' : 'mengaktifkan';

                $('#user_id_aktivasi').val($(this).data('id'));
                $('#text-modal-aktivasi').text('Apakah anda yakin ingin ' + aksi + ' akun Kepala P4MP atas nama ' + name + '?');
                $('#modal-aktivasi').removeClass('hidden').addClass('flex');
            });

            $(document).on('click', '[data-modal-hide="modal-edit"]', function() {
                $('#modal-edit').removeClass('flex').addClass('hidden');
            });

            $(document).on('click', '[data-modal-hide="modal-hapus"]', function() {
                $('#modal-hapus').removeClass('flex').addClass('hidden');
            });

            $(document).on('click', '[data-modal-hide="modal-aktivasi"]', function() {
                $('#modal-aktivasi').removeClass('flex').addClass('hidden');
            });

            $(document).on('click', '[data-modal-hide="modal-tambah"]', function() {
                $('#modal-tambah').removeClass('flex').addClass('hidden');
            });

            $(document).on('click', '[data-modal-target="modal-tambah"]', function() {
                $('#modal-tambah').removeClass('hidden').addClass('flex');
            });
        </script>
        {!! $dataTable->scripts() !!}
    @endpush
</x-app-layout>
