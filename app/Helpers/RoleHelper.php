<?php

namespace App\Helpers;

use App\Models\User;

/**
 * Helper class for role-related operations
 */
class RoleHelper
{
    /**
     * Get all available roles
     *
     * @return array
     */
    public static function getAllRoles(): array
    {
        return [
            User::ROLE_SUPERADMIN => 'Super Admin',
            User::ROLE_ADMIN => 'Admin',
            User::ROLE_MUTU => 'Mutu',
            User::ROLE_KLAIM => 'Klaim',
            User::ROLE_MANAJEMEN => 'Manajemen',
            User::ROLE_OBSERVER => 'Observer (Read-only)',
        ];
    }

    /**
     * Get roles that can be assigned by admin (excluding superadmin)
     *
     * @return array
     */
    public static function getAssignableRoles(): array
    {
        return [
            User::ROLE_ADMIN => 'Admin',
            User::ROLE_MUTU => 'Mutu',
            User::ROLE_KLAIM => 'Klaim',
            User::ROLE_MANAJEMEN => 'Manajemen',
            User::ROLE_OBSERVER => 'Observer (Read-only)',
        ];
    }

    /**
     * Get role badge color classes
     *
     * @param string $role
     * @return array ['bg' => string, 'text' => string]
     */
    public static function getRoleBadgeColors(string $role): array
    {
        return match($role) {
            User::ROLE_SUPERADMIN => [
                'bg' => 'bg-gray-100 dark:bg-gray-900',
                'text' => 'text-gray-800 dark:text-gray-100',
            ],
            User::ROLE_ADMIN => [
                'bg' => 'bg-red-100 dark:bg-red-900',
                'text' => 'text-red-800 dark:text-red-100',
            ],
            User::ROLE_MUTU => [
                'bg' => 'bg-blue-100 dark:bg-blue-900',
                'text' => 'text-blue-800 dark:text-blue-100',
            ],
            User::ROLE_KLAIM => [
                'bg' => 'bg-green-100 dark:bg-green-900',
                'text' => 'text-green-800 dark:text-green-100',
            ],
            User::ROLE_MANAJEMEN => [
                'bg' => 'bg-yellow-100 dark:bg-yellow-900',
                'text' => 'text-yellow-800 dark:text-yellow-100',
            ],
            User::ROLE_OBSERVER => [
                'bg' => 'bg-purple-100 dark:bg-purple-900',
                'text' => 'text-purple-800 dark:text-purple-100',
            ],
            default => [
                'bg' => 'bg-gray-100 dark:bg-gray-900',
                'text' => 'text-gray-800 dark:text-gray-100',
            ],
        };
    }

    /**
     * Check if role is read-only
     *
     * @param string $role
     * @return bool
     */
    public static function isReadOnly(string $role): bool
    {
        return $role === User::ROLE_OBSERVER;
    }

    /**
     * Get role display name
     *
     * @param string $role
     * @return string
     */
    public static function getRoleDisplayName(string $role): string
    {
        return self::getAllRoles()[$role] ?? ucfirst($role);
    }
}

