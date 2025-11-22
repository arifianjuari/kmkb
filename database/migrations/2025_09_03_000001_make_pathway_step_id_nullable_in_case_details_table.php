<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePathwayStepIdNullableInCaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('case_details', function (Blueprint $table) {
            $table->unsignedBigInteger('pathway_step_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('case_details', function (Blueprint $table) {
            $table->unsignedBigInteger('pathway_step_id')->nullable(false)->change();
        });
    }
}
