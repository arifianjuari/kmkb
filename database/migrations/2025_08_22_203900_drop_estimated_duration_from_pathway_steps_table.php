<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            if (Schema::hasColumn('pathway_steps', 'estimated_duration')) {
                $table->dropColumn('estimated_duration');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            if (!Schema::hasColumn('pathway_steps', 'estimated_duration')) {
                $table->integer('estimated_duration')->nullable()->after('criteria');
            }
        });
    }
};
