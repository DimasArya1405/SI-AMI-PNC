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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $standarId = $standar->standar_mutu_id;
                }

                $currentSubStandarId = null;
                $lastParentItemId = null;
                $subUrutan = 1;
                $itemUrutan = 1;
                $childUrutan = 1;

                for ($row = 11; $row <= $sheet->getHighestRow(); $row++) {
                    $colA = trim((string) $sheet->getCell("A{$row}")->getValue());
                    $colB = trim((string) $sheet->getCell("B{$row}")->getValue());

                    if ($colA === '' && $colB === '') {
                        continue;
                    }

                    if (
                        strtoupper($colA) === 'NO' ||
                        strtoupper($colB) === 'PERTANYAAN DAN PERNYATAAN'
                    ) {
                        continue;
                    }

                    if ($colA !== '' && !is_numeric($colA)) {
                        $subStandarId = (string) Str::uuid();

                        DB::table('sub_standar_mutu')->insert([
                            'sub_standar_id' => $subStandarId,
                            'standar_mutu_id' => $standarId,
                            'nama_sub_standar' => $colA,
                            'urutan' => $subUrutan++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $currentSubStandarId = $subStandarId;
                        $lastParentItemId = null;
                        $itemUrutan = 1;
                        $childUrutan = 1;

                        continue;
                    }

                    if (!$currentSubStandarId) {
                        continue;
                    }

                    if (is_numeric($colA) && $colB !== '') {
                        $itemId = (string) Str::uuid();

                        DB::table('item_sub_standar')->insert([
                            'item_sub_standar_id' => $itemId,
                            'sub_standar_id' => $currentSubStandarId,
                            'parent_item_id' => null,
                            'nama_item' => $colB,
                            'tipe_item' => 'pertanyaan',
                            'level' => 1,
                            'urutan' => $itemUrutan++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $lastParentItemId = $itemId;
                        $childUrutan = 1;

                        continue;
                    }

                    if ($colA === '' && $colB !== '' && $lastParentItemId) {
                        DB::table('item_sub_standar')->insert([
                            'item_sub_standar_id' => (string) Str::uuid(),
                            'sub_standar_id' => $currentSubStandarId,
                            'parent_item_id' => $lastParentItemId,
                            'nama_item' => $colB,
                            'tipe_item' => 'sub_pertanyaan',
                            'level' => 2,
                            'urutan' => $childUrutan++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });
    }
}