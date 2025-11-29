<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class ConfigWriter
{
    /**
     * Write permissions config to file
     */
    public static function writePermissionsConfig(array $config): bool
    {
        $path = config_path('permissions.php');
        
        // Backup existing file
        if (File::exists($path)) {
            $backupPath = $path . '.backup.' . date('Y-m-d_H-i-s');
            File::copy($path, $backupPath);
        }
        
        // Generate content
        $content = self::generateConfigContent($config);
        
        // Write to file
        return File::put($path, $content) !== false;
    }
    
    /**
     * Generate PHP config file content
     */
    protected static function generateConfigContent(array $config): string
    {
        $content = "<?php\n\n";
        $content .= "return [\n";
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | Menu Structure\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | Define all menus and submenus with their routes and required permissions.\n";
        $content .= "    | Each menu can have submenus that also require specific permissions.\n";
        $content .= "    |\n";
        $content .= "    */\n\n";
        
        $content .= "    'menus' => [\n";
        $content .= self::formatMenus($config['menus'] ?? [], 2);
        $content .= "    ],\n\n";
        
        $content .= "    /*\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    | Role Permissions\n";
        $content .= "    |--------------------------------------------------------------------------\n";
        $content .= "    |\n";
        $content .= "    | Define permissions for each role. Use '*' for all menus/permissions.\n";
        $content .= "    | Each role should have:\n";
        $content .= "    | - 'menus': array of menu keys the role can access\n";
        $content .= "    | - 'permissions': array of permission names for CRUD operations\n";
        $content .= "    |\n";
        $content .= "    */\n\n";
        
        $content .= "    'roles' => [\n";
        $content .= self::formatRoles($config['roles'] ?? [], 2);
        $content .= "    ],\n";
        $content .= "];\n";
        
        return $content;
    }
    
    /**
     * Format menus array to PHP code
     */
    protected static function formatMenus(array $menus, int $indent = 0): string
    {
        $spaces = str_repeat('    ', $indent);
        $output = '';
        
        foreach ($menus as $key => $menu) {
            $output .= $spaces . "        '" . addslashes($key) . "' => [\n";
            
            if (isset($menu['route'])) {
                $output .= $spaces . "            'route' => '" . addslashes($menu['route']) . "',\n";
            }
            
            if (isset($menu['permission'])) {
                $output .= $spaces . "            'permission' => '" . addslashes($menu['permission']) . "',\n";
            }
            
            if (isset($menu['submenus']) && is_array($menu['submenus'])) {
                $output .= $spaces . "            'submenus' => [\n";
                $output .= self::formatSubmenus($menu['submenus'], $indent + 1);
                $output .= $spaces . "            ],\n";
            }
            
            $output .= $spaces . "        ],\n\n";
        }
        
        return $output;
    }
    
    /**
     * Format submenus array to PHP code
     */
    protected static function formatSubmenus(array $submenus, int $indent = 0): string
    {
        $spaces = str_repeat('    ', $indent);
        $output = '';
        
        foreach ($submenus as $key => $submenu) {
            $output .= $spaces . "                '" . addslashes($key) . "' => [\n";
            
            if (isset($submenu['route'])) {
                $output .= $spaces . "                    'route' => '" . addslashes($submenu['route']) . "',\n";
            }
            
            if (isset($submenu['permission'])) {
                $output .= $spaces . "                    'permission' => '" . addslashes($submenu['permission']) . "',\n";
            }
            
            $output .= $spaces . "                ],\n";
        }
        
        return $output;
    }
    
    /**
     * Format roles array to PHP code
     */
    protected static function formatRoles(array $roles, int $indent = 0): string
    {
        $spaces = str_repeat('    ', $indent);
        $output = '';
        
        foreach ($roles as $roleName => $roleConfig) {
            $output .= $spaces . "        '" . addslashes($roleName) . "' => [\n";
            
            // Format menus
            if (isset($roleConfig['menus'])) {
                $output .= $spaces . "            'menus' => [\n";
                if (is_array($roleConfig['menus'])) {
                    foreach ($roleConfig['menus'] as $menu) {
                        if ($menu === '*') {
                            $output .= $spaces . "                '*',\n";
                        } else {
                            $output .= $spaces . "                '" . addslashes($menu) . "',\n";
                        }
                    }
                }
                $output .= $spaces . "            ],\n";
            }
            
            // Format permissions
            if (isset($roleConfig['permissions'])) {
                $output .= $spaces . "            'permissions' => [\n";
                if (is_array($roleConfig['permissions'])) {
                    // Check if it's wildcard
                    if (in_array('*', $roleConfig['permissions'])) {
                        $output .= $spaces . "                '*',\n";
                    } else {
                        // Group permissions by resource for better readability
                        $grouped = self::groupPermissions($roleConfig['permissions']);
                        foreach ($grouped as $group => $perms) {
                            if ($group !== 'other' && count($perms) > 0) {
                                $output .= $spaces . "                // " . ucfirst(str_replace('-', ' ', $group)) . "\n";
                            }
                            foreach ($perms as $perm) {
                                $output .= $spaces . "                '" . addslashes($perm) . "',\n";
                            }
                        }
                    }
                }
                $output .= $spaces . "            ],\n";
            }
            
            $output .= $spaces . "        ],\n\n";
        }
        
        return $output;
    }
    
    /**
     * Group permissions by resource for better formatting
     */
    protected static function groupPermissions(array $permissions): array
    {
        $grouped = [];
        $other = [];
        
        foreach ($permissions as $permission) {
            if ($permission === '*') {
                continue; // Skip wildcard, handled separately
            }
            
            // Extract resource from permission (e.g., 'view-dashboard' -> 'dashboard')
            $parts = explode('-', $permission, 2);
            if (count($parts) === 2) {
                $resource = $parts[1];
                if (!isset($grouped[$resource])) {
                    $grouped[$resource] = [];
                }
                $grouped[$resource][] = $permission;
            } else {
                $other[] = $permission;
            }
        }
        
        // Sort resources alphabetically
        ksort($grouped);
        
        // Add other permissions at the end
        if (!empty($other)) {
            $grouped['other'] = $other;
        }
        
        return $grouped;
    }
}

