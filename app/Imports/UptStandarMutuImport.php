<?php

namespace App\Imports;

use App\Models\Upt;
use App\Models\UptSubStandarMutu;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class UptStandarMutuImport implements ToCollection
{
    protected $periodeId;

    public function __construct($periodeId)
    {
        $this->periodeId = $periodeId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index < 1) {
                continue;
            }

            $kodeUpt = trim($row[0] ?? '');
            $kodeStandar = trim($row[1] ?? '');
            $namaStandar = trim($row[2] ?? '');

            if (!$kodeUpt || !$kodeStandar) {
                continue;
            }

            $upt = Upt::where('kode_upt', $kodeUpt)->first();

            if (!$upt) {
                continue;
            }

            UptSubStandarMutu::updateOrCreate(
                [
                    'periode_id' => $this->periodeId,
                    'upt_id' => $upt->upt_id,
                    'kode_standar' => $kodeStandar,
                ],
                [
                    'pemetaan_id' => Str::uuid(),
                    'nama_standar' => $namaStandar,
                ]
            );
        }
    }
}