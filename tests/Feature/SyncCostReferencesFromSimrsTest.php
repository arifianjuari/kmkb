<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\CostReference;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SyncCostReferencesFromSimrsTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test that the sync command can be called successfully.
     *
     * @return void
     */
    public function test_sync_command_can_be_called()
    {
        // Mock the SIMRS service or database connection
        // For now, we'll just test that the command can be called
        $this->artisan('cost-references:sync-from-simrs', ['--limit' => 5])
             ->expectsOutput('Starting sync of cost references from SIMRS...')
             ->assertExitCode(0);
    }
    
    /**
     * Test that cost references can be created from SIMRS data.
     *
     * @return void
     */
    public function test_cost_references_can_be_created_from_simrs_data()
    {
        // Create a cost reference that would be created from SIMRS
        $costReference = CostReference::create([
            'service_code' => 'TEST001',
            'service_description' => 'Test Item',
            'standard_cost' => 10000,
            'unit' => 'unit',
            'source' => 'SIMRS',
            'simrs_kode_brng' => 'TEST001',
            'is_synced_from_simrs' => true,
        ]);
        
        $this->assertDatabaseHas('cost_references', [
            'service_code' => 'TEST001',
            'simrs_kode_brng' => 'TEST001',
            'is_synced_from_simrs' => true,
        ]);
    }
    
    /**
     * Test that existing cost references can be updated.
     *
     * @return void
     */
    public function test_existing_cost_references_can_be_updated()
    {
        // Create an existing cost reference
        $costReference = CostReference::create([
            'service_code' => 'TEST002',
            'service_description' => 'Old Description',
            'standard_cost' => 5000,
            'unit' => 'unit',
            'source' => 'Manual',
            'simrs_kode_brng' => 'TEST002',
            'is_synced_from_simrs' => false,
        ]);
        
        // Update the cost reference as if from SIMRS sync
        $costReference->update([
            'service_description' => 'New Description',
            'standard_cost' => 15000,
            'source' => 'SIMRS',
            'is_synced_from_simrs' => true,
            'last_synced_at' => now(),
        ]);
        
        $this->assertDatabaseHas('cost_references', [
            'service_code' => 'TEST002',
            'service_description' => 'New Description',
            'standard_cost' => 15000,
            'source' => 'SIMRS',
            'is_synced_from_simrs' => true,
        ]);
    }
}
