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
        Schema::table('employees', function (Blueprint $table) {
            // Job Information
            $table->string('job_title', 100)->nullable()->after('name')
                ->comment('Jabatan: Dokter, Perawat, Kepala Ruang, dll');
            
            // Employment Type (ASN/PNS, Kontrak, Honorer, etc.)
            $table->string('employment_type', 50)->nullable()->after('job_title')
                ->comment('Tipe kepegawaian: pns, contract, honorary, outsource');
            
            // Education Level
            $table->string('education_level', 20)->nullable()->after('employment_type')
                ->comment('Tingkat pendidikan: sd, smp, sma, d1, d2, d3, d4, s1, s2, s3, specialist');
            
            // Professional Category
            $table->string('professional_category', 50)->nullable()->after('education_level')
                ->comment('Kategori profesionalisme: doctor_specialist, doctor_general, nurse, health_analyst, pharmacist, non_medical');
            
            // Salary Information
            $table->decimal('base_salary', 15, 2)->nullable()->after('professional_category')
                ->comment('Gaji pokok bulanan');
            
            $table->decimal('allowances', 15, 2)->nullable()->after('base_salary')
                ->comment('Total tunjangan, insentif, kompensasi lainnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'job_title',
                'employment_type',
                'education_level',
                'professional_category',
                'base_salary',
                'allowances',
            ]);
        });
    }
};
