<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel's configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test SIMRS database connection
    DB::connection('simrs')->select('SELECT 1');
    echo "Successfully connected to SIMRS database\n";
    
    // Try to fetch some data from a table that should exist
    $result = DB::connection('simrs')->select('SELECT COUNT(*) as count FROM jns_perawatan LIMIT 1');
    echo "Successfully queried jns_perawatan table. Row count: " . $result[0]->count . "\n";
    
} catch (Exception $e) {
    echo "Error connecting to SIMRS database: " . $e->getMessage() . "\n";
}
