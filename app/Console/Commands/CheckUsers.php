<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the users in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = DB::table('users')->get();
        $this->info("There are " . $users->count() . " users in the database.");
        
        foreach ($users as $user) {
            $this->line("ID: {$user->id}, Name: {$user->name}, Email: {$user->email}");
        }
        
        if ($users->count() == 0) {
            $this->warn('No users found in the database. You may need to run the seeders.');
        }
    }
}
