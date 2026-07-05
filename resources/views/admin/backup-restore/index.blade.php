<x-app-layout>
    @include('admin.sidebar')

    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-semibold">Backup & Restore Data</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Kelola salinan database SIAMI untuk kebutuhan pengamanan dan pemulihan data.
                    </p>
                </div>
            </div>

            @if (session('success'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid gap-4 lg:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <i class="bi bi-database-down text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold text-gray-900">Backup Database</h2>
                            <p class="mt-1 text-sm text-gray-500">
                                Buat salinan seluruh tabel database dalam format SQL.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('admin.backup_restore.store') }}" method="POST" class="mt-6">
                        @csrf
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 sm:w-auto">
                            <i class="bi bi-plus-circle mr-2"></i>
                            Buat Backup Sekarang
                        </button>
                    </form>
                </div>

                <div class="rounded-lg border border-orange-200 bg-orange-50 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-orange-600">
                            <i class="bi bi-arrow-counterclockwise text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold text-gray-900">Restore Database</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Restore akan mengganti data database sesuai isi file SQL. Sistem akan membuat backup otomatis sebelum restore.
                            </p>
                        </div>
                    </div>

                    <form id="form-restore-database" action="{{ route('admin.backup_restore.restore') }}" method="POST" enctype="multipart/form-data"
                        class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label for="backup_file" class="mb-2 block text-sm font-medium text-gray-700">
                                File Backup SQL
                            </label>
                            <input type="file" name="backup_file" id="backup_file" accept=".sql,.txt" required
                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <button type="button" id="btn-open-restore-modal"
                            class="inline-flex w-full items-center justify-center rounded-md bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700 sm:w-auto">
                            <i class="bi bi-upload mr-2"></i>
                            Restore Database
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Backup</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        File backup tersimpan di server dan dapat diunduh kapan saja.
                    </p>
                </div>

                <div class="overflow-x-auto p-6">
                    <table class="w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Nama File</th>
                                <th class="px-4 py-3">Ukuran</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($backups as $backup)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $backup['name'] }}</td>
                                    <td class="px-4 py-3">{{ $backup['size'] }}</td>
                                    <td class="px-4 py-3">{{ $backup['created_at'] }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.backup_restore.download', $backup['name']) }}"
                                                class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-green-700">
                                                <i class="bi bi-download mr-1"></i>
                                                Unduh
                                            </a>
                                            <form action="{{ route('admin.backup_restore.destroy', $backup['name']) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus file backup ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                                                    <i class="bi bi-trash mr-1"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada file backup.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-restore-database" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/50 p-4">
        <div class="relative w-full max-w-md">
            <div class="relative rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 bg-orange-600 px-5 py-4 text-white">
                    <h3 class="text-lg font-semibold">Konfirmasi Restore Database</h3>
                    <button type="button" data-restore-modal-close
                        class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-white/15 text-white transition hover:bg-white/25">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="space-y-4 p-5">
                    <div class="rounded-md border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
                        Restore akan menimpa data database saat ini sesuai isi file backup yang dipilih.
                        Sistem akan membuat backup otomatis sebelum restore dilakukan.
                    </div>

                    <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">File restore</p>
                        <p id="restore-file-name" class="mt-1 break-all text-sm font-semibold text-gray-900">-</p>
                    </div>

                    <p class="text-sm text-gray-600">
                        Pastikan file backup yang dipilih benar sebelum melanjutkan.
                    </p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-200 p-5 sm:flex-row sm:justify-end">
                    <button type="button" data-restore-modal-close
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">
                        Batal
                    </button>
                    <button type="button" id="btn-confirm-restore"
                        class="inline-flex items-center justify-center rounded-md bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700">
                        <i class="bi bi-arrow-counterclockwise mr-2"></i>
                        Ya, Restore
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            const restoreModal = $('#modal-restore-database');
            const restoreForm = $('#form-restore-database');
            const restoreFileInput = $('#backup_file');

            $('#btn-open-restore-modal').on('click', function() {
                const file = restoreFileInput[0].files[0];

                if (!file) {
                    restoreFileInput[0].reportValidity();
                    return;
                }

                $('#restore-file-name').text(file.name);
                restoreModal.removeClass('hidden').addClass('flex');
            });

            $('[data-restore-modal-close]').on('click', function() {
                restoreModal.removeClass('flex').addClass('hidden');
            });

            $('#btn-confirm-restore').on('click', function() {
                $(this).prop('disabled', true).addClass('opacity-70 cursor-not-allowed').html(
                    '<i class="bi bi-hourglass-split mr-2"></i>Memproses...'
                );
                restoreForm.submit();
            });
        </script>
    @endpush
</x-app-layout>
