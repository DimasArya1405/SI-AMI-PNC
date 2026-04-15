<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // --- KATEGORI PRODI (Sesuai ProdiSeeder) ---
            [
                'upt_id' => Str::uuid(),
                'kode_upt' => 'TI',
                'nama_upt' => 'Teknik Informatika',
                'kategori_upt' => 'Prodi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'upt_id' => Str::uuid(),
                'kode_upt' => 'TE',
                'nama_upt' => 'Teknik Elektro',
                'kategori_upt' => 'Prodi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'upt_id' => Str::uuid(),
                'kode_upt' => 'TM',
                'nama_upt' => 'Teknik Mesin',
                'kategori_upt' => 'Prodi',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- KATEGORI UNIT/BAGIAN ---
            [
                'upt_id' => Str::uuid(),
                'kode_upt' => 'BAAK',
                'nama_upt' => 'Biro Administrasi Akademik & Kemahasiswaan',
                'kategori_upt' => 'Unit/Bagian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'upt_id' => Str::uuid(),
                'kode_upt' => 'BAU',
                'nama_upt' => 'Biro Administrasi Umum',
                'kategori_upt' => 'Unit/Bagian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('upt')->insert($data);
    }
}