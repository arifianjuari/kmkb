<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePathwayStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pathway_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinical_pathway_id');
            $table->integer('step_order');
            $table->text('description');
            $table->text('action')->nullable();
            $table->text('criteria')->nullable();
            $table->integer('estimated_duration')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->unsignedBigInteger('cost_reference_id')->nullable();
            $table->timestamps();
            
            $table->foreign('clinical_pathway_id')->references('id')->on('clinical_pathways')->onDelete('cascade');
            $table->index('clinical_pathway_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pathway_steps');
    }
}
