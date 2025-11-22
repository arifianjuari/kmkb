<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            // Rename 'action' to 'service_code'
            if (Schema::hasColumn('pathway_steps', 'action')) {
                $table->renameColumn('action', 'service_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            if (Schema::hasColumn('pathway_steps', 'service_code')) {
                $table->renameColumn('service_code', 'action');
            }
        });
    }
};
