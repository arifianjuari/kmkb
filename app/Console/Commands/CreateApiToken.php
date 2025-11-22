<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:token:create {email} {name=token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an API token for a user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $token = $user->createToken($name);
        
        $this->info("API Token created successfully:");
        $this->line($token->plainTextToken);
        
        return 0;
    }
}
