<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CostReference;

class ShowSimrsCostReferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cost-references:show-simrs {--limit=10 : Number of records to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show cost references synced from SIMRS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('Showing cost references synced from SIMRS...');
        
        $costReferences = CostReference::where('is_synced_from_simrs', true)
            ->limit($limit)
            ->get();
            
        if ($costReferences->isEmpty()) {
            $this->info('No cost references found that were synced from SIMRS.');
            return;
        }
        
        $headers = ['ID', 'Service Code', 'SIMRS Code', 'Description', 'Standard Cost', 'Last Synced'];
        $rows = [];
        
        foreach ($costReferences as $costReference) {
            $rows[] = [
                $costReference->id,
                $costReference->service_code,
                $costReference->simrs_kode_brng,
                $costReference->service_description,
                'Rp ' . number_format($costReference->standard_cost, 2, ',', '.'),
                $costReference->last_synced_at ? $costReference->last_synced_at->format('Y-m-d H:i:s') : 'Never',
            ];
        }
        
        $this->table($headers, $rows);
        
        $this->info("Total records: " . $costReferences->count());
    }
}
