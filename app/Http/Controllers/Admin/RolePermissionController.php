<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ConfigWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Hanya superadmin yang bisa akses
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperadmin()) {
                abort(403, 'Hanya Superadmin yang dapat mengakses halaman ini.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of all roles
     */
    public function index()
    {
        $config = config('permissions');
        $roles = $config['roles'] ?? [];
        $menus = $config['menus'] ?? [];
        
        // Get role display names
        $roleDisplayNames = [
            'superadmin' => 'Super Admin',
            'hospital_admin' => 'Hospital Admin',
            'finance_costing' => 'Finance Costing',
            'hr_payroll' => 'HR Payroll',
            'facility_asset' => 'Facility Asset',
            'simrs_integration' => 'SIMRS Integration',
            'support_unit' => 'Support Unit',
            'clinical_unit' => 'Clinical Unit',
            'medrec_claims' => 'Medrec Claims',
            'pathway_team' => 'Pathway Team',
            'management_auditor' => 'Management Auditor',
        ];
        
        return view('admin.roles.index', compact('roles', 'menus', 'roleDisplayNames'));
    }

    /**
     * Show the form for editing a role
     */
    public function edit($roleName)
    {
        $config = config('permissions');
        $role = $config['roles'][$roleName] ?? null;
        $menus = $config['menus'] ?? [];
        
        if (!$role) {
            abort(404, 'Role tidak ditemukan.');
        }
        
        // Get all available permissions
        $allPermissions = $this->getAllPermissions($menus);
        
        // Get role display name
        $roleDisplayNames = [
            'superadmin' => 'Super Admin',
            'hospital_admin' => 'Hospital Admin',
            'finance_costing' => 'Finance Costing',
            'hr_payroll' => 'HR Payroll',
            'facility_asset' => 'Facility Asset',
            'simrs_integration' => 'SIMRS Integration',
            'support_unit' => 'Support Unit',
            'clinical_unit' => 'Clinical Unit',
            'medrec_claims' => 'Medrec Claims',
            'pathway_team' => 'Pathway Team',
            'management_auditor' => 'Management Auditor',
        ];
        
        $roleDisplayName = $roleDisplayNames[$roleName] ?? ucfirst(str_replace('_', ' ', $roleName));
        
        return view('admin.roles.edit', compact('roleName', 'role', 'menus', 'allPermissions', 'roleDisplayName'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, $roleName)
    {
        $config = config('permissions');
        
        if (!isset($config['roles'][$roleName])) {
            return back()->with('error', 'Role tidak ditemukan.');
        }
        
        // Validate input
        $request->validate([
            'menus' => 'nullable|array',
            'menus.*' => 'string|in:' . implode(',', array_keys($config['menus'] ?? [])),
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);
        
        // Prevent editing superadmin role
        if ($roleName === 'superadmin') {
            return back()->with('error', 'Role Superadmin tidak dapat diubah.');
        }
        
        // Update role configuration
        $config['roles'][$roleName]['menus'] = $request->menus ?? [];
        $config['roles'][$roleName]['permissions'] = $request->permissions ?? [];
        
        // Write to config file
        try {
            ConfigWriter::writePermissionsConfig($config);
            
            // Clear config cache
            Artisan::call('config:clear');
            Cache::forget('roles_permissions');
            
            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$roleName}' berhasil diupdate. Config file telah disimpan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan config file: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get all available permissions from menus and common CRUD operations
     */
    protected function getAllPermissions(array $menus): array
    {
        $permissions = [];
        
        // Collect permissions from menus
        foreach ($menus as $menu) {
            if (isset($menu['permission'])) {
                $permissions[] = $menu['permission'];
            }
            if (isset($menu['submenus'])) {
                foreach ($menu['submenus'] as $submenu) {
                    if (isset($submenu['permission'])) {
                        $permissions[] = $submenu['permission'];
                    }
                }
            }
        }
        
        // Add common CRUD permissions for resources
        $resources = [
            'dashboard',
            'setup',
            'cost-centers',
            'expense-categories',
            'allocation-drivers',
            'tariff-classes',
            'cost-references',
            'service-catalog',
            'jkn-cbg-codes',
            'simrs-integration',
            'data-input',
            'gl-expenses',
            'driver-statistics',
            'service-volumes',
            'import-center',
            'costing-process',
            'pre-allocation-check',
            'allocation',
            'unit-cost',
            'tariffs',
            'tariff-simulation',
            'tariff-structure',
            'final-tariffs',
            'tariff-explorer',
            'tariff-comparison',
            'pathways',
            'cases',
            'case-details',
            'analytics',
            'cost-center-performance',
            'allocation-summary',
            'unit-cost-summary',
            'tariff-analytics',
            'pathway-compliance',
            'case-variance',
            'los-analysis',
            'continuous-improvement',
            'simrs',
            'service-volume-current',
            'references',
            'users',
            'audit-logs',
        ];
        
        $actions = ['view', 'create', 'update', 'delete', 'export', 'import', 'approve', 'sync'];
        
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permission = "{$action}-{$resource}";
                if (!in_array($permission, $permissions)) {
                    $permissions[] = $permission;
                }
            }
        }
        
        // Sort permissions for better UX
        sort($permissions);
        
        return array_unique($permissions);
    }
}

