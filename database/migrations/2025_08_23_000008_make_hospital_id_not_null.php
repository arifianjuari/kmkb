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
        // Drop all foreign key constraints first
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('patient_cases', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('case_details', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('cost_references', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        // Make hospital_id NOT NULL for all tables except users table which can have null for superadmin
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable(false)->change();
        });

        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable(false)->change();
        });

        Schema::table('patient_cases', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable(false)->change();
        });

        Schema::table('case_details', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable(false)->change();
        });

        Schema::table('cost_references', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable(false)->change();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable(false)->change();
        });

        // Recreate all foreign key constraints
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });

        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });

        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });

        Schema::table('patient_cases', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });

        Schema::table('case_details', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });

        Schema::table('cost_references', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all foreign key constraints first
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('patient_cases', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('case_details', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('cost_references', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
        });

        // Revert hospital_id to nullable for all tables
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        Schema::table('patient_cases', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        Schema::table('case_details', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        Schema::table('cost_references', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('hospital_id')->nullable()->change();
        });

        // Recreate all foreign key constraints with set null
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('set null');
        });

        Schema::table('clinical_pathways', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('set null');
        });

        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('set null');
        });

        Schema::table('patient_cases', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('set null');
        });

        Schema::table('case_details', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('set null');
        });

        Schema::table('cost_references', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('set null');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('set null');
        });
    }
};
