<?php

namespace Database\Seeders;

use App\Models\ClinicalPathway;
use App\Models\Hospital;
use App\Models\PatientCase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PatientCasesTableSeeder extends Seeder
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

        $user = User::where('email', 'admin@example.com')->first() ?: User::first();
        if (!$user) {
            $user = User::factory()->create(['hospital_id' => $hospital->id]);
        }

        $pathways = ClinicalPathway::take(3)->get();
        if ($pathways->isEmpty()) {
            $this->call(ClinicalPathwaysTableSeeder::class);
            $pathways = ClinicalPathway::take(3)->get();
        }

        // Create a few patient cases per pathway if none exist for this hospital
        foreach ($pathways as $cp) {
            for ($i = 0; $i < 3; $i++) {
                PatientCase::updateOrCreate(
                    [
                        'medical_record_number' => 'MRN-' . $cp->id . '-' . str_pad((string)($i + 1), 3, '0', STR_PAD_LEFT),
                    ],
                    [
                        'patient_id' => 'PAT-' . $cp->id . '-' . ($i + 1),
                        'clinical_pathway_id' => $cp->id,
                        'admission_date' => now()->subDays(rand(10, 30))->toDateString(),
                        'discharge_date' => now()->subDays(rand(1, 9))->toDateString(),
                        'primary_diagnosis' => $cp->description ?: 'Primary diagnosis',
                        'ina_cbg_code' => 'CBG' . rand(100, 999),
                        'actual_total_cost' => 0, // will be recalculated after details are seeded
                        'ina_cbg_tariff' => rand(1000000, 5000000),
                        'compliance_percentage' => rand(70, 100),
                        'cost_variance' => null,
                        'input_by' => $user->id,
                        'input_date' => now(),
                        'hospital_id' => $cp->hospital_id ?? $hospital->id,
                    ]
                );
            }
        }
    }
}
