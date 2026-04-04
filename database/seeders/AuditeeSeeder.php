<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuditeeSeeder extends Seeder
{
    public function run(): void
    {
        $uptIds = DB::table('upt')->pluck('upt_id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        DB::table('auditee')->insert([
            [
                'auditee_id' => Str::uuid(),
                'user_id' => $userIds[1],
                'upt_id' => $uptIds[0],
                'nip' => '198001011',
                'nama_lengkap' => 'Budi Santoso',
                // 'kategori' => 'Prodi',
                'no_telp' => '081234567890',
                'email' => 'budi@example.com',
                'status_aktif' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
        ]);
    }
}