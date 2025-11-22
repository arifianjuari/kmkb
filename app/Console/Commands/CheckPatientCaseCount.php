<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPatientCaseCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-patient-case-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the count of patient cases in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = DB::table('patient_cases')->count();
        $this->info("There are {$count} patient cases in the database.");
        
        if ($count > 0) {
            $this->info('Database connection and patient case creation are working correctly.');
        } else {
            $this->warn('No patient cases found in the database.');
        }
    }
}
