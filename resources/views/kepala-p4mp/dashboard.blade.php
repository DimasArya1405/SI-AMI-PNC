<x-app-layout>
    @include('kepala-p4mp.sidebar')

    <div class="py-6 lg:ml-60">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-5 shadow-sm sm:p-6">
                <h1 class="text-xl font-bold text-gray-800">Dashboard Kepala P4MP</h1>
                <p class="mt-1 text-sm text-gray-600">Ringkasan verifikasi tindakan koreksi AMI.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-lg border bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total Penugasan</p>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $totalPenugasan }}</p>
                </div>
                <div class="rounded-lg border bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Menunggu Verifikasi</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $menungguVerifikasi }}</p>
                </div>
                <div class="rounded-lg border bg-white p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Sudah Terverifikasi</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ $terverifikasi }}</p>
                </div>
            </div>

            <div class="rounded-lg border border-indigo-100 bg-indigo-50 p-5 text-sm text-indigo-800">
                <p class="font-semibold">Fokus Kepala P4MP</p>
                <p class="mt-1">Verifikasi tindakan koreksi dilakukan setelah auditor menyelesaikan penilaian ulang terhadap bukti auditee.</p>
                <a href="{{ route('kepala_p4mp.tindakan_koreksi.index') }}"
                    class="mt-4 inline-flex rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Buka Verifikasi TK
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
