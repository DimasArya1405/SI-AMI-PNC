<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuditeeSeeder extends Seeder
{
    public function run(): void
    {
        $auditees = [
            [
                'kode_upt' => 'TI',
                'name' => 'Probo Dwi Wahyudi, S.Kom.',
                'email' => 'auditee@mail.com',
                'nip' => '198001011',
                'nama_lengkap' => 'Probo Dwi Wahyudi',
                'no_telp' => '081234567890',
            ],
            [
                'kode_upt' => 'TE',
                'name' => 'Jamaludin, S.Kom.',
                'email' => 'auditee.te@pnc.ac.id',
                'nip' => '198001012',
                'nama_lengkap' => 'Jamaludin',
                'no_telp' => '081234567891',
            ],
            [
                'kode_upt' => 'TM',
                'name' => 'Rudi Hermawan, M.Kom.',
                'email' => 'auditee.tm@pnc.ac.id',
                'nip' => '198001013',
                'nama_lengkap' => 'Rudi Hermawan, M.Kom.',
                'no_telp' => '081234567892',
            ],
            [
                'kode_upt' => 'BAAK',
                'name' => 'Siti Aminah, S.T.',
                'email' => 'auditee.baak@pnc.ac.id',
                'nip' => '198001014',
                'nama_lengkap' => 'Siti Aminah, S.T.',
                'no_telp' => '081234567893',
            ],
            [
                'kode_upt' => 'BAU',
                'name' => 'Dewi Lestari, S.E.',
                'email' => 'auditee.bau@pnc.ac.id',
                'nip' => '198001015',
                'nama_lengkap' => 'Dewi Lestari, S.E.',
                'no_telp' => '081234567894',
            ],
        ];

        DB::transaction(function () use ($auditees) {
            foreach ($auditees as $auditeeData) {
                $upt = DB::table('upt')
                    ->where('kode_upt', $auditeeData['kode_upt'])
                    ->first();

                if (!$upt) {
                    continue;
                }

                $now = now();
                $user = DB::table('users')
                    ->where('email', $auditeeData['email'])
                    ->first();

                if ($user) {
                    $userId = $user->id;

                    DB::table('users')
                        ->where('id', $userId)
                        ->update([
                            'name' => $auditeeData['name'],
                            'password' => Hash::make('password123'),
                            'role' => 'auditee',
                            'deleted_at' => null,
                            'updated_at' => $now,
                        ]);
                } else {
                    $userId = (string) Str::uuid();

                    DB::table('users')->insert([
                        'id' => $userId,
                        'name' => $auditeeData['name'],
                        'email' => $auditeeData['email'],
                        'password' => Hash::make('password123'),
                        'role' => 'auditee',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $auditee = DB::table('auditee')
                    ->where('upt_id', $upt->upt_id)
                    ->first();

                $auditeePayload = [
                    'user_id' => $userId,
                    'upt_id' => $upt->upt_id,
                    'nip' => $auditeeData['nip'],
                    'nama_lengkap' => $auditeeData['nama_lengkap'],
                    'no_telp' => $auditeeData['no_telp'],
                    'email' => $auditeeData['email'],
                    'status_aktif' => true,
                    'deleted_at' => null,
                    'updated_at' => $now,
                ];

                if ($auditee) {
                    DB::table('auditee')
                        ->where('auditee_id', $auditee->auditee_id)
                        ->update($auditeePayload);
                } else {
                    DB::table('auditee')->insert(array_merge($auditeePayload, [
                        'auditee_id' => (string) Str::uuid(),
                        'created_at' => $now,
                    ]));
                }
            }
        });
    }
}
