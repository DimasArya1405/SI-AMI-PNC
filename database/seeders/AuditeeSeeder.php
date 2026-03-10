<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuditeeSeeder extends Seeder
{
    public function run(): void
    {
        $prodiIds = DB::table('prodi')->pluck('prodi_id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        DB::table('auditee')->insert([
            [
                'auditee_id' => Str::uuid(),
                'user_id' => $userIds[1],
                'prodi_id' => $prodiIds[0],
                'nip' => '198001011',
                'nama_lengkap' => 'Budi Santoso',
                'jabatan' => 'Ketua Prodi',
                'no_telp' => '081234567890',
                'email' => 'budi@example.com',
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
        ]);
    }
}