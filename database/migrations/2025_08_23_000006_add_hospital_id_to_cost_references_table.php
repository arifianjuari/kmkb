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
            $table->unsignedBigInteger('hospital_id')->nullable()->default(null)->after('id');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->index('hospital_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_references', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropIndex(['hospital_id']);
            $table->dropColumn('hospital_id');
        });
    }
};
