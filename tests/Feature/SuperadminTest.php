<?php

namespace Tests\Feature;

use App\Models\Hospital;
use App\Models\User;
use App\Models\ClinicalPathway;
use App\Models\PatientCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Superadmin can access data from all hospitals via direct URLs.
     */
    public function test_superadmin_can_access_all_hospital_data_via_urls(): void
    {
        $hospital1 = Hospital::factory()->create(['name' => 'Hospital 1', 'code' => 'HOSP001']);
        $hospital2 = Hospital::factory()->create(['name' => 'Hospital 2', 'code' => 'HOSP002']);

        $user1 = User::factory()->create([
            'hospital_id' => $hospital1->id,
            'name' => 'User Hospital 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);
        $user2 = User::factory()->create([
            'hospital_id' => $hospital2->id,
            'name' => 'User Hospital 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        $superadmin = User::factory()->create([
            'hospital_id' => null,
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => User::ROLE_SUPERADMIN,
            'password' => bcrypt('password'),
        ]);

        // Create hospital1 data
        $this->actingAs($user1);
        session(['hospital_id' => $hospital1->id]);
        $pathway1 = ClinicalPathway::create([
            'name' => 'Pathway 1',
            'description' => 'Desc 1',
            'diagnosis_code' => 'D001',
            'version' => '1.0.0',
            'effective_date' => now(),
            'status' => 'active',
            'created_by' => $user1->id,
            'hospital_id' => $hospital1->id,
        ]);
        $case1 = PatientCase::create([
            'patient_id' => 'PAT001',
            'medical_record_number' => 'MRN001',
            'clinical_pathway_id' => $pathway1->id,
            'admission_date' => now(),
            'discharge_date' => now()->addDays(3),
            'primary_diagnosis' => 'Dx1',
            'ina_cbg_code' => 'CBG001',
            'actual_total_cost' => 1000000,
            'ina_cbg_tariff' => 900000,
            'compliance_percentage' => 90.0,
            'cost_variance' => 100000,
            'input_by' => $user1->id,
            'input_date' => now(),
            'hospital_id' => $hospital1->id,
        ]);

        // Create hospital2 data
        $this->actingAs($user2);
        session(['hospital_id' => $hospital2->id]);
        $pathway2 = ClinicalPathway::factory()->create([
            'name' => 'Pathway 2',
            'description' => 'Desc 2',
            'diagnosis_code' => 'D002',
            'version' => '1.0.0',
            'effective_date' => now(),
            'status' => 'active',
            'created_by' => $user2->id,
            'hospital_id' => $hospital2->id,
        ]);
        $case2 = PatientCase::create([
            'patient_id' => 'PAT002',
            'medical_record_number' => 'MRN002',
            'clinical_pathway_id' => $pathway2->id,
            'admission_date' => now(),
            'discharge_date' => now()->addDays(2),
            'primary_diagnosis' => 'Dx2',
            'ina_cbg_code' => 'CBG002',
            'actual_total_cost' => 2000000,
            'ina_cbg_tariff' => 1800000,
            'compliance_percentage' => 85.0,
            'cost_variance' => 200000,
            'input_by' => $user2->id,
            'input_date' => now(),
            'hospital_id' => $hospital2->id,
        ]);

        // As superadmin (no session hospital_id), can view both
        $this->actingAs($superadmin);

        $this->get("/pathways/{$pathway1->id}")->assertStatus(200)->assertSee($pathway1->name);
        $this->get("/cases/{$case1->id}")->assertStatus(200)->assertSee($case1->patient_id);
        $this->get("/pathways/{$pathway2->id}")->assertStatus(200)->assertSee($pathway2->name);
        $this->get("/cases/{$case2->id}")->assertStatus(200)->assertSee($case2->patient_id);

        // Edit pages should be accessible as well
        $this->get("/pathways/{$pathway1->id}/edit")->assertStatus(200);
        $this->get("/pathways/{$pathway2->id}/edit")->assertStatus(200);
        $this->get("/cases/{$case1->id}/edit")->assertStatus(200);
        $this->get("/cases/{$case2->id}/edit")->assertStatus(200);
    }

    /**
     * Superadmin bypasses role checks (has access to all roles).
     */
    public function test_superadmin_bypasses_role_checks(): void
    {
        $superadmin = User::factory()->create([
            'hospital_id' => null,
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => User::ROLE_SUPERADMIN,
            'password' => bcrypt('password'),
        ]);

        $this->assertTrue($superadmin->hasRole(User::ROLE_ADMIN));
        $this->assertTrue($superadmin->hasRole(User::ROLE_MUTU));
        $this->assertTrue($superadmin->hasRole(User::ROLE_KLAIM));
        $this->assertTrue($superadmin->hasRole(User::ROLE_MANAJEMEN));
        $this->assertTrue($superadmin->hasRole(User::ROLE_SUPERADMIN));
    }

    /**
     * SetHospital middleware does not set hospital_id for superadmin.
     */
    public function test_set_hospital_middleware_does_not_set_hospital_for_superadmin(): void
    {
        $hospital = Hospital::factory()->create(['name' => 'Test Hospital', 'code' => 'TEST001']);

        $superadmin = User::factory()->create([
            'hospital_id' => null,
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => User::ROLE_SUPERADMIN,
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($superadmin);
        $response = $this->get('/cases');
        $response->assertSessionMissing('hospital_id');
    }

    /**
     * Index pages should show data from all hospitals for superadmin (scope bypass).
     */
    public function test_superadmin_sees_all_data_on_index_pages(): void
    {
        $hospital1 = Hospital::factory()->create(['name' => 'Hospital 1', 'code' => 'HOSP001']);
        $hospital2 = Hospital::factory()->create(['name' => 'Hospital 2', 'code' => 'HOSP002']);

        $user1 = User::factory()->create(['hospital_id' => $hospital1->id]);
        $user2 = User::factory()->create(['hospital_id' => $hospital2->id]);
        $superadmin = User::factory()->create([
            'hospital_id' => null,
            'role' => User::ROLE_SUPERADMIN,
        ]);

        // Data H1
        $this->actingAs($user1);
        session(['hospital_id' => $hospital1->id]);
        $pathway1 = ClinicalPathway::create([
            'name' => 'CP H1',
            'description' => 'Desc H1',
            'diagnosis_code' => 'D001',
            'version' => '1.0.0',
            'effective_date' => now(),
            'status' => 'active',
            'created_by' => $user1->id,
            'hospital_id' => $hospital1->id,
        ]);

        // Data H2
        $this->actingAs($user2);
        session(['hospital_id' => $hospital2->id]);
        $pathway2 = ClinicalPathway::factory()->create([
            'name' => 'CP H2',
            'description' => 'Desc H2',
            'diagnosis_code' => 'D002',
            'version' => '1.0.0',
            'effective_date' => now(),
            'status' => 'active',
            'created_by' => $user2->id,
            'hospital_id' => $hospital2->id,
        ]);

        // As superadmin, index should show both
        $this->actingAs($superadmin);
        $this->get('/pathways')
            ->assertStatus(200)
            ->assertSee('CP H1')
            ->assertSee('CP H2');
    }
}
