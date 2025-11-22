<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckHospitalContext extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-hospital-context';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the hospital context';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Simulate a logged in user
        $user = \App\Models\User::first();
        if (!$user) {
            $this->error('No user found in the database.');
            return;
        }
        
        Auth::login($user);
        
        // Check hospital context
        $hospitalId = session('hospital_id', auth()->user()->hospital_id ?? null);
        $this->info("Hospital ID from session: " . ($hospitalId ?: 'null'));
        
        $hospital = \App\Models\Hospital::find($hospitalId);
        if ($hospital) {
            $this->info("Hospital name: " . $hospital->name);
        } else {
            $this->warn("No hospital found with ID: " . $hospitalId);
        }
        
        // Check using the helper function
        $hospitalFromHelper = hospital();
        if ($hospitalFromHelper) {
            $this->info("Hospital from helper: " . $hospitalFromHelper->name);
        } else {
            $this->warn("No hospital from helper");
        }
    }
}
