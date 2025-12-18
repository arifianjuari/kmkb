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
            $table->string('kd_poli', 10)->nullable()->after('category');
            $table->index('kd_poli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_volumes', function (Blueprint $table) {
            $table->dropIndex(['kd_poli']);
            $table->dropColumn('kd_poli');
        });
    }
};
