<?php

namespace Database\Seeders;

use App\Models\ClinicalPathway;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClinicalPathwaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospital = Hospital::first();
        if (!$hospital) {
            $hospital = Hospital::create([
                'code' => 'HOSP1',
                'name' => 'Default Hospital',
                'is_active' => true,
            ]);
        }

        $creator = User::where('email', 'admin@example.com')->first() ?: User::first();
        if (!$creator) {
            $creator = User::create([
                'name' => 'Seeder Admin',
                'email' => 'seed-admin@example.com',
                'password' => bcrypt('password'),
                'role' => method_exists(User::class, 'ROLE_ADMIN') ? User::ROLE_ADMIN : 'admin',
                'hospital_id' => $hospital->id,
            ]);
        }

        // Ensure at least 3 clinical pathways exist
        if (ClinicalPathway::count() < 3) {
            $names = ['Appendectomy Adult', 'Pneumonia Moderate', 'Cesarean Section'];
            foreach ($names as $i => $name) {
                ClinicalPathway::updateOrCreate(
                    ['name' => $name, 'hospital_id' => $hospital->id],
                    [
                        'description' => 'Seeded clinical pathway: ' . $name,
                        'diagnosis_code' => 'D' . str_pad((string)($i + 10), 3, '0', STR_PAD_LEFT),
                        'version' => '1',
                        'effective_date' => now()->toDateString(),
                        'status' => 'active',
                        'created_by' => $creator->id,
                        'hospital_id' => $hospital->id,
                    ]
                );
            }
        }
    }
}
