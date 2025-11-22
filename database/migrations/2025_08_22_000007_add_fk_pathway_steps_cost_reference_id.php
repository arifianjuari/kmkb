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
            // Ensure column exists and is nullable as per model/migration
            if (!Schema::hasColumn('pathway_steps', 'cost_reference_id')) {
                $table->unsignedBigInteger('cost_reference_id')->nullable()->after('estimated_cost');
            }
            // Add FK to cost_references
            $table->foreign('cost_reference_id', 'pathway_steps_cost_reference_id_foreign')
                  ->references('id')
                  ->on('cost_references')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            // Drop FK if exists, then (optionally) keep column
            try {
                $table->dropForeign('pathway_steps_cost_reference_id_foreign');
            } catch (\Throwable $e) {
                // ignore if not exists
            }
        });
    }
};
