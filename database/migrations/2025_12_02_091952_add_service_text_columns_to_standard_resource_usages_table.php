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
        Schema::table('standard_resource_usages', function (Blueprint $table) {
            // Tambahkan kolom untuk menyimpan informasi service secara tekstual
            // sehingga bisa menyimpan service manual (tidak harus dari cost_references)
            $table->string('service_code', 100)->nullable()->after('service_id');
            $table->string('service_name', 255)->nullable()->after('service_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standard_resource_usages', function (Blueprint $table) {
            $table->dropColumn(['service_name', 'service_code']);
        });
    }
};
