<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\PatientCase;
use App\Models\CaseDetail;
use App\Models\User;
use App\Models\Hospital;

class PatientCaseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $hospital;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a hospital and user for testing
        $this->hospital = Hospital::factory()->create();
        $this->user = User::factory()->create();
    }

    public function test_actual_total_cost_is_automatically_calculated_when_case_detail_is_created()
    {
        // Set the hospital context
        session(['hospital_id' => $this->hospital->id]);
        
        // Create a patient case
        $patientCase = PatientCase::factory()->create([
            'hospital_id' => $this->hospital->id,
            'actual_total_cost' => 0
        ]);
        
        // Create case details
        $caseDetail1 = CaseDetail::factory()->create([
            'patient_case_id' => $patientCase->id,
            'actual_cost' => 100000,
            'quantity' => 1
        ]);
        
        $caseDetail2 = CaseDetail::factory()->create([
            'patient_case_id' => $patientCase->id,
            'actual_cost' => 200000,
            'quantity' => 2
        ]);
        
        // Refresh the patient case to get updated values
        $patientCase->refresh();
        
        // The actual_total_cost should be the sum of (actual_cost * quantity) for all case details
        $expectedTotal = (100000 * 1) + (200000 * 2); // 500000
        $this->assertEquals($expectedTotal, $patientCase->actual_total_cost);
    }

    public function test_actual_total_cost_is_automatically_calculated_when_case_detail_is_updated()
    {
        // Set the hospital context
        session(['hospital_id' => $this->hospital->id]);
        
        // Create a patient case
        $patientCase = PatientCase::factory()->create([
            'hospital_id' => $this->hospital->id,
            'actual_total_cost' => 0
        ]);
        
        // Create a case detail
        $caseDetail = CaseDetail::factory()->create([
            'patient_case_id' => $patientCase->id,
            'actual_cost' => 100000,
            'quantity' => 1
        ]);
        
        // Refresh the patient case to get updated values
        $patientCase->refresh();
        
        // Initially, the actual_total_cost should be 100000
        $this->assertEquals(100000, $patientCase->actual_total_cost);
        
        // Update the case detail
        $caseDetail->update([
            'actual_cost' => 150000,
            'quantity' => 2
        ]);
        
        // Refresh the patient case to get updated values
        $patientCase->refresh();
        
        // The actual_total_cost should now be 300000 (150000 * 2)
        $this->assertEquals(300000, $patientCase->actual_total_cost);
    }

    public function test_actual_total_cost_is_automatically_calculated_when_case_detail_is_deleted()
    {
        // Set the hospital context
        session(['hospital_id' => $this->hospital->id]);
        
        // Create a patient case
        $patientCase = PatientCase::factory()->create([
            'hospital_id' => $this->hospital->id,
            'actual_total_cost' => 0
        ]);
        
        // Create case details
        $caseDetail1 = CaseDetail::factory()->create([
            'patient_case_id' => $patientCase->id,
            'actual_cost' => 100000,
            'quantity' => 1
        ]);
        
        $caseDetail2 = CaseDetail::factory()->create([
            'patient_case_id' => $patientCase->id,
            'actual_cost' => 200000,
            'quantity' => 2
        ]);
        
        // Refresh the patient case to get updated values
        $patientCase->refresh();
        
        // The actual_total_cost should be the sum of (actual_cost * quantity) for all case details
        $expectedTotal = (100000 * 1) + (200000 * 2); // 500000
        $this->assertEquals($expectedTotal, $patientCase->actual_total_cost);
        
        // Delete one case detail
        $caseDetail1->delete();
        
        // Refresh the patient case to get updated values
        $patientCase->refresh();
        
        // The actual_total_cost should now be only the cost of the remaining case detail
        $expectedTotal = (200000 * 2); // 400000
        $this->assertEquals($expectedTotal, $patientCase->actual_total_cost);
    }
}
