<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prodi')->insert([
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TI',
                'nama_prodi' => 'Teknik Informatika',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'SI',
                'nama_prodi' => 'Sistem Informasi',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'MI',
                'nama_prodi' => 'Manajemen Informatika',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TK',
                'nama_prodi' => 'Teknik Komputer',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TE',
                'nama_prodi' => 'Teknik Elektro',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TM',
                'nama_prodi' => 'Teknik Mesin',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TS',
                'nama_prodi' => 'Teknik Sipil',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'MN',
                'nama_prodi' => 'Manajemen',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'AK',
                'nama_prodi' => 'Akuntansi',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'BD',
                'nama_prodi' => 'Bisnis Digital',
                'jenjang' => 'S1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}