<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StandarMutuSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('app/imports/Formulir_AMI_2025_Prodi.xlsx');

        if (!file_exists($filePath)) {
            throw new \Exception("File Excel tidak ditemukan: {$filePath}");
        }

        DB::transaction(function () use ($filePath) {
            $spreadsheet = IOFactory::load($filePath);
            $urutanStandar = 1;

            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                $standarName = trim((string) $sheet->getCell('E6')->getValue());

                if ($standarName === '') {
                    $standarName = trim($sheet->getTitle());
                }

                $standar = DB::table('standar_mutu')
                    ->where('nama_standar_mutu', $standarName)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$standar) {
                    $standarId = (string) Str::uuid();

                    DB::table('standar_mutu')->insert([
                        'standar_mutu_id' => $standarId,
                        'nama_standar_mutu' => $standarName,
                        'urutan' => $urutanStandar,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $standarId = $standar->standar_mutu_id;

                    DB::table('standar_mutu')
                        ->where('standar_mutu_id', $standarId)
                        ->update([
                            'urutan' => $urutanStandar,
                            'updated_at' => now(),
                        ]);
                }

                $urutanStandar++;

                $currentSubStandarId = null;
                $lastLevel1ItemId = null;
                $lastLevel2ItemId = null;

                $subUrutan = 1;
                $urutanGlobal = 1;

                for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
                    $colA = trim((string) $sheet->getCell("A{$row}")->getCalculatedValue());
                    $colB = trim((string) $sheet->getCell("B{$row}")->getCalculatedValue());

                    if ($colA === '' && $colB === '') {
                        continue;
                    }

                    if (
                        strtoupper($colA) === 'NO' ||
                        strtoupper($colB) === 'PERTANYAAN DAN PERNYATAAN' ||
                        strtoupper($colB) === 'YA'
                    ) {
                        continue;
                    }

                    if ($colA !== '' && !is_numeric($colA) && $colB === '') {
                        $currentSubStandarId = (string) Str::uuid();

                        DB::table('sub_standar_mutu')->insert([
                            'sub_standar_id' => $currentSubStandarId,
                            'standar_mutu_id' => $standarId,
                            'nama_sub_standar' => $colA,
                            'urutan' => $subUrutan++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $lastLevel1ItemId = null;
                        $lastLevel2ItemId = null;
                        $urutanGlobal = 1;

                        continue;
                    }

                    if (!$currentSubStandarId || $colB === '') {
                        continue;
                    }

                    $text = $colB;
                    $level = 1;
                    $parentItemId = null;

                    if (is_numeric($colA)) {
                        $level = 1;
                        $parentItemId = null;
                    } elseif (preg_match('/^[a-zA-Z]\./', $text)) {
                        $level = 2;
                        $parentItemId = $lastLevel1ItemId;
                    } elseif (preg_match('/^-/', $text)) {
                        $level = 3;
                        $parentItemId = $lastLevel2ItemId ?: $lastLevel1ItemId;
                    } else {
                        $level = 2;
                        $parentItemId = $lastLevel1ItemId;
                    }

                    $itemId = (string) Str::uuid();

                    DB::table('item_sub_standar')->insert([
                        'item_sub_standar_id' => $itemId,
                        'sub_standar_id' => $currentSubStandarId,
                        'parent_item_id' => $parentItemId,
                        'nama_item' => $text,
                        'tipe_item' => $level === 1 ? 'pertanyaan' : 'sub_pertanyaan',
                        'level' => $level,
                        'urutan' => $urutanGlobal++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($level === 1) {
                        $lastLevel1ItemId = $itemId;
                        $lastLevel2ItemId = null;
                    }

                    if ($level === 2) {
                        $lastLevel2ItemId = $itemId;
                    }
                }
            }
        });
    }
}
