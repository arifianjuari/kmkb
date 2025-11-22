<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckHospitals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-hospitals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the hospitals in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hospitals = DB::table('hospitals')->get();
        $this->info("There are " . $hospitals->count() . " hospitals in the database.");
        
        foreach ($hospitals as $hospital) {
            $this->line("ID: {$hospital->id}, Name: {$hospital->name}");
        }
        
        if ($hospitals->count() == 0) {
            $this->warn('No hospitals found in the database. You may need to run the seeders.');
        }
    }
}
