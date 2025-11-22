<?php

namespace Tests\Feature;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetHospitalMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that middleware sets hospital context for authenticated users.
     *
     * @return void
     */
    public function test_middleware_sets_hospital_context_for_authenticated_users()
    {
        // Create a hospital
        $hospital = Hospital::factory()->create([
            'name' => 'Test Hospital',
            'code' => 'TEST001'
        ]);

        // Create a user with hospital_id
        $user = User::factory()->create([
            'hospital_id' => $hospital->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Acting as the user
        $this->actingAs($user);

        // Make a request to a route that uses the set.hospital middleware
        $response = $this->get('/cases');

        // Verify that the hospital_id is set in the session
        $response->assertSessionHas('hospital_id', $hospital->id);
    }

    /**
     * Test that middleware does not set hospital context for unauthenticated users.
     *
     * @return void
     */
    public function test_middleware_does_not_set_hospital_context_for_unauthenticated_users()
    {
        // Make a request without authentication
        $response = $this->get('/cases');

        // Verify that the hospital_id is not set in the session
        $response->assertSessionMissing('hospital_id');
    }
}
