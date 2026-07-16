<?php

namespace App\Http\Middleware;

use App\Services\PeriodeAktifService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SinkronkanPeriodeAktif
{
    public function __construct(private PeriodeAktifService $periodeAktifService)
    {
    }

    // Berjalan otomatis agar periode tahun berjalan selalu tersedia dan aktif.
    public function handle(Request $request, Closure $next): Response
    {
        $this->periodeAktifService->sinkronkanTahunBerjalan();

        return $next($request);
    }
}
