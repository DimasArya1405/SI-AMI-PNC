<?php

namespace App\Exports;

use App\Exports\Sheets\UptStandarMutuSheet;
use App\Models\UptStandarMutu;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UptStandarMutuExport implements WithMultipleSheets
{
    protected $upt_id;
    protected $periode_id;

    public function __construct($upt_id, $periode_id)
    {
        $this->upt_id = $upt_id;
        $this->periode_id = $periode_id;
    }

    public function sheets(): array
    {
        $sheets = [];

        $urutanStandar = [
            'STANDAR PENDIDIKAN',
            'STANDAR PENELITIAN',
            'STANDAR PKM',
            'STANDAR TAMBAHAN',
        ];

        $standarList = UptStandarMutu::with('standar_mutu')
            ->where('upt_id', $this->upt_id)
            ->where('periode_id', $this->periode_id)
            ->get()
            ->unique('standar_mutu_id')
            ->sortBy(function ($item) use ($urutanStandar) {
                $namaStandar = strtoupper(
                    trim($item->standar_mutu->nama_standar_mutu ?? '')
                );

                $index = array_search($namaStandar, $urutanStandar);

                return $index !== false ? $index : 999;
            });

        foreach ($standarList as $data) {
            $sheets[] = new UptStandarMutuSheet(
                $this->upt_id,
                $this->periode_id,
                $data->standar_mutu_id
            );
        }

        return $sheets;
    }
}
