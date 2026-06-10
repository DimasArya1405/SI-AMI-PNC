@include('errors.layout', [
    'code' => '429',
    'title' => 'Terlalu Banyak Permintaan',
    'message' => 'Sistem menerima terlalu banyak permintaan dalam waktu singkat.',
    'description' => 'Tunggu beberapa saat, lalu coba kembali.',
])
