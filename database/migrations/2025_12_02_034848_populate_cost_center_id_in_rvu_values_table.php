<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate cost_center_id from cost_references for existing RVU values
        DB::statement('
            UPDATE rvu_values rv
            INNER JOIN cost_references cr ON rv.cost_reference_id = cr.id
            SET rv.cost_center_id = cr.cost_center_id
            WHERE rv.cost_center_id IS NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - data will remain
    }
};
