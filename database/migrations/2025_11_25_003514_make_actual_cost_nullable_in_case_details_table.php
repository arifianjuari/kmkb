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
        Schema::table('case_details', function (Blueprint $table) {
            $table->decimal('actual_cost', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_details', function (Blueprint $table) {
            // Backfill NULLs to 0.00 to safely revert to NOT NULL
            \Illuminate\Support\Facades\DB::table('case_details')
                ->whereNull('actual_cost')
                ->update(['actual_cost' => 0.00]);
            
            $table->decimal('actual_cost', 15, 2)->nullable(false)->change();
        });
    }
};
