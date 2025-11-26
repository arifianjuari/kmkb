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
        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->string('unit_cost_version', 100)->nullable()->after('status');
            $table->index('unit_cost_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->dropIndex(['unit_cost_version']);
            $table->dropColumn('unit_cost_version');
        });
    }
};
