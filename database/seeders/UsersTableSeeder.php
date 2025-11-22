<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin user (not tied to any specific hospital)
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'role' => User::ROLE_SUPERADMIN,
                'department' => 'Global Administration',
                'hospital_id' => null,
                'password' => Hash::make('password'), // default password
                'email_verified_at' => now(),
            ]
        );

        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role' => User::ROLE_ADMIN,
                'department' => 'Administration',
            ],
            [
                'name' => 'Mutu User',
                'email' => 'mutu@example.com',
                'role' => User::ROLE_MUTU,
                'department' => 'Mutu',
            ],
            [
                'name' => 'Klaim User',
                'email' => 'klaim@example.com',
                'role' => User::ROLE_KLAIM,
                'department' => 'Klaim',
            ],
            [
                'name' => 'Manajemen User',
                'email' => 'manajemen@example.com',
                'role' => User::ROLE_MANAJEMEN,
                'department' => 'Manajemen',
            ],
        ];

        // Get the first hospital
        $hospital = \App\Models\Hospital::first();
        
        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'department' => $data['department'],
                    'hospital_id' => $hospital ? $hospital->id : 1,
                    'password' => Hash::make('password'), // default password
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
