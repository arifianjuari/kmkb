<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckClinicalPathways extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-clinical-pathways';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the clinical pathways in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pathways = DB::table('clinical_pathways')->get();
        $this->info("There are " . $pathways->count() . " clinical pathways in the database.");
        
        foreach ($pathways as $pathway) {
            $this->line("ID: {$pathway->id}, Name: {$pathway->name}");
        }
        
        if ($pathways->count() == 0) {
            $this->warn('No clinical pathways found in the database. You may need to run the seeders.');
        }
    }
}
