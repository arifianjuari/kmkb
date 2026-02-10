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
        Schema::table('asset_depreciations', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('gl_expense_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_depreciations', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
