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
    
    // Test if we can query the users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Users table has " . $result['count'] . " records.\n";
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
