<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimrsService;
use App\Models\CostReference;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

class SyncCostReferencesFromSimrs extends Command
{
    /**
     * Send error notification
     *
     * @param string $errorMessage
     * @param array $context
     * @return void
     */
    protected function sendErrorNotification($errorMessage, $context = [])
    {
        // For now, we'll just log the notification
        // In a real application, you might want to send an email or Slack notification
        Log::alert('SIMRS Cost Reference Sync Error Notification', [
            'error' => $errorMessage,
            'context' => $context,
            'timestamp' => now()
        ]);
        
        // Example of how you might send an email notification:
        /*
        if (config('mail.admin_address')) {
            Mail::raw("SIMRS sync error: {$errorMessage}", function ($message) {
                $message->to(config('mail.admin_address'))
                        ->subject('SIMRS Cost Reference Sync Error');
            });
        }
        */
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cost-references:sync-from-simrs {--limit=100 : Number of records to sync} {--hospital-id= : Hospital ID to sync for} {--use-user-hospital : Use the hospital ID from the authenticated user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync cost references from SIMRS master barang data';

    /**
     * Execute the console command.
     */
    public function handle(SimrsService $simrsService)
    {
        $limit = $this->option('limit');
        $hospitalId = $this->option('hospital-id');
        $useUserHospital = $this->option('use-user-hospital');
        
        // If use-user-hospital option is set, get hospital ID from authenticated user
        if ($useUserHospital) {
            // For console commands, we need to manually get the user
            // In a web context, this would be auth()->user()->hospital_id
            // For now, we'll keep the existing logic but note that in web context
            // we would use the user's hospital_id
        }
        
        $this->info('Starting sync of cost references from SIMRS...');
        
        // Log start of sync process
        Log::info('Starting SIMRS cost references sync', [
            'limit' => $limit,
            'hospital_id' => $hospitalId,
            'timestamp' => now()
        ]);
        
        try {
            // Test SIMRS connection first
            if (!$simrsService->testConnection()) {
                $this->error('Failed to connect to SIMRS database');
                return Command::FAILURE;
            }
            
            $this->info('Successfully connected to SIMRS database');
            
            // Get master barang data from SIMRS
            $result = $simrsService->getMasterBarang($limit, 0);
            $items = $result['data'];
            
            $this->info("Found {$result['total']} master barang records, syncing {$limit} records...");
            
            $syncedCount = 0;
            $createdCount = 0;
            $updatedCount = 0;
            
            foreach ($items as $item) {
                // Skip items without harga beli
                if (!isset($item->harga_beli_dasar) || $item->harga_beli_dasar <= 0) {
                    continue;
                }
                
                // Look for existing cost reference with this SIMRS kode_brng
                $costReference = CostReference::where('simrs_kode_brng', $item->kode_brng)
                    ->when($hospitalId, function($query, $hospitalId) {
                        return $query->where('hospital_id', $hospitalId);
                    })
                    ->first();
                
                // Prepare data for cost reference
                $data = [
                    'service_code' => $item->kode_brng,
                    'service_description' => $item->nama_brng,
                    'standard_cost' => $item->harga_beli_dasar ?? 0,
                    'purchase_price' => $item->harga_beli_dasar ?? 0,
                    'unit' => 'unit', // Default unit, adjust as needed
                    'source' => 'SIMRS',
                    'simrs_kode_brng' => $item->kode_brng,
                    'is_synced_from_simrs' => true,
                    'last_synced_at' => now(),
                ];
                
                // Set hospital_id based on options
                if ($hospitalId) {
                    $data['hospital_id'] = $hospitalId;
                } elseif ($useUserHospital) {
                    // In a web context, we would use auth()->user()->hospital_id
                    // For console commands, this would need to be passed differently
                    // We'll handle this in the web controller
                }
                // If no hospital_id provided and this is a global sync, we'll create records without hospital_id
                
                if ($costReference) {
                    // Update existing record
                    $costReference->update($data);
                    $updatedCount++;
                    
                    // Log update
                    Log::debug('Updated existing cost reference from SIMRS', [
                        'service_code' => $item->kode_brng,
                        'hospital_id' => $hospitalId
                    ]);
                } else {
                    // Create new record
                    // Check if there's already a cost reference with the same service_code
                    $existing = CostReference::where('service_code', $item->kode_brng)
                        ->when($hospitalId, function($query, $hospitalId) {
                            return $query->where('hospital_id', $hospitalId);
                        })
                        ->first();
                    
                    if ($existing) {
                        // Update existing record
                        $existing->update($data);
                        $updatedCount++;
                        
                        // Log update
                        Log::debug('Updated existing cost reference by service_code from SIMRS', [
                            'service_code' => $item->kode_brng,
                            'hospital_id' => $hospitalId
                        ]);
                    } else {
                        // Create new record
                        try {
                            CostReference::create($data);
                            $createdCount++;
                            
                            // Log creation
                            Log::debug('Created new cost reference from SIMRS', [
                                'service_code' => $item->kode_brng,
                                'hospital_id' => $hospitalId
                            ]);
                        } catch (\Exception $e) {
                            $this->warn("Failed to create record for {$item->kode_brng}: " . $e->getMessage());
                            Log::warning("Failed to create cost reference for {$item->kode_brng}", [
                                'error' => $e->getMessage(),
                                'service_code' => $item->kode_brng,
                                'hospital_id' => $hospitalId
                            ]);
                        }
                    }
                }
                
                $syncedCount++;
            }
            
            $this->info("Sync completed successfully!");
            $this->info("Total records processed: {$syncedCount}");
            $this->info("Records created: {$createdCount}");
            $this->info("Records updated: {$updatedCount}");
            
            // Log completion of sync process
            Log::info('SIMRS cost references sync completed successfully', [
                'total_processed' => $syncedCount,
                'records_created' => $createdCount,
                'records_updated' => $updatedCount,
                'timestamp' => now()
            ]);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            Log::error('Error syncing cost references from SIMRS: ' . $e->getMessage());
            $this->error('Error syncing cost references from SIMRS: ' . $e->getMessage());
            
            // Send error notification
            $this->sendErrorNotification($e->getMessage(), [
                'limit' => $limit,
                'hospital_id' => $hospitalId
            ]);
            
            return Command::FAILURE;
        }
    }
}
