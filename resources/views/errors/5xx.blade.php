@include('errors.layout', [
    'code' => method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : '5xx',
    'title' => 'Terjadi Kesalahan Sistem',
    'message' => 'Sistem sedang mengalami kendala saat memproses permintaan Anda.',
    'description' => 'Silakan coba kembali beberapa saat lagi atau hubungi pengelola sistem SIAMI.',
])
