<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_cases', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id');
            $table->string('medical_record_number');
            $table->unsignedBigInteger('clinical_pathway_id');
            $table->date('admission_date');
            $table->date('discharge_date')->nullable();
            $table->string('primary_diagnosis');
            $table->string('ina_cbg_code');
            $table->decimal('actual_total_cost', 15, 2);
            $table->decimal('ina_cbg_tariff', 15, 2);
            $table->decimal('compliance_percentage', 5, 2);
            $table->decimal('cost_variance', 15, 2);
            $table->unsignedBigInteger('input_by');
            $table->datetime('input_date');
            $table->timestamps();
            
            $table->foreign('clinical_pathway_id')->references('id')->on('clinical_pathways')->onDelete('cascade');
            $table->foreign('input_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('clinical_pathway_id');
            $table->index('input_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_cases');
    }
}
