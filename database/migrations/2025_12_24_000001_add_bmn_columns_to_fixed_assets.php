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
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Kolom BMN Prioritas Tinggi - Identifikasi
            $table->string('jenis_bmn', 50)->nullable()->after('name');
            $table->string('kode_satker', 30)->nullable()->after('jenis_bmn');
            $table->string('nama_satker')->nullable()->after('kode_satker');
            $table->integer('nup')->nullable()->after('nama_satker');
            $table->string('kode_register', 50)->nullable()->after('nup');

            // Kolom Nilai Tambahan
            $table->decimal('nilai_mutasi', 18, 2)->nullable()->after('salvage_value');
            $table->decimal('nilai_penyusutan', 18, 2)->nullable()->after('nilai_mutasi');
            $table->decimal('nilai_buku', 18, 2)->nullable()->after('nilai_penyusutan');

            // Kolom Tanggal Tambahan
            $table->date('tanggal_buku_pertama')->nullable()->after('acquisition_date');
            $table->date('tanggal_penghapusan')->nullable()->after('disposal_date');

            // Kolom Dokumen
            $table->string('jenis_dokumen')->nullable()->after('serial_number');
            $table->string('no_dokumen')->nullable()->after('jenis_dokumen');
            $table->string('no_bpkb')->nullable()->after('no_dokumen');
            $table->string('no_polisi', 20)->nullable()->after('no_bpkb');
            $table->string('no_sertifikat')->nullable()->after('no_polisi');
            $table->string('jenis_sertifikat', 50)->nullable()->after('no_sertifikat');
            $table->string('status_sertifikasi', 50)->nullable()->after('jenis_sertifikat');

            // Kolom Tanah/Bangunan
            $table->decimal('luas_tanah_seluruhnya', 15, 2)->nullable()->after('status_sertifikasi');
            $table->decimal('luas_tanah_bangunan', 15, 2)->nullable()->after('luas_tanah_seluruhnya');
            $table->decimal('luas_tanah_sarana', 15, 2)->nullable()->after('luas_tanah_bangunan');
            $table->decimal('luas_lahan_kosong', 15, 2)->nullable()->after('luas_tanah_sarana');
            $table->decimal('luas_bangunan', 15, 2)->nullable()->after('luas_lahan_kosong');
            $table->decimal('luas_tapak_bangunan', 15, 2)->nullable()->after('luas_bangunan');
            $table->decimal('luas_pemanfaatan', 15, 2)->nullable()->after('luas_tapak_bangunan');
            $table->integer('jumlah_lantai')->nullable()->after('luas_pemanfaatan');

            // Kolom Alamat Lengkap
            $table->text('alamat_lengkap')->nullable()->after('location');
            $table->string('rt_rw', 20)->nullable()->after('alamat_lengkap');
            $table->string('kelurahan')->nullable()->after('rt_rw');
            $table->string('kecamatan')->nullable()->after('kelurahan');
            $table->string('kabupaten_kota')->nullable()->after('kecamatan');
            $table->string('kode_kabupaten_kota', 10)->nullable()->after('kabupaten_kota');
            $table->string('provinsi')->nullable()->after('kode_kabupaten_kota');
            $table->string('kode_provinsi', 10)->nullable()->after('provinsi');
            $table->string('kode_pos', 10)->nullable()->after('kode_provinsi');

            // Kolom Status & Penggunaan BMN
            $table->string('status_penggunaan', 100)->nullable()->after('status');
            $table->string('no_psp')->nullable()->after('status_penggunaan');
            $table->date('tanggal_psp')->nullable()->after('no_psp');
            $table->string('sbsk', 50)->nullable()->after('tanggal_psp');
            $table->string('optimalisasi')->nullable()->after('sbsk');
            $table->string('penghuni')->nullable()->after('optimalisasi');
            $table->string('pengguna')->nullable()->after('penghuni');

            // Kolom Organisasi DJKN
            $table->string('kode_kpknl', 20)->nullable()->after('pengguna');
            $table->string('uraian_kpknl')->nullable()->after('kode_kpknl');
            $table->string('uraian_kanwil_djkn')->nullable()->after('uraian_kpknl');
            $table->string('nama_kl')->nullable()->after('uraian_kanwil_djkn');
            $table->string('nama_e1')->nullable()->after('nama_kl');
            $table->string('nama_korwil')->nullable()->after('nama_e1');
            $table->string('lokasi_ruang')->nullable()->after('nama_korwil');

            // Metadata JSON untuk data tambahan lainnya
            $table->json('bmn_metadata')->nullable()->after('lokasi_ruang');

            // Index untuk pencarian
            $table->index('jenis_bmn');
            $table->index('kode_satker');
            $table->index('nup');
            $table->index('status_penggunaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixed_assets', function (Blueprint $table) {
            // Drop indexes dulu
            $table->dropIndex(['jenis_bmn']);
            $table->dropIndex(['kode_satker']);
            $table->dropIndex(['nup']);
            $table->dropIndex(['status_penggunaan']);

            // Drop kolom
            $table->dropColumn([
                'jenis_bmn', 'kode_satker', 'nama_satker', 'nup', 'kode_register',
                'nilai_mutasi', 'nilai_penyusutan', 'nilai_buku',
                'tanggal_buku_pertama', 'tanggal_penghapusan',
                'jenis_dokumen', 'no_dokumen', 'no_bpkb', 'no_polisi', 'no_sertifikat',
                'jenis_sertifikat', 'status_sertifikasi',
                'luas_tanah_seluruhnya', 'luas_tanah_bangunan', 'luas_tanah_sarana',
                'luas_lahan_kosong', 'luas_bangunan', 'luas_tapak_bangunan',
                'luas_pemanfaatan', 'jumlah_lantai',
                'alamat_lengkap', 'rt_rw', 'kelurahan', 'kecamatan',
                'kabupaten_kota', 'kode_kabupaten_kota', 'provinsi', 'kode_provinsi', 'kode_pos',
                'status_penggunaan', 'no_psp', 'tanggal_psp', 'sbsk', 'optimalisasi',
                'penghuni', 'pengguna',
                'kode_kpknl', 'uraian_kpknl', 'uraian_kanwil_djkn',
                'nama_kl', 'nama_e1', 'nama_korwil', 'lokasi_ruang',
                'bmn_metadata',
            ]);
        });
    }
};
