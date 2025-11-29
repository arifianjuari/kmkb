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
        // Default password for all users
        $defaultPassword = Hash::make('asdfasdf');
        
        // Get the first hospital
        $hospital = \App\Models\Hospital::first();

        // Create superadmin user (not tied to any specific hospital)
        User::updateOrCreate(
            ['email' => 'superadmin@kmkb.online'],
            [
                'name' => 'Super Admin',
                'role' => User::ROLE_SUPERADMIN,
                'department' => 'Global Administration',
                'hospital_id' => null,
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]
        );

        // Define all roles with their details
        $roles = [
            [
                'name' => 'Hospital Admin',
                'role' => User::ROLE_HOSPITAL_ADMIN,
                'department' => 'Administration',
            ],
            [
                'name' => 'Finance Costing',
                'role' => User::ROLE_FINANCE_COSTING,
                'department' => 'Finance',
            ],
            [
                'name' => 'HR Payroll',
                'role' => User::ROLE_HR_PAYROLL,
                'department' => 'HR',
            ],
            [
                'name' => 'Facility Asset',
                'role' => User::ROLE_FACILITY_ASSET,
                'department' => 'Facility',
            ],
            [
                'name' => 'SIMRS Integration',
                'role' => User::ROLE_SIMRS_INTEGRATION,
                'department' => 'IT',
            ],
            [
                'name' => 'Support Unit',
                'role' => User::ROLE_SUPPORT_UNIT,
                'department' => 'Support',
            ],
            [
                'name' => 'Clinical Unit',
                'role' => User::ROLE_CLINICAL_UNIT,
                'department' => 'Clinical',
            ],
            [
                'name' => 'Medrec Claims',
                'role' => User::ROLE_MEDREC_CLAIMS,
                'department' => 'Medical Records',
            ],
            [
                'name' => 'Pathway Team',
                'role' => User::ROLE_PATHWAY_TEAM,
                'department' => 'Quality',
            ],
            [
                'name' => 'Management Auditor',
                'role' => User::ROLE_MANAGEMENT_AUDITOR,
                'department' => 'Audit',
            ],
        ];
        
        // Create users for all roles
        foreach ($roles as $roleData) {
            $email = $roleData['role'] . '@kmkb.online';
            
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $roleData['name'],
                    'role' => $roleData['role'],
                    'department' => $roleData['department'],
                    'hospital_id' => $hospital ? $hospital->id : 1,
                    'password' => $defaultPassword,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
