<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Auditor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil semua User dengan role auditor
        $users = User::where('role', 'auditor')->get();

        // 2. Ambil semua UUID dari tabel prodi
        // Pastikan Anda sudah menjalankan ProdiSeeder sebelumnya!
        $prodiIds = DB::table('prodi')->pluck('prodi_id')->toArray();

        if (empty($prodiIds)) {
            throw new \Exception("Tabel 'prodi' kosong! Jalankan ProdiSeeder terlebih dahulu.");
        }

        $dataAuditor = [
            ['nip' => '199001012026041001', 'jabatan' => 'Lektor'],
            ['nip' => '199001012026041002', 'jabatan' => 'Kepala SPMI'],
            ['nip' => '199001012026041003', 'jabatan' => 'Sekretaris SPMI'],
            ['nip' => '199001012026041004', 'jabatan' => 'Dosen'],
            ['nip' => '199001012026041005', 'jabatan' => 'Dosen'],
        ];

        foreach ($users as $index => $user) {
            // Gunakan array_random atau ambil indeks secara bergantian agar tidak null
            $randomProdiId = $prodiIds[array_rand($prodiIds)];

            Auditor::create([
                'auditor_id'   => (string) Str::uuid(),
                'user_id'      => $user->id,
                'nip'          => $dataAuditor[$index]['nip'],
                'nama_lengkap' => $user->name,
                'jabatan'      => $dataAuditor[$index]['jabatan'],
                'prodi_id'     => $randomProdiId, // SEKARANG TIDAK NULL
                'no_telp'      => '0812' . rand(11111111, 99999999),
                'email'        => $user->email,
                'status_aktif' => true,
            ]);
        }
    }
}