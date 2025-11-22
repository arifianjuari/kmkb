<?php

namespace Database\Seeders;

use App\Models\ClinicalPathway;
use App\Models\CostReference;
use App\Models\PathwayStep;
use Illuminate\Database\Seeder;

class PathwayStepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $costRefs = CostReference::all();
        if ($costRefs->isEmpty()) {
            // Ensure cost references exist from CostReferencesTableSeeder
            $this->call(CostReferencesTableSeeder::class);
            $costRefs = CostReference::all();
        }

        ClinicalPathway::query()->each(function (ClinicalPathway $cp) use ($costRefs) {
            // Create 5 ordered steps per clinical pathway if none exist yet
            if ($cp->id && PathwayStep::where('clinical_pathway_id', $cp->id)->count() === 0) {
                for ($i = 1; $i <= 5; $i++) {
                    $ref = $costRefs->random();
                    PathwayStep::create([
                        'clinical_pathway_id' => $cp->id,
                        'step_order' => $i,
                        'display_order' => $i,
                        'description' => "Step $i for {$cp->name}",
                        'service_code' => 'Tindakan sesuai prosedur',
                        'criteria' => null,
                        'estimated_cost' => $ref->standard_cost,
                        'quantity' => rand(1, 3),
                        'cost_reference_id' => $ref->id,
                        'hospital_id' => $cp->hospital_id,
                    ]);
                }
            }
        });
    }
}
