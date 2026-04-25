<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin User',
                'email' => 'admin@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Auditee User',
                'email' => 'auditee@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'auditee',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Dosen User',
                'email' => 'dosen@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
            ],
            // 5 Data User dengan Role Auditor
            [
                'id' => (string) Str::uuid(),
                'name' => 'Hendra Wijaya, M.Eng.',
                'email' => 'auditor1@pnc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Dr. Aris Sudaryono',
                'email' => 'auditor2@pnc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Siti Aminah, S.T., M.T.',
                'email' => 'auditor3@pnc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Budi Santoso, M.Kom.',
                'email' => 'auditor4@pnc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Lani Cahyani, M.Pd.',
                'email' => 'auditor5@pnc.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}