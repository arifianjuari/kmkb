<?php

namespace Database\Seeders;

use App\Models\CaseDetail;
use App\Models\PatientCase;
use App\Models\PathwayStep;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = PatientCase::with('clinicalPathway')->get();
        if ($cases->isEmpty()) {
            // Ensure dependencies exist
            $this->call([
                PatientCasesTableSeeder::class,
                PathwayStepsTableSeeder::class,
            ]);
            $cases = PatientCase::with('clinicalPathway')->get();
        }

        foreach ($cases as $case) {
            // Skip if already seeded
            if (CaseDetail::where('patient_case_id', $case->id)->exists()) {
                continue;
            }

            // Link some details to pathway steps for this pathway
            $steps = PathwayStep::where('clinical_pathway_id', $case->clinical_pathway_id)
                ->orderBy('display_order')
                ->take(3)
                ->get();

            foreach ($steps as $step) {
                CaseDetail::create([
                    'patient_case_id' => $case->id,
                    'pathway_step_id' => $step->id,
                    'service_item' => $step->description,
                    'service_code' => optional($step->costReference)->service_code ?? 'SRV-' . $step->id,
                    'status' => 'completed',
                    'performed' => true,
                    'quantity' => $step->quantity ?? 1,
                    'actual_cost' => $step->estimated_cost ?? 0,
                    'service_date' => now()->subDays(rand(1, 10))->toDateString(),
                    'hospital_id' => $case->hospital_id,
                ]);
            }

            // Add a couple of custom steps (pathway_step_id null)
            for ($i = 1; $i <= 2; $i++) {
                CaseDetail::create([
                    'patient_case_id' => $case->id,
                    'pathway_step_id' => null,
                    'service_item' => 'Custom Service ' . $i,
                    'service_code' => 'CUST-' . $case->id . '-' . $i,
                    'status' => 'completed',
                    'performed' => true,
                    'quantity' => rand(1, 2),
                    'actual_cost' => rand(50000, 250000),
                    'service_date' => now()->subDays(rand(1, 10))->toDateString(),
                    'hospital_id' => $case->hospital_id,
                ]);
            }

            // Recalculate totals at DB level: sum(actual_cost * quantity)
            $total = CaseDetail::where('patient_case_id', $case->id)
                ->sum(DB::raw('COALESCE(actual_cost,0) * COALESCE(NULLIF(quantity,0),1)'));

            $case->actual_total_cost = $total;
            $case->cost_variance = ($case->ina_cbg_tariff !== null) ? ($total - $case->ina_cbg_tariff) : null;
            $case->save();
        }
    }
}
