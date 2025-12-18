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
        Schema::table('service_volumes', function (Blueprint $table) {
            $table->string('kd_bangsal', 10)->nullable()->after('kd_poli');
            $table->index('kd_bangsal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_volumes', function (Blueprint $table) {
            $table->dropIndex(['kd_bangsal']);
            $table->dropColumn('kd_bangsal');
        });
    }
};
