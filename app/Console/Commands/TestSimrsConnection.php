<?php

namespace App\Console\Commands;

use App\Services\SimrsService;
use Illuminate\Console\Command;

class TestSimrsConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simrs:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to SIMRS database';

    protected $simrsService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SimrsService $simrsService)
    {
        parent::__construct();
        $this->simrsService = $simrsService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing connection to SIMRS database...');
        
        $connected = $this->simrsService->testConnection();
        
        if ($connected) {
            $this->info('✓ Successfully connected to SIMRS database');
            
            // Test fetching some data
            $this->info('Fetching sample data...');
            $masterBarang = $this->simrsService->getMasterBarang(5);
            $this->info('✓ Fetched ' . count($masterBarang) . ' master barang records');
            
            return 0;
        } else {
            $this->error('✗ Failed to connect to SIMRS database');
            return 1;
        }
    }
}
