<?php

namespace Tests\Unit;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HospitalHelperTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that hospital_cache_key function prefixes keys with hospital ID.
     *
     * @return void
     */
    public function test_hospital_cache_key_prefixes_with_hospital_id()
    {
        // Create a hospital
        $hospital = Hospital::factory()->create([
            'name' => 'Test Hospital',
            'code' => 'TEST001'
        ]);

        // Create a user for the hospital
        $user = User::factory()->create([
            'hospital_id' => $hospital->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Acting as the user
        $this->actingAs($user);
        session(['hospital_id' => $hospital->id]);

        // Test that cache keys are properly prefixed
        $cacheKey = hospital_cache_key('test_key');
        $this->assertEquals("hospital_{$hospital->id}_test_key", $cacheKey);

        // Test with another key
        $anotherKey = hospital_cache_key('another_test_key');
        $this->assertEquals("hospital_{$hospital->id}_another_test_key", $anotherKey);
    }

    /**
     * Test that hospital_cache_key function returns original key when no hospital context.
     *
     * @return void
     */
    public function test_hospital_cache_key_returns_original_when_no_hospital()
    {
        // Test without user authentication
        $cacheKey = hospital_cache_key('test_key');
        $this->assertEquals('test_key', $cacheKey);

        // Test with user but no hospital_id in session (but user has hospital_id)
        $hospital = Hospital::factory()->create([
            'name' => 'Test Hospital 2',
            'code' => 'TEST002'
        ]);

        $user = User::factory()->create([
            'hospital_id' => $hospital->id,
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password')
        ]);

        $this->actingAs($user);
        session()->forget('hospital_id');

        $cacheKey = hospital_cache_key('test_key');
        $this->assertEquals("hospital_{$hospital->id}_test_key", $cacheKey);

        // Test with no user authenticated
        auth()->logout();
        session()->flush();

        $cacheKey = hospital_cache_key('test_key');
        $this->assertEquals('test_key', $cacheKey);
    }

    /**
     * Test that hospital_storage_path function generates tenant-specific storage paths.
     *
     * @return void
     */
    public function test_hospital_storage_path_generates_tenant_specific_paths()
    {
        // Test without user authentication
        $storagePath = hospital_storage_path('test_file.txt');
        $this->assertEquals(storage_path('test_file.txt'), $storagePath);

        // Create a hospital
        $hospital = Hospital::factory()->create([
            'name' => 'Test Hospital',
            'code' => 'TEST001'
        ]);

        // Create a user for the hospital
        $user = User::factory()->create([
            'hospital_id' => $hospital->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Acting as the user
        $this->actingAs($user);
        session(['hospital_id' => $hospital->id]);

        // Test that storage paths are properly prefixed
        $storagePath = hospital_storage_path('test_file.txt');
        $expectedPath = storage_path("framework/tenant_{$hospital->id}/test_file.txt");
        $this->assertEquals($expectedPath, $storagePath);

        // Test with empty path
        $storagePath = hospital_storage_path();
        $expectedPath = storage_path("framework/tenant_{$hospital->id}/");
        $this->assertEquals($expectedPath, $storagePath);
    }
}
