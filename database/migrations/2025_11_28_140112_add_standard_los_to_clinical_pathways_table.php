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
            $table->integer('standard_los')->nullable()->after('status')->comment('Standard Length of Stay in days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->dropColumn('standard_los');
        });
    }
};
