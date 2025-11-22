<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->integer('display_order')->nullable()->after('step_order');
            $table->index('display_order');
        });

        // Backfill existing rows so display_order mirrors step_order
        DB::table('pathway_steps')->update([
            'display_order' => DB::raw('step_order')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->dropIndex(['display_order']);
            $table->dropColumn('display_order');
        });
    }
};
