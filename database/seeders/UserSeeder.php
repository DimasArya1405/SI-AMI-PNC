<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@mail.com',
                'password' => Hash::make('password123'), // Ini cara nge-hash yang benar
                'role' => 'admin',
            ],
            [
                'name' => 'Auditor User',
                'email' => 'auditor@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'auditor',
            ],
            [
                'name' => 'Auditee User',
                'email' => 'auditee@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'auditee',
            ],
            [
                'name' => 'Dosen User',
                'email' => 'dosen@mail.com',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
