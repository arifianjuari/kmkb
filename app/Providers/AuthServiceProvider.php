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
        // Keep for backward compatibility
        Gate::define('viewCostingStatus', function ($user) {
            return $user->isSuperadmin() || 
                   in_array($user->role, ['admin', 'mutu', 'hospital_admin', 'pathway_team'], true);
        });

        // Auto-generate Gates from config/permissions.php
        $this->registerPermissionGates();
    }

    /**
     * Auto-generate Gate definitions from permissions config
     */
    protected function registerPermissionGates(): void
    {
        $allPermissions = [];

        // Collect permissions from menus
        foreach (config('permissions.menus', []) as $menu) {
            if (isset($menu['permission'])) {
                $allPermissions[] = $menu['permission'];
            }
            if (isset($menu['submenus'])) {
                foreach ($menu['submenus'] as $submenu) {
                    if (isset($submenu['permission'])) {
                        $allPermissions[] = $submenu['permission'];
                    }
                }
            }
        }

        // Collect permissions from roles
        foreach (config('permissions.roles', []) as $role => $config) {
            $permissions = $config['permissions'] ?? [];
            $allPermissions = array_merge($allPermissions, $permissions);
        }

        // Remove wildcard and duplicates
        $allPermissions = array_filter(
            array_unique($allPermissions),
            fn($p) => $p !== '*'
        );

        // Define gates
        foreach ($allPermissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                // Superadmin has all permissions
                if ($user->role === 'superadmin') {
                    return true;
                }

                // Check role permission from config
                $roleConfig = config("permissions.roles.{$user->role}");
                if (!$roleConfig) {
                    return false;
                }

                $rolePermissions = $roleConfig['permissions'] ?? [];
                return in_array('*', $rolePermissions) || in_array($permission, $rolePermissions);
            });
        }
    }
}
