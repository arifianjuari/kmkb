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
        Schema::table('gl_expenses', function (Blueprint $table) {
            // Line number for distinguishing multiple entries with same combination
            $table->unsignedInteger('line_number')->default(1)->after('expense_category_id');
            
            // Transaction date for individual transaction tracking (optional)
            $table->date('transaction_date')->nullable()->after('amount');
            
            // Reference/invoice number (optional)
            $table->string('reference_number', 50)->nullable()->after('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gl_expenses', function (Blueprint $table) {
            $table->dropColumn(['line_number', 'transaction_date', 'reference_number']);
        });
    }
};
