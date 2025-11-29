<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Gate;

class MenuHelper
{
    /**
     * Check if user can access a menu
     *
     * @param \App\Models\User $user
     * @param string $menuKey
     * @return bool
     */
    public static function canAccessMenu($user, $menuKey)
    {
        // Superadmin can access all menus
        if ($user->role === 'superadmin') {
            return true;
        }

        $roleConfig = config("permissions.roles.{$user->role}");
        if (!$roleConfig) {
            return false;
        }

        $allowedMenus = $roleConfig['menus'] ?? [];

        // Check if wildcard or menu in allowed list
        return in_array('*', $allowedMenus) || in_array($menuKey, $allowedMenus);
    }

    /**
     * Get all accessible menus for user
     *
     * @param \App\Models\User $user
     * @return array
     */
    public static function getAccessibleMenus($user)
    {
        if ($user->role === 'superadmin') {
            return array_keys(config('permissions.menus', []));
        }

        $roleConfig = config("permissions.roles.{$user->role}");
        if (!$roleConfig) {
            return [];
        }

        $allowedMenus = $roleConfig['menus'] ?? [];

        if (in_array('*', $allowedMenus)) {
            return array_keys(config('permissions.menus', []));
        }

        return $allowedMenus;
    }

    /**
     * Check if user can access a submenu
     *
     * @param \App\Models\User $user
     * @param string $menuKey
     * @param string $submenuKey
     * @return bool
     */
    public static function canAccessSubmenu($user, $menuKey, $submenuKey)
    {
        // Superadmin can access all submenus
        if ($user->role === 'superadmin') {
            return true;
        }

        $menu = config("permissions.menus.{$menuKey}");
        if (!$menu || !isset($menu['submenus'])) {
            return false;
        }

        $submenu = $menu['submenus'][$submenuKey] ?? null;
        if (!$submenu) {
            return false;
        }

        // Check permission for submenu
        $permission = $submenu['permission'] ?? null;
        if ($permission) {
            return Gate::allows($permission);
        }

        return false;
    }

    /**
     * Check if user has any permission for a menu
     * Useful for showing menu group even if user can't access all submenus
     *
     * @param \App\Models\User $user
     * @param string $menuKey
     * @return bool
     */
    public static function hasAnyMenuPermission($user, $menuKey)
    {
        // Superadmin has all permissions
        if ($user->role === 'superadmin') {
            return true;
        }

        // First check if user can access the menu itself
        if (self::canAccessMenu($user, $menuKey)) {
            return true;
        }

        // Check if user can access any submenu
        $menu = config("permissions.menus.{$menuKey}");
        if ($menu && isset($menu['submenus'])) {
            foreach ($menu['submenus'] as $submenuKey => $submenu) {
                if (self::canAccessSubmenu($user, $menuKey, $submenuKey)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get all submenus user can access for a given menu
     *
     * @param \App\Models\User $user
     * @param string $menuKey
     * @return array
     */
    public static function getAccessibleSubmenus($user, $menuKey)
    {
        $menu = config("permissions.menus.{$menuKey}");
        if (!$menu || !isset($menu['submenus'])) {
            return [];
        }

        $accessibleSubmenus = [];

        foreach ($menu['submenus'] as $submenuKey => $submenu) {
            if (self::canAccessSubmenu($user, $menuKey, $submenuKey)) {
                $accessibleSubmenus[$submenuKey] = $submenu;
            }
        }

        return $accessibleSubmenus;
    }
}

