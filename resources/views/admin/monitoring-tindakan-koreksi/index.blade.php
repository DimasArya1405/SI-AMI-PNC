<x-app-layout>
    @php
        $isKepalaP4mp = Auth::user()->role === 'kepala_p4mp';
        $showRouteName = $isKepalaP4mp ? 'kepala_p4mp.tindakan_koreksi.show' : 'admin.monitoring_tk.show';
    @endphp

    @include($isKepalaP4mp ? 'kepala-p4mp.sidebar' : 'admin.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">{{ $isKepalaP4mp ? 'Verifikasi Tindakan Koreksi' : 'Tindakan Koreksi' }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $isKepalaP4mp ? 'Finalisasi verifikasi tindakan koreksi yang sudah selesai dinilai auditor.' : 'Pantau tindak lanjut temuan AMI seluruh UPT.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Total Temuan</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $ringkasan['total_temuan'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Selesai</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ $ringkasan['total_tk_selesai'] }}</p>
                </div>
                <div class="rounded-lg border bg-white p-4 shadow-sm">
                    <p class="text-xs text-gray-500">Menunggu P4MP</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $ringkasan['total_menunggu_p4mp'] }}</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
                <table class="min-w-[58rem] w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Periode</th>
                            <th class="px-4 py-3">UPT</th>
                            <th class="px-4 py-3">Ketua Auditor</th>
                            <th class="px-4 py-3">Anggota Auditor</th>
                            <th class="px-4 py-3 text-center">Temuan</th>
                            <th class="px-4 py-3 text-center">Selesai</th>
                            <th class="px-4 py-3 text-center">Menunggu P4MP</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($penugasan as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->periode?->tahun ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $item->upt?->nama_upt ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->auditor1?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $item->auditor2?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">{{ $item->jumlah_temuan }}</td>
                                <td class="px-4 py-3 text-center text-green-600">{{ $item->tk_selesai }}</td>
                                <td class="px-4 py-3 text-center text-indigo-600">{{ $item->tk_menunggu_p4mp }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route($showRouteName, $item->penugasan_id) }}"
                                        class="inline-flex items-center justify-center rounded bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada penugasan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
