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
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'ALKS',
                'nama_prodi' => 'Akuntansi Lembaga Keuangan Syariah',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'PPA',
                'nama_prodi' => 'Pengembangan Produk Agroindustri',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'RKS',
                'nama_prodi' => 'Rekayasa Keamanan Siber',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TE',
                'nama_prodi' => 'Teknik Elektronika',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TM',
                'nama_prodi' => 'Teknik Mesin',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TL',
                'nama_prodi' => 'Teknik Listrik',
                'jenjang' => 'D3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TPPL',
                'nama_prodi' => 'Teknik Pengendalian Pencemaran Lingkungan',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TRET',
                'nama_prodi' => 'Teknologi Rekayasa Energi Terbarukan',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TRKI',
                'nama_prodi' => 'Teknologi Rekayasa Kimia Industri',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TRM',
                'nama_prodi' => 'Teknologi Rekayasa Multimedia',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TRMA',
                'nama_prodi' => 'Teknologi Rekayasa Mekatronika',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'prodi_id' => Str::uuid(),
                'kode_prodi' => 'TRPL',
                'nama_prodi' => 'Teknologi Rekayasa Perangkat Lunak',
                'jenjang' => 'D4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}