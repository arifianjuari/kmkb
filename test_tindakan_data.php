<?php
require_once 'vendor/autoload.php';

// Load Laravel's configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Test fetching tindakan rawat jalan data
    echo "Testing tindakan rawat jalan data...\n";
    
    // Get tindakan dokter
    $dokter = DB::connection('simrs')->select("SELECT 
        rj.tgl_perawatan,
        rj.jam_rawat AS jam,
        rj.no_rawat,
        rj.kd_jenis_prw,
        jp.nm_perawatan,
        'DR' AS pelaksana,
        jp.tarif_tindakandr AS tarif_standar_dr,
        rj.biaya_rawat AS harga_transaksi
    FROM rawat_jl_dr rj
    JOIN jns_perawatan jp ON jp.kd_jenis_prw = rj.kd_jenis_prw
    ORDER BY rj.tgl_perawatan DESC, rj.jam_rawat DESC
    LIMIT 5");
    
    echo "Fetched " . count($dokter) . " tindakan dokter records\n";
    
    if (count($dokter) > 0) {
        echo "Sample record: \n";
        print_r($dokter[0]);
    }
    
    // Get tindakan perawat
    $perawat = DB::connection('simrs')->select("SELECT 
        rj.tgl_perawatan,
        rj.jam_rawat AS jam,
        rj.no_rawat,
        rj.kd_jenis_prw,
        jp.nm_perawatan,
        'PR' AS pelaksana,
        jp.tarif_tindakanpr AS tarif_standar_pr,
        rj.biaya_rawat AS harga_transaksi
    FROM rawat_jl_pr rj
    JOIN jns_perawatan jp ON jp.kd_jenis_prw = rj.kd_jenis_prw
    ORDER BY rj.tgl_perawatan DESC, rj.jam_rawat DESC
    LIMIT 5");
    
    echo "\nFetched " . count($perawat) . " tindakan perawat records\n";
    
    if (count($perawat) > 0) {
        echo "Sample record: \n";
        print_r($perawat[0]);
    }
    
} catch (Exception $e) {
    echo "Error fetching tindakan data: " . $e->getMessage() . "\n";
}
