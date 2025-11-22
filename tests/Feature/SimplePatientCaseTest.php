<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PatientCase;
use App\Models\CaseDetail;

class SimplePatientCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_actual_total_cost_is_automatically_calculated()
    {
        // Create a patient case
        $patientCase = PatientCase::create([
            'patient_id' => 'PAT001',
            'medical_record_number' => 'MRN001',
            'clinical_pathway_id' => 1,
            'admission_date' => '2025-01-01',
            'discharge_date' => '2025-01-10',
            'primary_diagnosis' => 'Test diagnosis',
            'ina_cbg_code' => 'CBG001',
            'actual_total_cost' => 0,
            'ina_cbg_tariff' => 1000000,
            'hospital_id' => 1,
            'input_by' => 'Test User',
        ]);
        
        // Initially, actual_total_cost should be 0
        $this->assertEquals(0, $patientCase->actual_total_cost);
        
        // Create case details
        $caseDetail1 = CaseDetail::create([
            'patient_case_id' => $patientCase->id,
            'service_item' => 'Test service 1',
            'actual_cost' => 100000,
            'quantity' => 1,
            'hospital_id' => 1,
            'status' => 'completed',
        ]);
        
        // Refresh the patient case to get updated values
        $patientCase->refresh();
        
        // The actual_total_cost should be the sum of (actual_cost * quantity) for all case details
        $expectedTotal = (100000 * 1); // 100000
        $this->assertEquals($expectedTotal, $patientCase->actual_total_cost);
        
        // Create another case detail
        $caseDetail2 = CaseDetail::create([
            'patient_case_id' => $patientCase->id,
            'service_item' => 'Test service 2',
            'actual_cost' => 200000,
            'quantity' => 2,
            'hospital_id' => 1,
            'status' => 'completed',
        ]);
        
        // Refresh the patient case to get updated values
        $patientCase->refresh();
        
        // The actual_total_cost should be the sum of (actual_cost * quantity) for all case details
        $expectedTotal = (100000 * 1) + (200000 * 2); // 500000
        $this->assertEquals($expectedTotal, $patientCase->actual_total_cost);
    }
}
