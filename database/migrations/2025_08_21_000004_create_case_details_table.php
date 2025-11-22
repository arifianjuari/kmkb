<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_case_id');
            $table->unsignedBigInteger('pathway_step_id');
            $table->string('service_item');
            $table->string('service_code');
            $table->string('status')->default('completed');
            $table->integer('quantity');
            $table->decimal('actual_cost', 15, 2);
            $table->date('service_date');
            $table->timestamps();
            
            $table->foreign('patient_case_id')->references('id')->on('patient_cases')->onDelete('cascade');
            $table->foreign('pathway_step_id')->references('id')->on('pathway_steps')->onDelete('cascade');
            $table->index('patient_case_id');
            $table->index('pathway_step_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('case_details');
    }
}
