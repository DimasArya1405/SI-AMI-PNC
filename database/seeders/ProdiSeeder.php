<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('prodi')->insert([
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TI',
                'nama_prodi' => 'Teknik Informatika',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TM',
                'nama_prodi' => 'Teknik Mesin',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TRM',
                'nama_prodi' => 'Teknik Rekayasa Multimedia',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TE',
                'nama_prodi' => 'Teknik Elektro',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TRPL',
                'nama_prodi' => 'Teknik Rekayasa Perangkat Lunak',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}