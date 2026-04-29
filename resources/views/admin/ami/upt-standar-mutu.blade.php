<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('Data Pemetaan Standar Mutu') }}
                </div>
            </div>
            <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                <div class="flex justify-between items-center py-4 mx-4 border-b border-gray-300">
                    <div class="flex gap-2">
                        <button data-modal-target="modal-tambah" data-modal-toggle="modal-tambah"
                            class="flex items-center gap-2 bg-green-500 hover:bg-green-700 transition duration-200 ease-in-out text-white py-1 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="button">
                            <i class="bi bi-plus"></i> <span class="text-sm">Tambah Pemetaan</span>
                        </button>
                        <button
                            data-modal-target="modal-import"
                            data-modal-toggle="modal-import"
                            class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 transition duration-200 ease-in-out text-white py-1 px-4 rounded focus:outline-none focus:shadow-outline">
                            Import Excel
                        </button>
                        <button data-modal-target="modal-copy-periode" data-modal-toggle="modal-copy-periode"
                            class="flex items-center gap-2 bg-blue-500 hover:bg-blue-700 transition duration-200 ease-in-out text-white py-1 px-4 rounded"
                            type="button">
                            <i class="bi bi-files"></i> <span class="text-sm">Copy Periode</span>
                        </button>
                    </div>
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
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Tambah Pemetaan Standar Mutu
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
                <form action="{{ route('admin.upt_standar_mutu.tambah') }}" method="POST">
                    @csrf

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Pilih Periode
                        </label>

                        <select name="periode_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                            required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach ($periodeList as $periode)
                            <option value="{{ $periode->id }}">
                                {{ $periode->tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="py-4 md:py-6 space-y-5">
                        {{-- Target pemetaan --}}
                        <div>
                            <label class="block mb-2.5 text-sm font-medium text-heading">Target Pemetaan</label>

                            <div class="space-y-3">
                                <label class="flex items-center gap-3">
                                    <input type="radio" name="target_type" value="all_prodi"
                                        class="target-type w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                        checked>
                                    <span class="text-sm text-heading">Semua Prodi</span>
                                </label>

                                <label class="flex items-center gap-3">
                                    <input type="radio" name="target_type" value="unit_bagian"
                                        class="target-type w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="text-sm text-heading">Unit/Bagian Tertentu</span>
                                </label>
                            </div>
                        </div>

                        {{-- Info prodi --}}
                        <div id="info-prodi"
                            class="p-4 text-sm text-blue-700 bg-blue-50 rounded-lg">
                            Standar yang dipilih akan diterapkan ke semua UPT dengan kategori <strong>Prodi</strong>.
                        </div>

                        {{-- Multi select Unit/Bagian --}}
                        <div id="unit-bagian-wrapper" class="hidden">
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Pilih Unit/Bagian
                            </label>

                            <button id="dropdownSearchButtonUnit"
                                data-dropdown-toggle="dropdownSearchUnit"
                                data-dropdown-placement="bottom"
                                type="button"
                                class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300">
                                <span id="unit-selected-text">Pilih unit/bagian</span>
                                <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>

                            <div id="dropdownSearchUnit"
                                class="z-10 hidden bg-white rounded-lg shadow w-full border border-gray-200">
                                <div class="p-3 border-b border-gray-200">
                                    <label class="sr-only" for="input-group-search-unit">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="input-group-search-unit"
                                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Cari unit/bagian">
                                    </div>
                                </div>

                                <ul class="max-h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700"
                                    aria-labelledby="dropdownSearchButtonUnit" id="unit-list">

                                    <li class="py-2 border-b border-gray-100">
                                        <div class="flex items-center">
                                            <input id="checkbox-all-unit" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                            <label for="checkbox-all-unit"
                                                class="ms-2 text-sm font-medium text-gray-900">
                                                Pilih Semua
                                            </label>
                                        </div>
                                    </li>

                                    @foreach ($uptUnitBagian as $upt)
                                    <li class="py-2 unit-item">
                                        <div class="flex items-center">
                                            <input id="unit-{{ $upt->upt_id }}"
                                                type="checkbox"
                                                name="upt_ids[]"
                                                value="{{ $upt->upt_id }}"
                                                class="unit-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                            <label for="unit-{{ $upt->upt_id }}"
                                                class="ms-2 text-sm font-medium text-gray-900 unit-label">
                                                {{ $upt->nama_upt }}
                                            </label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <p class="mt-2 text-sm text-gray-500">Bisa pilih satu atau lebih unit/bagian.</p>
                        </div>

                        {{-- Multi select Standar Mutu --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Pilih Standar Mutu
                            </label>

                            <button id="dropdownSearchButtonStandar"
                                data-dropdown-toggle="dropdownSearchStandar"
                                data-dropdown-placement="bottom"
                                type="button"
                                class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300">
                                <span id="standar-selected-text">Pilih standar mutu</span>
                                <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>

                            <div id="dropdownSearchStandar"
                                class="z-10 hidden bg-white rounded-lg shadow w-full border border-gray-200">
                                <div class="p-3 border-b border-gray-200">
                                    <label class="sr-only" for="input-group-search-standar">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="input-group-search-standar"
                                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Cari standar mutu">
                                    </div>
                                </div>

                                <ul class="max-h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700"
                                    aria-labelledby="dropdownSearchButtonStandar" id="standar-list">

                                    <li class="py-2 border-b border-gray-100">
                                        <div class="flex items-center">
                                            <input id="checkbox-all-standar" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                            <label for="checkbox-all-standar"
                                                class="ms-2 text-sm font-medium text-gray-900">
                                                Pilih Semua
                                            </label>
                                        </div>
                                    </li>

                                    @foreach ($standarMutu as $standar)
                                    <li class="py-2 standar-item">
                                        <div class="flex items-center">
                                            <input id="standar-{{ $standar->standar_mutu_id }}"
                                                type="checkbox"
                                                name="standar_mutu_ids[]"
                                                value="{{ $standar->standar_mutu_id }}"
                                                class="standar-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                            <label for="standar-{{ $standar->standar_mutu_id }}"
                                                class="ms-2 text-sm font-medium text-gray-900 standar-label">
                                                {{ $standar->nama_standar_mutu }}
                                            </label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <p class="mt-2 text-sm text-gray-500">Bisa pilih lebih dari satu standar mutu.</p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-500 hover:bg-blue-700 border border-transparent focus:ring-4 focus:ring-blue-300 shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none transition duration-200 ease-in-out">
                            Simpan Pemetaan
                        </button>

                        <button data-modal-hide="modal-tambah" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
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
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Edit Pemetaan Standar Mutu
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-edit">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <form action="{{ route('admin.upt_standar_mutu.edit') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="upt_id" id="edit_upt_id">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Pilih Periode
                        </label>

                        <select name="periode_id" id="edit_periode_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                            required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach ($periodeList as $periode)
                            <option value="{{ $periode->id }}">
                                {{ $periode->tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="py-4 md:py-6 space-y-5">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nama UPT</label>
                            <input type="text" id="edit_nama_upt"
                                class="w-full px-4 py-2.5 text-sm text-gray-900 bg-gray-100 border border-gray-300 rounded-lg"
                                readonly>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">
                                Pilih Standar Mutu
                            </label>

                            <button id="dropdownSearchButtonStandarEdit"
                                data-dropdown-toggle="dropdownSearchStandarEdit"
                                data-dropdown-placement="bottom"
                                type="button"
                                class="flex items-center justify-between w-full px-4 py-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300">
                                <span id="standar-edit-selected-text">Pilih standar mutu</span>
                                <svg class="w-2.5 h-2.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 4 4 4-4" />
                                </svg>
                            </button>

                            <div id="dropdownSearchStandarEdit"
                                class="z-10 hidden bg-white rounded-lg shadow w-full border border-gray-200">
                                <div class="p-3 border-b border-gray-200">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="text" id="input-group-search-standar-edit"
                                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Cari standar mutu">
                                    </div>
                                </div>

                                <ul class="max-h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700"
                                    aria-labelledby="dropdownSearchButtonStandarEdit" id="standar-edit-list">

                                    <li class="py-2 border-b border-gray-100">
                                        <div class="flex items-center">
                                            <input id="checkbox-all-standar-edit" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                            <label for="checkbox-all-standar-edit"
                                                class="ms-2 text-sm font-medium text-gray-900">
                                                Pilih Semua
                                            </label>
                                        </div>
                                    </li>

                                    @foreach ($standarMutu as $standar)
                                    <li class="py-2 standar-edit-item">
                                        <div class="flex items-center">
                                            <input id="edit-standar-{{ $standar->standar_mutu_id }}"
                                                type="checkbox"
                                                name="standar_mutu_ids[]"
                                                value="{{ $standar->standar_mutu_id }}"
                                                class="standar-edit-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                                            <label for="edit-standar-{{ $standar->standar_mutu_id }}"
                                                class="ms-2 text-sm font-medium text-gray-900 standar-edit-label">
                                                {{ $standar->nama_standar_mutu }}
                                            </label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <p class="mt-2 text-sm text-gray-500">Bisa pilih lebih dari satu standar mutu.</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center text-white bg-yellow-500 hover:bg-yellow-700 border border-transparent focus:ring-4 focus:ring-yellow-300 shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none transition duration-200 ease-in-out">
                            Update Pemetaan
                        </button>

                        <button data-modal-hide="modal-edit" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
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

                    <h3 class="mb-2 text-body">Apakah anda yakin akan menghapus data ini?</h3>
                    <p class="mb-6 text-sm text-gray-500">
                        Semua pemetaan standar pada UPT ini akan dihapus.
                    </p>

                    <form action="{{ route('admin.upt_standar_mutu.hapus') }}" method="post">
                        @csrf
                        @method('delete')

                        <input type="hidden" name="upt_id" id="upt_id_hapus">
                        <input type="hidden" name="periode_id" id="periode_id_hapus">

                        <div class="flex items-center space-x-4 justify-center">
                            <button data-modal-hide="modal-hapus" type="submit"
                                class="text-white transition duration-300 ease-in-out bg-blue-500 box-border border border-transparent hover:bg-blue-700 focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                                Iya, saya yakin
                            </button>

                            <button data-modal-hide="modal-hapus" type="button"
                                class="text-body transition duration-300 ease-in-out bg-white box-border border border-default-medium hover:bg-gray-200 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">
                                Tidak, Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Copy Periode --}}
    <div id="modal-copy-periode" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">

                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Copy Pemetaan dari Periode Sebelumnya
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-copy-periode">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.upt_standar_mutu.copy_periode') }}" method="POST">
                    @csrf

                    <div class="py-4 md:py-6 space-y-5">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Periode Sumber</label>
                            <select name="periode_sumber_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                required>
                                <option value="">-- Pilih Periode Sumber --</option>
                                @foreach ($periodeList as $periode)
                                <option value="{{ $periode->id }}">{{ $periode->tahun }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Periode Tujuan</label>
                            <select name="periode_tujuan_id"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                                required>
                                <option value="">-- Pilih Periode Tujuan --</option>
                                @foreach ($periodeList as $periode)
                                <option value="{{ $periode->id }}">{{ $periode->tahun }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="p-4 text-sm text-blue-700 bg-blue-50 rounded-lg">
                            Sistem akan menyalin seluruh pemetaan standar, sub standar, dan item dari periode sumber ke periode tujuan.
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 border-t border-default pt-4 md:pt-6">
                        <button type="submit"
                            class="inline-flex items-center text-white bg-blue-500 hover:bg-blue-700 border border-transparent focus:ring-4 focus:ring-blue-300 shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none transition duration-200 ease-in-out">
                            Copy Pemetaan
                        </button>

                        <button data-modal-hide="modal-copy-periode" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium rounded-base text-sm px-4 py-2.5 focus:outline-none">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <div id="modal-import" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">

        <div class="relative w-full max-w-lg p-4">
            <div class="relative bg-white shadow-lg">

                <div class="flex items-center justify-between p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Import Excel Standar Mutu
                    </h3>

                    <button type="button"
                        class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8"
                        data-modal-hide="modal-import">
                        ✕
                    </button>
                </div>

                <form action="{{ route('admin.upt_standar_mutu.import') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="p-6 space-y-5">
                    @csrf

                    {{-- Periode --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Pilih Periode
                        </label>
                        <select name="periode_id"
                            class="w-full border border-gray-300 rounded-lg p-3"
                            required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach ($periodeList as $periode)
                            <option value="{{ $periode->id }}">
                                {{ $periode->tahun }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Standar Mutu Multi Select + Pilih Semua --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Pilih Standar Mutu
                        </label>

                        <button id="dropdownStandarButton"
                            data-dropdown-toggle="dropdownStandar"
                            data-dropdown-placement="bottom"
                            type="button"
                            class="w-full text-left border border-gray-300 bg-white hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-3 inline-flex items-center justify-between">

                            <span id="selectedStandarText">
                                -- Pilih Standar Mutu --
                            </span>

                            <svg class="w-2.5 h-2.5 ms-3"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 10 6">
                                <path stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>

                        <div id="dropdownStandar"
                            class="z-50 hidden bg-white rounded-lg shadow w-full border border-gray-200">

                            {{-- Search --}}
                            <div class="p-3 border-b">
                                <input type="text"
                                    id="searchStandar"
                                    placeholder="Cari standar mutu..."
                                    class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            {{-- Pilih Semua --}}
                            <div class="p-3 border-b bg-gray-50">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                        id="checkAllStandar"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded">

                                    <span class="text-sm font-semibold text-gray-700">
                                        Pilih Semua
                                    </span>
                                </label>
                            </div>

                            {{-- List Standar --}}
                            <ul id="standarList"
                                class="max-h-64 overflow-y-auto text-sm text-gray-700 p-3 space-y-2">

                                @foreach ($standarMutu as $standar)
                                <li class="standar-item">
                                    <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-2 rounded">

                                        <input type="checkbox"
                                            name="standar_mutu_ids[]"
                                            value="{{ $standar->standar_mutu_id }}"
                                            class="standar-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded">

                                        <span>
                                            {{ $standar->nama_standar_mutu }}
                                        </span>
                                    </label>
                                </li>
                                @endforeach

                            </ul>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            Bisa pilih lebih dari satu standar mutu atau pilih semua.
                        </p>
                    </div>

                    {{-- Target Import --}}
                    <div>
                        <label class="block mb-3 text-sm font-medium text-gray-700">
                            Target Import
                        </label>

                        <div class="flex flex-col gap-3">

                            {{-- Radio Semua Prodi --}}
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio"
                                    name="target_type"
                                    id="target_all_prodi"
                                    value="all_prodi"
                                    checked
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">

                                <span class="text-sm text-gray-700">
                                    Semua Prodi
                                </span>
                            </label>

                            {{-- Radio Unit / Bagian --}}
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio"
                                    name="target_type"
                                    id="target_unit_bagian"
                                    value="unit_bagian"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">

                                <span class="text-sm text-gray-700">
                                    Unit / Bagian
                                </span>
                            </label>

                        </div>
                    </div>

                    {{-- Info Semua Prodi --}}
                    {{-- <div id="info-prodi"
                        class="p-4 text-sm text-blue-700 bg-blue-50 rounded-lg">
                        Standar yang dipilih akan diterapkan ke semua UPT dengan kategori
                        <strong>Prodi</strong>.
                    </div> --}}

                    {{-- Unit / Bagian --}}
                    <div id="wrapper-upt-unit" class="hidden">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Pilih Unit / Bagian
                        </label>

                        <select name="upt_ids[]"
                            disabled
                            class="w-full border border-gray-300 rounded-lg p-3">
                            <option value="">-- Pilih Unit / Bagian --</option>
                            @foreach ($uptUnitBagian as $upt)
                            <option value="{{ $upt->upt_id }}">
                                {{ $upt->nama_upt }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- File --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Upload File Excel
                        </label>

                        <input type="file"
                            name="file_excel"
                            accept=".xlsx,.xls"
                            class="w-full border border-gray-300 rounded-lg p-2"
                            required>
                    </div>

                    <div class="flex justify-start gap-3 pt-4 border-t">
                        <button type="submit"
                            class="px-5 py-2.5 text-sm text-white bg-blue-600 hover:bg-blue-700">
                            Import Excel
                        </button>

                        <button type="button"
                            data-modal-hide="modal-import"
                            class="px-5 py-2.5 text-sm border">
                            Batal
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- JS --}}
    @push('js')
    <script>
        // JS MODAL IMPORT
        document.addEventListener('DOMContentLoaded', function() {
            const standarCheckboxes = document.querySelectorAll('.standar-checkbox');
            const checkAllStandar = document.getElementById('checkAllStandar');
            const selectedStandarText = document.getElementById('selectedStandarText');
            const searchStandar = document.getElementById('searchStandar');
            const standarItems = document.querySelectorAll('.standar-item');

            function updateStandarText() {
                const checked = document.querySelectorAll('.standar-checkbox:checked');

                if (checked.length === 0) {
                    selectedStandarText.textContent = '-- Pilih Standar Mutu --';
                } else if (checked.length === standarCheckboxes.length) {
                    selectedStandarText.textContent = 'Semua Standar Dipilih';
                } else {
                    selectedStandarText.textContent =
                        checked.length + ' standar dipilih';
                }
            }

            checkAllStandar.addEventListener('change', function() {
                standarCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });

                updateStandarText();
            });

            standarCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const total = standarCheckboxes.length;
                    const checked = document.querySelectorAll('.standar-checkbox:checked').length;

                    checkAllStandar.checked = total === checked;

                    updateStandarText();
                });
            });

            searchStandar.addEventListener('keyup', function() {
                const keyword = this.value.toLowerCase();

                standarItems.forEach(item => {
                    const text = item.textContent.toLowerCase();

                    if (text.includes(keyword)) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            });

            updateStandarText();
        });
        document.addEventListener('DOMContentLoaded', function() {
            const radioAllProdi = document.getElementById('target_all_prodi');
            const radioUnitBagian = document.getElementById('target_unit_bagian');
            const wrapperUnit = document.getElementById('wrapper-upt-unit');
            const infoProdi = document.getElementById('info-prodi');
            const selectUnit = wrapperUnit.querySelector('select');

            function toggleUnitBagian() {
                if (radioUnitBagian.checked) {
                    // tampilkan dropdown unit/bagian
                    wrapperUnit.classList.remove('hidden');
                    selectUnit.disabled = false;
                    selectUnit.required = true;

                    // sembunyikan info prodi
                    infoProdi.classList.add('hidden');

                } else {
                    // sembunyikan dropdown unit/bagian
                    wrapperUnit.classList.add('hidden');
                    selectUnit.disabled = true;
                    selectUnit.required = false;
                    selectUnit.value = '';

                    // tampilkan info prodi
                    infoProdi.classList.remove('hidden');
                }
            }

            radioAllProdi.addEventListener('change', toggleUnitBagian);
            radioUnitBagian.addEventListener('change', toggleUnitBagian);

            toggleUnitBagian();
        });

        // JS MODAL TAMBAH
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('.target-type');
            const unitWrapper = document.getElementById('unit-bagian-wrapper');
            const infoProdi = document.getElementById('info-prodi');

            function toggleTarget() {
                const selected = document.querySelector('.target-type:checked')?.value;

                if (selected === 'all_prodi') {
                    unitWrapper.classList.add('hidden');
                    infoProdi.classList.remove('hidden');
                } else {
                    unitWrapper.classList.remove('hidden');
                    infoProdi.classList.add('hidden');
                }
            }

            radios.forEach(radio => {
                radio.addEventListener('change', toggleTarget);
            });

            toggleTarget();

            const checkAllStandar = document.getElementById('checkbox-all-standar');
            const standarCheckboxes = document.querySelectorAll('.standar-checkbox');
            const standarSelectedText = document.getElementById('standar-selected-text');
            const searchStandarInput = document.getElementById('input-group-search-standar');
            const standarItems = document.querySelectorAll('.standar-item');

            function updateStandarSelectedText() {
                const checked = document.querySelectorAll('.standar-checkbox:checked');
                const labels = Array.from(checked).map(item =>
                    item.closest('.flex.items-center').querySelector('.standar-label').textContent.trim()
                );

                if (labels.length === 0) {
                    standarSelectedText.textContent = 'Pilih standar mutu';
                } else if (labels.length <= 2) {
                    standarSelectedText.textContent = labels.join(', ');
                } else {
                    standarSelectedText.textContent = `${labels.length} standar dipilih`;
                }

                if (checkAllStandar) {
                    checkAllStandar.checked = checked.length === standarCheckboxes.length && standarCheckboxes.length > 0;
                }
            }

            if (checkAllStandar) {
                checkAllStandar.addEventListener('change', function() {
                    standarCheckboxes.forEach(item => {
                        item.checked = this.checked;
                    });
                    updateStandarSelectedText();
                });
            }

            standarCheckboxes.forEach(item => {
                item.addEventListener('change', updateStandarSelectedText);
            });

            if (searchStandarInput) {
                searchStandarInput.addEventListener('keyup', function() {
                    const value = this.value.toLowerCase();

                    standarItems.forEach(item => {
                        const label = item.querySelector('.standar-label').textContent.toLowerCase();
                        item.style.display = label.includes(value) ? '' : 'none';
                    });
                });
            }

            updateStandarSelectedText();

            const checkAllUnit = document.getElementById('checkbox-all-unit');
            const unitCheckboxes = document.querySelectorAll('.unit-checkbox');
            const unitSelectedText = document.getElementById('unit-selected-text');
            const searchUnitInput = document.getElementById('input-group-search-unit');
            const unitItems = document.querySelectorAll('.unit-item');

            function updateUnitSelectedText() {
                const checked = document.querySelectorAll('.unit-checkbox:checked');
                const labels = Array.from(checked).map(item =>
                    item.closest('.flex.items-center').querySelector('.unit-label').textContent.trim()
                );

                if (labels.length === 0) {
                    unitSelectedText.textContent = 'Pilih unit/bagian';
                } else if (labels.length <= 2) {
                    unitSelectedText.textContent = labels.join(', ');
                } else {
                    unitSelectedText.textContent = `${labels.length} unit/bagian dipilih`;
                }

                if (checkAllUnit) {
                    checkAllUnit.checked = checked.length === unitCheckboxes.length && unitCheckboxes.length > 0;
                }
            }

            if (checkAllUnit) {
                checkAllUnit.addEventListener('change', function() {
                    unitCheckboxes.forEach(item => {
                        item.checked = this.checked;
                    });
                    updateUnitSelectedText();
                });
            }

            unitCheckboxes.forEach(item => {
                item.addEventListener('change', updateUnitSelectedText);
            });

            if (searchUnitInput) {
                searchUnitInput.addEventListener('keyup', function() {
                    const value = this.value.toLowerCase();

                    unitItems.forEach(item => {
                        const label = item.querySelector('.unit-label').textContent.toLowerCase();
                        item.style.display = label.includes(value) ? '' : 'none';
                    });
                });
            }

            updateUnitSelectedText();
        });

        const checkAllStandarEdit = document.getElementById('checkbox-all-standar-edit');
        const standarEditCheckboxes = document.querySelectorAll('.standar-edit-checkbox');
        const standarEditSelectedText = document.getElementById('standar-edit-selected-text');
        const searchStandarEditInput = document.getElementById('input-group-search-standar-edit');
        const standarEditItems = document.querySelectorAll('.standar-edit-item');

        function updateStandarEditSelectedText() {
            const checked = document.querySelectorAll('.standar-edit-checkbox:checked');
            const labels = Array.from(checked).map(item =>
                item.closest('.flex.items-center').querySelector('.standar-edit-label').textContent.trim()
            );

            if (labels.length === 0) {
                standarEditSelectedText.textContent = 'Pilih standar mutu';
            } else if (labels.length <= 2) {
                standarEditSelectedText.textContent = labels.join(', ');
            } else {
                standarEditSelectedText.textContent = `${labels.length} standar dipilih`;
            }

            if (checkAllStandarEdit) {
                checkAllStandarEdit.checked = checked.length === standarEditCheckboxes.length && standarEditCheckboxes.length > 0;
            }
        }

        if (checkAllStandarEdit) {
            checkAllStandarEdit.addEventListener('change', function() {
                standarEditCheckboxes.forEach(item => {
                    item.checked = this.checked;
                });
                updateStandarEditSelectedText();
            });
        }


        // JS MODAL EDIT
        standarEditCheckboxes.forEach(item => {
            item.addEventListener('change', updateStandarEditSelectedText);
        });

        if (searchStandarEditInput) {
            searchStandarEditInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();

                standarEditItems.forEach(item => {
                    const label = item.querySelector('.standar-edit-label').textContent.toLowerCase();
                    item.style.display = label.includes(value) ? '' : 'none';
                });
            });
        }

        $(document).on('click', '.button-edit', function() {
            let uptId = $(this).data('upt-id');
            let namaUpt = $(this).data('nama-upt');
            let standarIds = ($(this).data('standar-ids') + '').split(',');
            let periodeId = $(this).data('periode-id');

            $('#edit_upt_id').val(uptId);
            $('#edit_nama_upt').val(namaUpt);
            $('#edit_periode_id').val(periodeId);

            $('.standar-edit-checkbox').prop('checked', false);

            standarIds.forEach(function(id) {
                $(`.standar-edit-checkbox[value="${id}"]`).prop('checked', true);
            });

            updateStandarEditSelectedText();

            $('#modal-edit').removeClass('hidden').addClass('flex');
        });

        $(document).on('click', '.button-hapus', function() {
            let upt_id = $(this).data('upt-id');
            let periodeId = $(this).data('periode-id');

            $('#upt_id_hapus').val(upt_id);
            $('#periode_id_hapus').val(periodeId);

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