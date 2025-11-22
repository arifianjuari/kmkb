<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PatientCase;
use App\Models\ClinicalPathway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestPatientCaseCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-patient-case-creation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test patient case creation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing patient case creation...');
        
        // Set hospital context
        session(['hospital_id' => 1]);
        
        // Get a clinical pathway to use
        $clinicalPathway = ClinicalPathway::first();
        
        if (!$clinicalPathway) {
            $this->error('No clinical pathway found. Please seed the database first.');
            return;
        }
        
        $this->info('Using clinical pathway: ' . $clinicalPathway->name);
        
        // Create a test patient case
        DB::beginTransaction();
        try {
            $case = new PatientCase();
            $case->patient_id = uniqid();
            $case->medical_record_number = 'TEST-' . time();
            $case->clinical_pathway_id = $clinicalPathway->id;
            $case->admission_date = now()->toDateString();
            $case->primary_diagnosis = 'Test Diagnosis';
            $case->ina_cbg_code = 'TEST-CBG';
            $case->actual_total_cost = 1000000;
            $case->ina_cbg_tariff = 900000;
            $case->input_by = 1; // Assuming user ID 1 exists
            $case->input_date = now();
            $case->hospital_id = 1; // Assuming hospital ID 1 exists
            
            // Set default values for fields that don't have defaults in the database
            $case->cost_variance = 1000000 - 900000;
            $case->compliance_percentage = 0; // Will be calculated after saving
            
            $this->info('Attempting to save patient case...');
            \Log::info('Test patient case data:', $case->toArray());
            
            $case->save();
            
            $this->info('Patient case created successfully with ID: ' . $case->id);
            \Log::info('Test patient case created successfully with ID: ' . $case->id);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Failed to create patient case: ' . $e->getMessage());
            \Log::error('Failed to create test patient case: ' . $e->getMessage());
            return;
        }
    }
}
