<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            // Use decimal to support fractional quantities if needed (e.g., 0.5 vial)
            $table->decimal('quantity', 12, 3)->default(1.000)->after('estimated_cost');
        });

        // Backfill existing rows
        DB::table('pathway_steps')->whereNull('quantity')->update(['quantity' => 1.000]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
