@include('errors.layout', [
    'code' => $exception->getStatusCode() ?: '4xx',
    'title' => 'Permintaan Tidak Dapat Diproses',
    'message' => 'Halaman atau permintaan yang Anda akses tidak dapat diproses.',
    'description' => 'Periksa kembali alamat halaman atau kembali ke beranda SIAMI.',
])
