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
        Schema::create('reference_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reference_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->foreign('reference_id')->references('id')->on('references')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            $table->unique(['reference_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_tag');
    }
};
