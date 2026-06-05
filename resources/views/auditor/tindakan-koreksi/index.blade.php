<x-app-layout>
    @include('auditor.sidebar')

    <div class="ml-60 py-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">Verifikasi Tindakan Koreksi</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Pantau dan verifikasi tindakan koreksi yang diajukan auditee.
                </p>
            </div>

            @forelse ($penugasan as $item)
                <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-semibold text-gray-800">{{ $item->upt?->nama_upt ?? '-' }}</h2>
                                <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">
                                    Periode {{ $item->periode?->tahun ?? '-' }}
                                </span>
                                <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700">
                                    {{ $item->jumlah_temuan }} temuan
                                </span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2 text-sm">
                                <span class="rounded bg-yellow-50 px-2 py-1 text-yellow-700">{{ $item->tk_menunggu }} menunggu verifikasi</span>
                                <span class="rounded bg-green-50 px-2 py-1 text-green-700">{{ $item->tk_selesai }} selesai</span>
                            </div>
                        </div>

                        <a href="{{ route('auditor.tindakan_koreksi.show', $item->penugasan_id) }}"
                            class="inline-flex items-center justify-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <i class="bi bi-eye"></i>
                            Lihat Detail
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
