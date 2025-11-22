<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Hospital;

class CheckUserHospitals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-hospitals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user-hospital associations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        foreach ($users as $user) {
            $hospital = $user->hospital_id ? Hospital::find($user->hospital_id) : null;
            $hospitalInfo = $hospital ? "{$hospital->id} ({$hospital->name})" : "None";
            
            $this->line("User ID: {$user->id}, Name: {$user->name}, Role: {$user->role}, Hospital: {$hospitalInfo}");
        }
        
        $this->info("Total users: " . $users->count());
    }
}
