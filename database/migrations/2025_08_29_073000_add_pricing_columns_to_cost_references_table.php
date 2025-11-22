<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingColumnsToCostReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_references', function (Blueprint $table) {
            $table->decimal('purchase_price', 15, 2)->nullable()->after('standard_cost');
            $table->decimal('selling_price_unit', 15, 2)->nullable()->after('purchase_price');
            $table->decimal('selling_price_total', 15, 2)->nullable()->after('selling_price_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cost_references', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'selling_price_unit', 'selling_price_total']);
        });
    }
}
