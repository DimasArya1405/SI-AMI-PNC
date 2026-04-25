<x-app-layout>
    @include('admin.sidebar')
    <div class="py-6 ml-60">
        <div class="max-w-7xl mx-auto sm:px-2 lg:px-4 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('Data Penugasan - Buat Penugasan') }}
                </div>
            </div>
            <div class="relative overflow-x-auto bg-white shadow-xs rounded-lg border border-default">
                <div class="flex justify-between items-center py-4 mx-4 border-b border-gray-300">
                    <div class="font-semibold">Program Studi</div>
                    <a href="{{ route('admin.ami.penugasan.export', $periode_id) }}" target="_blank"
                        class="bg-blue-500 text-sm font-semibold text-white px-4 py-1 rounded-md hover:bg-blue-700 transition duration-200 ease-in-out">
                        <i class="bi bi-download mr-2"></i>Export PDF
                    </a>
                </div>
                <div class="dt-responsive table-responsive p-4 pt-4">
                    <div
                        class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                        <table class="w-full text-sm text-left rtl:text-right text-body">
                            <thead class="text-sm text-body bg-gray-200 border-b rounded-base border-default">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        NO
                                    </th>
                                    <th scope="col" class="px-6 py-3 w-72 font-semibold">
                                        NAMA UPT
                                    </th>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        Auditor
                                    </th>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        Jadwal
                                    </th>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($uptProdi as $item)
                                    <tr class="bg-neutral-primary border-b border-default">
                                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $item->nama_upt }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $tugas = $item->penugasan->first(); @endphp

                                            @if ($tugas)
                                                <ul class="list-disc ml-4">
                                                    <li>
                                                        <span class="font-semibold text-green-500"> Ketua
                                                            Auditor:</span> <br>
                                                        <span
                                                            class="font-bold">{{ $tugas->auditor1->nama_lengkap ?? 'Nama tidak ditemukan' }}</span>
                                                    </li>
                                                    <li class="mt-2">
                                                        <span class="font-semibold text-green-500"> Anggota
                                                            Auditor:</span> <br>
                                                        <span
                                                            class="font-bold">{{ $tugas->auditor2->nama_lengkap ?? 'Nama tidak ditemukan' }}</span>
                                                    </li>
                                                </ul>
                                            @else
                                                <span class="text-red-500 italic">Belum ada auditor</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @php
                                                $penugasan = $item->penugasan->first();
                                                $pengajuan = $penugasan
                                                    ? $penugasan->pengajuan_jadwal_audit->first()
                                                    : null;
                                            @endphp

                                            @if ($penugasan)
                                                <div class="flex flex-col gap-2">
                                                    {{-- Kondisi jika ada pengajuan jadwal baru dan sudah disetujui semua pihak (1) --}}
                                                    @if ($pengajuan && $pengajuan->ketua_auditor == 1 && $pengajuan->anggota_auditor == 1 && $pengajuan->upt == 1)
                                                        {{-- Bagian Jadwal Awal --}}
                                                        <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                                            <span
                                                                class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Jadwal
                                                                Awal</span>
                                                            <div
                                                                class="flex items-center gap-1.5 text-gray-500 line-through">
                                                                <svg class="w-4 h-4" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                                <span>{{ $penugasan->tanggal_audit ?? '-' }}</span>
                                                            </div>
                                                            <span
                                                                class="text-xs ml-5 text-gray-400 italic">{{ $penugasan->jam ? $penugasan->jam . ' WIB' : '-' }}</span>
                                                        </div>

                                                        {{-- Bagian Jadwal Baru (Revisi) --}}
                                                        <div class="bg-blue-50 p-2 rounded-lg border border-blue-100">
                                                            <span
                                                                class="block text-[10px] font-bold uppercase tracking-wider text-blue-600 mb-1">Jadwal
                                                                Akhir</span>
                                                            <div
                                                                class="flex items-center gap-1.5 text-blue-900 font-semibold">
                                                                <svg class="w-4 h-4 text-blue-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                    </path>
                                                                </svg>
                                                                <span>{{ $pengajuan->tanggal_audit ?? '-' }}</span>
                                                            </div>
                                                            <span
                                                                class="text-xs ml-5 text-blue-700">{{ $pengajuan->jam ? $pengajuan->jam . ' WIB' : '-' }}</span>
                                                        </div>
                                                    @else
                                                        {{-- Tampilan Standar jika tidak ada perubahan jadwal --}}
                                                        <div class="flex flex-col">
                                                            <div
                                                                class="flex items-center gap-1.5 font-medium text-gray-900">
                                                                <svg class="w-4 h-4 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                                <span>{{ $penugasan->tanggal_audit ?? '-' }}</span>
                                                            </div>
                                                            <div
                                                                class="flex items-center gap-1.5 mt-1 text-xs text-gray-500 ml-5">
                                                                <span>{{ $penugasan->jam ? $penugasan->jam . ' WIB' : '-' }}</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">Belum ada penugasan</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 flex flex-col gap-2">
                                            @php $tugas = $item->penugasan->first(); @endphp

                                            @if (!$tugas)
                                                {{-- Tombol Buat Penugasan --}}
                                                <button data-modal-target="modal-penugasan"
                                                    data-modal-toggle="modal-penugasan"
                                                    data-uptId="{{ $item->upt_id }}"
                                                    data-periodeId="{{ $periode_id }}"
                                                    class="hover:bg-green-700 transition button-penugasan duration-300 ease-in-out py-1 px-2 bg-green-500 rounded text-white">
                                                    Buat Penugasan
                                                </button>
                                            @else
                                                @if ($tugas->status_penugasan == 'pending')
                                                    <button data-modal-target="modal-edit-penugasan"
                                                        data-modal-toggle="modal-edit-penugasan"
                                                        data-uptId="{{ $item->upt_id }}"
                                                        data-periodeId="{{ $periode_id }}"
                                                        data-auditorId_1="{{ $tugas->auditor_id_1 }}"
                                                        data-auditorId_2="{{ $tugas->auditor_id_2 }}"
                                                        data-jam="{{ $tugas->jam }}"
                                                        data-tanggal="{{ $tugas->tanggal_audit }}"
                                                        class="hover:bg-yellow-700 transition button-edit-penugasan duration-300 ease-in-out py-1 px-2 bg-yellow-500 rounded text-white">
                                                        <i class="bi bi-edit text-xs"></i> Edit Penugasan
                                                    </button>
                                                @else
                                                    <span class="text-green-500 font-semibold">Penugasan sudah
                                                        aktif</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-between items-center py-4 mx-4 border-b border-gray-300">
                    <div class="font-semibold">Unit / Bagian Lain</div>
                </div>
                <div class="dt-responsive table-responsive p-4 pt-4">
                    <div
                        class="relative mb-6 overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
                        <table class="w-full text-sm text-left rtl:text-right text-body">
                            <thead class="text-sm text-body bg-gray-200 border-b rounded-base border-default">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        NO
                                    </th>
                                    <th scope="col" class="px-6 py-3 w-72 font-semibold">
                                        NAMA UPT
                                    </th>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        Auditor
                                    </th>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        Jadwal
                                    </th>
                                    <th scope="col" class="px-6 py-3 font-semibold">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            <tbody>
                                @forelse ($uptBagian as $item)
                                    {{-- BLOK INI AKAN JALAN JIKA ADA DATA --}}
                                    <tr class="bg-neutral-primary border-b border-default">
                                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $item->nama_upt }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $tugas = $item->penugasan->first(); @endphp

                                            @if ($tugas)
                                                <ul class="list-disc ml-4">
                                                    <li>
                                                        <span class="font-semibold text-green-500"> Ketua
                                                            Auditor:</span> <br>
                                                        <span
                                                            class="font-bold">{{ $tugas->auditor1->nama_lengkap ?? 'Nama tidak ditemukan' }}</span>
                                                    </li>
                                                    <li class="mt-2">
                                                        <span class="font-semibold text-green-500"> Anggota
                                                            Auditor:</span> <br>
                                                        <span
                                                            class="font-bold">{{ $tugas->auditor2->nama_lengkap ?? 'Nama tidak ditemukan' }}</span>
                                                    </li>
                                                </ul>
                                            @else
                                                <span class="text-red-500 italic">Belum ada auditor</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($jam = $item->penugasan->first()?->jam)
                                            {{ $item->penugasan->first()->tanggal_audit ?? '-' }} <br>
                                                {{ $jam }} WIB
                                            @else
                                                <span class="text-gray-400 italic">Belum ada penugasan</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 flex flex-col gap-2">
                                            @php $tugas = $item->penugasan->first(); @endphp

                                            @if (!$tugas)
                                                {{-- Tombol Buat Penugasan --}}
                                                <button data-modal-target="modal-penugasan"
                                                    data-modal-toggle="modal-penugasan"
                                                    data-uptId="{{ $item->upt_id }}"
                                                    data-periodeId="{{ $periode_id }}"
                                                    class="hover:bg-green-700 transition button-penugasan duration-300 ease-in-out py-1 px-2 bg-green-500 rounded text-white">
                                                    Buat Penugasan
                                                </button>
                                            @else
                                                @if ($tugas->status_penugasan == 'pending')
                                                    <button data-modal-target="modal-edit-penugasan"
                                                        data-modal-toggle="modal-edit-penugasan"
                                                        data-uptId="{{ $item->upt_id }}"
                                                        data-periodeId="{{ $periode_id }}"
                                                        data-auditorId_1="{{ $tugas->auditor_id_1 }}"
                                                        data-auditorId_2="{{ $tugas->auditor_id_2 }}"
                                                        data-jam="{{ $tugas->jam }}"
                                                        data-tanggal="{{ $tugas->tanggal_audit }}"
                                                        class="hover:bg-yellow-700 transition button-edit-penugasan duration-300 ease-in-out py-1 px-2 bg-yellow-500 rounded text-white">
                                                        <i class="bi bi-edit text-xs"></i> Edit Penugasan
                                                    </button>
                                                @else
                                                    <span class="text-green-500 font-semibold">Penugasan sudah
                                                        aktif</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- BLOK INI AKAN JALAN JIKA $uptBagian KOSONG --}}
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-body italic">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="bi bi-folder-x text-4xl mb-2 text-gray-300"></i>
                                                <p>Belum ada data UPT/Bagian yang tersedia.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex justify-end">
                        @if ($penugasan_sekarang->count() > 0)
                            @if ($penugasan_sekarang->first()->status_penugasan == 'pending')
                                <button data-modal-target="modal-konfirmasi-aktif"
                                    data-modal-toggle="modal-konfirmasi-aktif" {{-- href="{{ route('admin.ami.penugasan.aktifkan', $periode_id) }}" --}}
                                    class="hover:bg-blue-700 mb-2 px-4 py-2 transition button-penugasan duration-300 ease-in-out bg-blue-500 rounded text-white">
                                    <i class="bi bi-check-circle"></i> Aktifkan Penugasan
                                </button>
                            @else
                                <p class="text-green-500">Penugasan sudah aktif</p>
                            @endif
                        @else
                            <p class="text-red-500">Belum ada penugasan</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modal-penugasan" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Buat Penugasan
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-penugasan">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form action="{{ route('admin.ami.penugasan.tambah') }}" method="post">
                    @csrf
                    @method('post')
                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <input type="hidden" id="periode_id" name="periode_id">
                        <input type="hidden" id="upt_id" name="upt_id">
                        <div class="col-span-2">
                            <label for="category" class="block mb-2.5 text-sm font-medium text-heading">
                                Ketua Auditor
                            </label>
                            <select id="" name="auditor_1"
                                class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body">
                                <option selected="">Pilih Auditor</option>
                                @foreach ($auditor as $item)
                                    <option value="{{ $item->auditor_id }}">{{ $item->nip }} -
                                        {{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="category" class="block mb-2.5 text-sm font-medium text-heading">
                                Anggota Auditor
                            </label>
                            <select id="" name="auditor_2"
                                class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body">
                                <option selected="">Pilih Auditor</option>
                                @foreach ($auditor as $item)
                                    <option value="{{ $item->auditor_id }}"> {{ $item->nip }} -
                                        {{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="price"
                                class="block mb-2.5 text-sm font-medium text-heading">Tanggal</label>
                            <input type="date" name="tanggal" id="price"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="jam" class="block mb-2.5 text-sm font-medium text-heading">Waktu
                                (Jam)</label>
                            <input type="time" name="jam" id="jam"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs"
                                required>
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
                            Simpan Penugasan
                        </button>
                        <button data-modal-hide="modal-penugasan" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- MODAL AKTIF PENUGASAN --}}
    <div id="modal-konfirmasi-aktif" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm border">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Status Kesiapan UPT</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
                        data-modal-toggle="modal-konfirmasi-aktif">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>

                <div class="p-4 md:p-5">
                    <p class="text-sm text-gray-500 mb-2">Pastikan setiap UPT telah memiliki penugasan (Tanda <span
                            class="text-green-600 font-bold">✔</span>).</p>
                    <p class="px-2 py-1 text-sm text-red-700 bg-red-100 mb-4 rounded-sm">Pastikan semua data penugasan
                        sudah benar!!!</p>

                    <ul class="max-h-60 overflow-y-auto divide-y divide-gray-100 border rounded-lg">
                        @foreach ($upts as $upt)
                            {{-- Logika baru: Dianggap lengkap jika penugasan_count adalah 1 --}}
                            @php $isLengkap = $upt->penugasan_count > 0; @endphp

                            <li
                                class="flex items-center justify-between p-3 {{ $isLengkap ? 'bg-green-50/50' : 'bg-white' }}">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-800">{{ $upt->nama_upt }}</span>
                                    <span class="text-xs {{ $isLengkap ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $isLengkap ? 'Penugasan Tersedia' : 'Belum Ada Penugasan' }}
                                    </span>
                                </div>

                                @if ($isLengkap)
                                    <div class="flex items-center justify-center w-6 h-6 bg-green-100 rounded-full">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center w-6 h-6 bg-red-100 rounded-full">
                                        <span class="text-red-600 font-bold text-xs">!</span>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-5 flex gap-3">
                        @php
                            // Tombol aktif jika SEMUA UPT sudah punya minimal 1 baris penugasan
                            $siapAktif = $upts->every(fn($u) => $u->penugasan_count > 0);
                        @endphp

                        @if ($siapAktif)
                            <form action="{{ route('admin.ami.penugasan.aktifkan', $periode_id) }}" method="POST"
                                class="w-full">
                                @csrf
                                @method('put')
                                <button type="submit"
                                    class="w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition">
                                    Konfirmasi & Aktifkan Semua
                                </button>
                            </form>
                        @else
                            <button disabled
                                class="w-full text-white bg-gray-400 cursor-not-allowed font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                Penugasan Belum Lengkap
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL EDIT PENUGASAN --}}
    <div id="modal-edit-penugasan" tabindex="-1" aria-hidden="true"
        class="hidden bg-gray-900/50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] min-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white border border-default rounded-base shadow-sm p-4 md:p-6">
                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        Edit Penugasan
                    </h3>
                    <button type="button"
                        class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal-edit-penugasan">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form action="{{ route('admin.ami.penugasan.edit') }}" method="post">
                    @csrf
                    @method('put')
                    <div class="grid gap-4 grid-cols-2 py-4 md:py-6">
                        <input type="hidden" id="periode_id_edit" name="periode_id">
                        <input type="hidden" id="upt_id_edit" name="upt_id">
                        <input type="hidden" id="auditor_1_old" name="auditor_1_old">
                        <input type="hidden" id="auditor_2_old" name="auditor_2_old">
                        <div class="col-span-2">
                            <label for="category" class="block mb-2.5 text-sm font-medium text-heading">Auditor
                                1</label>
                            <select id="auditor_1" name="auditor_1"
                                class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body">
                                <option selected="">Pilih Auditor</option>
                                @foreach ($auditor as $item)
                                    <option value="{{ $item->auditor_id }}">{{ $item->nip }} -
                                        {{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="category" class="block mb-2.5 text-sm font-medium text-heading">Auditor
                                2</label>
                            <select id="auditor_2" name="auditor_2"
                                class="block w-full px-3 py-2.5 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand px-3 py-2.5 shadow-xs placeholder:text-body">
                                <option selected="">Pilih Auditor</option>
                                @foreach ($auditor as $item)
                                    <option value="{{ $item->auditor_id }}"> {{ $item->nip }} -
                                        {{ $item->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="price"
                                class="block mb-2.5 text-sm font-medium text-heading">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label for="price" class="block mb-2.5 text-sm font-medium text-heading">Jam</label>
                            <input type="time" name="jam" id="jam_2"
                                class="bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand block w-full px-3 py-2.5 shadow-xs placeholder:text-body"
                                required="">
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
                            Simpan Penugasan
                        </button>
                        <button data-modal-hide="modal-edit-penugasan" type="button"
                            class="text-body bg-white hover:bg-gray-200 transition duration-300 ease-in-out border border-gray-400 hover:text-heading focus:ring-4 focus:ring-neutral-tertiary shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- JS --}}
    @push('js')
        <script>
            $(document).on('click', '.button-penugasan', function() {
                // Ambil nilai dari atribut tombol
                // Kita gunakan .attr untuk memastikan data diambil sesuai tulisan di Blade
                let valUpt = $(this).attr('data-uptId');
                let valPeriode = $(this).attr('data-periodeId');

                // Masukkan nilainya ke dalam input hidden
                // .val() digunakan untuk mengisi value
                $('#upt_id').val(valUpt);
                $('#periode_id').val(valPeriode);

                // Cek di console untuk memastikan (Tekan F12 di browser)
                console.log("Isi UPT ID: " + valUpt);
            });
            $(document).on('click', '.button-edit-penugasan', function() {
                let upt_id = $(this).data('uptid');
                let periode_id = $(this).data('periodeid');
                let auditor_1 = $(this).data('auditorid_1');
                let auditor_2 = $(this).data('auditorid_2');
                let jam = $(this).data('jam');
                let tanggal = $(this).data('tanggal');
                $('#periode_id_edit').val(periode_id);
                $('#upt_id_edit').val(upt_id);
                $('#auditor_1').val(auditor_1);
                $('#auditor_2').val(auditor_2);
                $('#auditor_1_old').val(auditor_1);
                $('#auditor_2_old').val(auditor_2);
                $('#jam_2').val(jam);
                $('#tanggal').val(tanggal);
            });
        </script>
    @endpush
    {{-- {!! $dataTable->scripts() !!} --}}
</x-app-layout>
