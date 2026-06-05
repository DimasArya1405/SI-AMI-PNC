<x-app-layout>
    @include('auditee.sidebar')

    <div class="ml-60 py-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">Tindakan Koreksi</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Lihat rumusan tindakan koreksi dari auditor dan unggah bukti pelaksanaannya.
                </p>
            </div>

            @forelse ($penugasan as $item)
                @php
                    $persentase = $item->jumlah_temuan > 0 ? round(($item->tk_selesai / $item->jumlah_temuan) * 100) : 0;
                @endphp
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-base font-semibold text-gray-800">{{ $item->upt?->nama_upt ?? '-' }}</h2>
                                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                    Periode {{ $item->periode?->tahun ?? '-' }}
                                </span>
                                <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                    {{ $item->jumlah_temuan }} temuan
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-2 text-sm text-gray-600 sm:grid-cols-3">
                                <span><i class="bi bi-send-check mr-1 text-blue-600"></i>{{ $item->tk_diajukan }} diajukan</span>
                                <span><i class="bi bi-check-circle mr-1 text-green-600"></i>{{ $item->tk_selesai }} selesai</span>
                                <span><i class="bi bi-person-check mr-1 text-blue-600"></i>{{ $item->auditor1?->nama_lengkap ?? '-' }}</span>
                            </div>

                            <div class="mt-4">
                                <div class="mb-1 flex justify-between text-xs text-gray-500">
                                    <span>Progress tindakan koreksi</span>
                                    <span class="font-semibold">{{ $persentase }}%</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-green-500" style="width: {{ $persentase }}%"></div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('auditee.tindakan_koreksi.show', $item->penugasan_id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <i class="bi bi-pencil-square"></i>
                            Kelola TK
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-lg border-2 border-dashed border-gray-200 bg-white p-10 text-center">
                    <i class="bi bi-clipboard2-check text-4xl text-gray-300"></i>
                    <p class="mt-3 text-sm font-medium text-gray-500">Belum ada penugasan audit.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
