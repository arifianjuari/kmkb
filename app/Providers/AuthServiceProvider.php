<?php

namespace App\Providers;

use App\Models\Reference;
use App\Policies\ReferencePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Reference::class => ReferencePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate untuk melihat status costing (hanya admin dan costing team)
        Gate::define('viewCostingStatus', function ($user) {
            return $user->isSuperadmin() || 
                   in_array($user->role, ['admin', 'mutu'], true);
        });
    }
}
