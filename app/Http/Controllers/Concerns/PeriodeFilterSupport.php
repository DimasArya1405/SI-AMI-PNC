<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Periode;
use Illuminate\Http\Request;

trait PeriodeFilterSupport
{
    protected function getPeriodeFilterContext(Request $request): array
    {
        $periodeOptions = Periode::query()
            ->orderByDesc('status')
            ->orderByDesc('tahun')
            ->get();

        $periodeAktif = $periodeOptions->firstWhere('status', '1')
            ?? Periode::where('status', '1')->first()
            ?? $periodeOptions->first();

        $selectedPeriodeId = $request->query('periode_id') ?: $periodeAktif?->id;
        $selectedPeriode = $selectedPeriodeId
            ? $periodeOptions->firstWhere('id', $selectedPeriodeId)
            : $periodeAktif;

        if (!$selectedPeriode && $periodeOptions->isNotEmpty()) {
            $selectedPeriode = $periodeOptions->first();
            $selectedPeriodeId = $selectedPeriode?->id;
        }

        return [
            'periodeOptions' => $periodeOptions,
            'periodeAktif' => $periodeAktif,
            'selectedPeriodeId' => $selectedPeriodeId,
            'selectedPeriode' => $selectedPeriode,
        ];
    }
}
