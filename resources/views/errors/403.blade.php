@include('errors.layout', [
    'code' => '403',
    'title' => 'Akses Ditolak',
    'message' => 'Akun Anda tidak memiliki izin untuk membuka halaman ini.',
    'description' => 'Pastikan Anda menggunakan akun dengan role yang sesuai.',
])
