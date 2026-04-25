<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('periode')->insert([
            [
                'id' => Str::uuid(),
                'tahun' => 2024,
                'status' => '0', // Tidak Aktif
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'tahun' => 2025,
                'status' => '0', // Tidak Aktif
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'tahun' => 2026,
                'status' => '1', // Aktif
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}