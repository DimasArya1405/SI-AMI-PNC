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
                        <a href="{{ route('admin.ami.upt_standar_mutu') }}"
                            class="flex items-center gap-2 bg-gray-500 hover:bg-gray-700 text-white text-sm px-3 py-1 rounded">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="bg-white shadow-xs rounded-lg border border-default p-6">
                @if ($pemetaanStandar->count() > 0)

                <div class="mb-4 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center"
                        id="default-tab"
                        data-tabs-toggle="#default-tab-content"
                        data-tabs-active-classes="text-blue-600 border-blue-600"
                        data-tabs-inactive-classes="text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300"
                        role="tablist">

                        @foreach ($pemetaanStandar as $index => $item)
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg"
                                id="tab-{{ $item->standar_mutu_id }}"
                                data-tabs-target="#content-{{ $item->standar_mutu_id }}"
                                type="button"
                                role="tab"
                                aria-controls="content-{{ $item->standar_mutu_id }}"
                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                {{ $item->standar_mutu->nama_standar_mutu ?? '-' }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div id="default-tab-content">
                    @foreach ($pemetaanStandar as $index => $item)
                    <div class="{{ $index == 0 ? '' : 'hidden' }} p-4 rounded-lg bg-gray-50"
                        id="content-{{ $item->standar_mutu_id }}"
                        role="tabpanel"
                        aria-labelledby="tab-{{ $item->standar_mutu_id }}">

                        <h2 class="text-base font-semibold text-gray-900 mb-2">
                            {{ $item->standar_mutu->nama_standar_mutu ?? '-' }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white border rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Nama UPT</p>
                                <p class="text-sm font-medium text-gray-900">{{ $upt->nama_upt }}</p>
                            </div>

                            <div class="bg-white border rounded-lg p-4">
                                <p class="text-sm text-gray-500 mb-1">Nama Standar Mutu</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $item->standar_mutu->nama_standar_mutu ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 bg-white border rounded-lg p-4">
                            <p class="text-sm font-medium text-gray-900 mb-2">Isi Detail</p>
                            <p class="text-sm text-gray-600">
                                Di bagian ini nanti bisa ditampilkan sub standar, item sub standar,
                                atau data audit sesuai standar yang dipilih.
                            </p>
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
</x-app-layout>