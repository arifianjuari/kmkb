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
            $table->unsignedBigInteger('hospital_id')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_references', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });
    }
};
