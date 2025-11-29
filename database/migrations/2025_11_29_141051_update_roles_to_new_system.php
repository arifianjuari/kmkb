<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Maps existing roles to new role system:
     * - superadmin → superadmin (no change)
     * - admin → hospital_admin
     * - mutu → pathway_team
     * - klaim → medrec_claims
     * - manajemen → management_auditor
     * - observer → management_auditor
     */
    public function up(): void
    {
        // Map old roles to new roles
        $roleMapping = [
            'admin' => 'hospital_admin',
            'mutu' => 'pathway_team',
            'klaim' => 'medrec_claims',
            'manajemen' => 'management_auditor',
            'observer' => 'management_auditor',
            // superadmin stays as superadmin
        ];

        // Update users table with new role names
        foreach ($roleMapping as $oldRole => $newRole) {
            DB::table('users')
                ->where('role', $oldRole)
                ->update(['role' => $newRole]);
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Maps new roles back to old roles (approximate mapping)
     */
    public function down(): void
    {
        // Reverse mapping (approximate, some data loss possible)
        $reverseMapping = [
            'hospital_admin' => 'admin',
            'pathway_team' => 'mutu',
            'medrec_claims' => 'klaim',
            'management_auditor' => 'observer', // Default to observer for reverse
            // superadmin stays as superadmin
        ];

        // Update users table back to old role names
        foreach ($reverseMapping as $newRole => $oldRole) {
            DB::table('users')
                ->where('role', $newRole)
                ->update(['role' => $oldRole]);
        }
    }
};
