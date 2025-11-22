<?php
require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test database connection
try {
    // Check if we're using socket or host/port
    if (!empty($_ENV['DB_SOCKET'])) {
        $pdo = new PDO(
            "mysql:unix_socket=" . $_ENV['DB_SOCKET'] . ";dbname=" . $_ENV['DB_DATABASE'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD']
        );
    } else {
        $pdo = new PDO(
            "mysql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_DATABASE'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD']
        );
    }
    
    echo "Database connection successful!\n";
    
    // Check users and their hospital associations
    $stmt = $pdo->query("SELECT id, name, email, hospital_id, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users in the database:\n";
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . ", Name: " . $user['name'] . ", Email: " . $user['email'] . ", Hospital ID: " . $user['hospital_id'] . ", Role: " . $user['role'] . "\n";
    }
    
    // Check hospitals
    $stmt = $pdo->query("SELECT id, name, code FROM hospitals");
    $hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nHospitals in the database:\n";
    foreach ($hospitals as $hospital) {
        echo "ID: " . $hospital['id'] . ", Name: " . $hospital['name'] . ", Code: " . $hospital['code'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
