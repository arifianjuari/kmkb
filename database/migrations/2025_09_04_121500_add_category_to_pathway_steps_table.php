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
        Schema::table('pathway_steps', function (Blueprint $table) {
            // Add category to group steps within a clinical pathway
            $table->enum('category', [
                'Administrasi',
                'Penilaian dan Pemantauan Medis',
                'Penilaian dan Pemantauan Keperawatan',
                'Pemeriksaan Penunjang Medik',
                'Tindakan Medis',
                'Tindakan Keperawatan',
                'Medikasi',
                'BHP',
                'Nutrisi',
                'Kegiatan',
                'Konsultasi dan Komunikasi Tim',
                'Konseling Psikososial',
                'Pendidikan dan Komunikasi dengan Pasien/Keluarga',
                'Kriteria KRS',
            ])->nullable()->after('display_order');

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathway_steps', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};
