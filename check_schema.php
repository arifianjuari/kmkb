<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'kmkb_db',
    'username' => 'root',
    'password' => 'root',
    'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    $columns = Capsule::select("SHOW COLUMNS FROM cost_references");
    echo "Columns in cost_references table:\n";
    foreach ($columns as $column) {
        echo "- " . $column->Field . " (" . $column->Type . ")" . ($column->Null === 'YES' ? ' NULL' : ' NOT NULL') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
