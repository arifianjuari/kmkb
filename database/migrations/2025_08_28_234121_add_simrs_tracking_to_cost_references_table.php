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
        Schema::table('cost_references', function (Blueprint $table) {
            $table->string('simrs_kode_brng')->nullable()->after('service_code');
            $table->boolean('is_synced_from_simrs')->default(false)->after('source');
            $table->timestamp('last_synced_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_references', function (Blueprint $table) {
            $table->dropColumn(['simrs_kode_brng', 'is_synced_from_simrs', 'last_synced_at']);
        });
    }
};
