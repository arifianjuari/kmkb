<?php

namespace Tests\Feature;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantScopingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users can only access data from their own hospital.
     *
     * @return void
     */
    public function test_user_can_only_access_own_hospital_data()
    {
        // Create two hospitals
        $hospital1 = Hospital::factory()->create([
            'name' => 'Hospital 1',
            'code' => 'HOSP001',
            'logo_path' => 'logo1.png',
            'theme_color' => '#FF0000'
        ]);

        $hospital2 = Hospital::factory()->create([
            'name' => 'Hospital 2',
            'code' => 'HOSP002',
            'logo_path' => 'logo2.png',
            'theme_color' => '#00FF00'
        ]);

        // Create users for each hospital
        $user1 = User::factory()->create([
            'hospital_id' => $hospital1->id,
            'name' => 'User Hospital 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password')
        ]);

        $user2 = User::factory()->create([
            'hospital_id' => $hospital2->id,
            'name' => 'User Hospital 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password')
        ]);

        // Acting as user1, create some data for hospital1
        $this->actingAs($user1);
        session(['hospital_id' => $hospital1->id]);
        // Refresh the application to ensure middleware is applied
        $this->app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
        
        // Create a clinical pathway for hospital1
        $pathway1 = \App\Models\ClinicalPathway::create([
            'name' => 'Pathway 1',
            'description' => 'Description for pathway 1',
            'diagnosis_code' => 'D001',
            'version' => '1.0.0',
            'effective_date' => now(),
            'status' => 'active',
            'created_by' => $user1->id,
            'hospital_id' => $hospital1->id
        ]);
        
        // Create a cost reference for hospital1
        $costReference1 = \App\Models\CostReference::factory()->create([
            'service_code' => 'CR001',
            'service_description' => 'Cost Reference 1',
            'hospital_id' => $hospital1->id
        ]);
        
        // Acting as user2, create some data for hospital2
        $this->actingAs($user2);
        session(['hospital_id' => $hospital2->id]);
        // Refresh the application to ensure middleware is applied
        $this->app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
        
        // Create a clinical pathway for hospital2
        $pathway2 = \App\Models\ClinicalPathway::factory()->create([
            'name' => 'Pathway 2',
            'description' => 'Description for pathway 2',
            'diagnosis_code' => 'D002',
            'version' => '1.0.0',
            'effective_date' => now(),
            'status' => 'active',
            'created_by' => $user2->id,
            'hospital_id' => $hospital2->id
        ]);
        
        // Create a cost reference for hospital2
        $costReference2 = \App\Models\CostReference::factory()->create([
            'service_code' => 'CR002',
            'service_description' => 'Cost Reference 2',
            'hospital_id' => $hospital2->id
        ]);
        
        // Acting as user1, verify they can only see hospital1 data
        $this->actingAs($user1);
        session(['hospital_id' => $hospital1->id]);
        // Refresh the application to ensure middleware is applied
        $this->app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
        
        $response = $this->get('/pathways');
        $response->assertStatus(200);
        $response->assertSee($pathway1->name);
        $response->assertDontSee($pathway2->name);
        
        $response = $this->get('/cost-references');
        $response->assertStatus(200);
        $response->assertSee($costReference1->service_description);
        $response->assertDontSee($costReference2->service_description);
        
        // Acting as user2, verify they can only see hospital2 data
        $this->actingAs($user2);
        session(['hospital_id' => $hospital2->id]);
        // Refresh the application to ensure middleware is applied
        $this->app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
        
        $response = $this->get('/pathways');
        $response->assertStatus(200);
        $response->assertSee($pathway2->name);
        $response->assertDontSee($pathway1->name);
        
        $response = $this->get('/cost-references');
        $response->assertStatus(200);
        $response->assertSee($costReference2->service_description);
        $response->assertDontSee($costReference1->service_description);
    }

    /**
     * Test that users cannot access data from other hospitals via direct URLs.
     *
     * @return void
     */
    public function test_user_cannot_access_other_hospital_data_via_urls()
    {
        // Create two hospitals
        $hospital1 = Hospital::factory()->create([
            'name' => 'Hospital 1',
            'code' => 'HOSP001'
        ]);

        $hospital2 = Hospital::factory()->create([
            'name' => 'Hospital 2',
            'code' => 'HOSP002'
        ]);

        // Create users for each hospital
        $user1 = User::factory()->create([
            'hospital_id' => $hospital1->id,
            'name' => 'User Hospital 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password')
        ]);

        $user2 = User::factory()->create([
            'hospital_id' => $hospital2->id,
            'name' => 'User Hospital 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password')
        ]);

        // Acting as user1, create some data for hospital1
        $this->actingAs($user1);
        session(['hospital_id' => $hospital1->id]);
        
        $pathway1 = \App\Models\ClinicalPathway::create([
            'name' => 'Pathway 1',
            'description' => 'Description for pathway 1',
            'diagnosis_code' => 'D001',
            'version' => '1.0.0',
            'effective_date' => now(),
            'status' => 'active',
            'created_by' => $user1->id,
            'hospital_id' => $hospital1->id
        ]);
        
        $case1 = \App\Models\PatientCase::create([
            'patient_id' => 'PAT001',
            'medical_record_number' => 'MRN001',
            'clinical_pathway_id' => $pathway1->id,
            'admission_date' => now(),
            'discharge_date' => now()->addDays(5),
            'primary_diagnosis' => 'Diagnosis 1',
            'ina_cbg_code' => 'CBG001',
            'actual_total_cost' => 1000000,
            'ina_cbg_tariff' => 900000,
            'compliance_percentage' => 95.5,
            'cost_variance' => 100000,
            'input_by' => $user1->id,
            'input_date' => now(),
            'hospital_id' => $hospital1->id
        ]);

        // Acting as user2, try to access hospital1's data
        $this->actingAs($user2);
        session(['hospital_id' => $hospital2->id]);
        // Refresh the application to ensure middleware is applied
        $this->app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
        
        // Try to access hospital1's pathway - should get 404
        $response = $this->get("/pathways/{$pathway1->id}");
        $response->assertStatus(404);
        
        // Try to access hospital1's patient case - should get 404
        $response = $this->get("/cases/{$case1->id}");
        $response->assertStatus(404);
        
        // Try to edit hospital1's pathway - should get 404
        $response = $this->get("/pathways/{$pathway1->id}/edit");
        $response->assertStatus(404);
        
        // Try to edit hospital1's patient case - should get 404
        $response = $this->get("/cases/{$case1->id}/edit");
        $response->assertStatus(404);
    }
}
