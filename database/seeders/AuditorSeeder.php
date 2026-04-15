<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuditorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil semua prodi yang ada di database
        $listProdi = DB::table('prodi')->get();

        // 2. Ambil semua user (asumsi UserSeeder sudah buat banyak user)
        // Kita ambil user yang role-nya bukan admin (jika ada sistem role)
        $listUser = DB::table('users')->get();

        // Data dummy nama untuk variasi
        $daftarNama = [
            'Dr. Aris Sudaryono, M.T.', 'Siti Aminah, M.Kom.', 'Budi Raharjo, Ph.D.',
            'Lestari Putri, M.Si.', 'Hendra Wijaya, M.Eng.', 'Dewi Sartika, M.T.',
            'Ahmad Fauzi, M.Kom.', 'Rina Pratama, M.Sc.', 'Eko Santoso, M.T.',
            'Indah Permata, M.Si.'
        ];

        // 3. Looping untuk membuat 10 auditor
        for ($i = 0; $i < 10; $i++) {
            
            // Cek apakah user dan prodi tersedia untuk index ini agar tidak error
            if (isset($listUser[$i]) && isset($listProdi[$i])) {
                
                $nip = '199001012026041' . str_pad($i, 3, '0', STR_PAD_LEFT);

                DB::table('auditor')->updateOrInsert(
                    ['nip' => $nip], // Unik berdasarkan NIP
                    [
                        'auditor_id'   => Str::uuid(),
                        'user_id'      => $listUser[$i]->id, // Mengambil ID dari user ke-i
                        'prodi_id'     => $listProdi[$i]->prodi_id, // Mengambil prodi berbeda tiap loop
                        'nip'          => $nip,
                        'nama_lengkap' => $daftarNama[$i] ?? 'Auditor Terdaftar ' . ($i + 1),
                        'jabatan'      => 'Dosen / Auditor Internal',
                        'no_telp'      => '0812' . rand(10000000, 99999999),
                        'email'        => 'auditor' . ($i + 1) . '@pnc.ac.id',
                        'status_aktif' => true,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]
                );
            }
        }
    }
}