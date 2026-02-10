<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove unique constraint on asset_code to allow multiple BMN items with same code
     * but different NUP (Nomor Urut Pendaftaran).
     */
    public function up(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Must drop foreign key first because MySQL uses the unique index to enforce it
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('fixed_assets', function (Blueprint $table) {
            // Now we can safely drop the unique constraint
            $table->dropUnique(['hospital_id', 'asset_code']);
            
            // Add a regular index instead for better query performance
            $table->index(['hospital_id', 'asset_code']);
        });

        Schema::table('fixed_assets', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Must drop foreign key first
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('fixed_assets', function (Blueprint $table) {
            $table->dropIndex(['hospital_id', 'asset_code']);
            $table->unique(['hospital_id', 'asset_code']);
        });

        Schema::table('fixed_assets', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });
    }
};
