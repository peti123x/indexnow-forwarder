<?php
require(__DIR__.'/../vendor/autoload.php');
require(__DIR__.'/../classes/Database.php');
// database/migrate.php

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $pdo = new Database();

    // Check if table exists, if not, create it
    $tableCheck = count($pdo->select("SHOW TABLES LIKE 'urls'"));
    if ($tableCheck === 0) {
        // Table does not exist, so we create it
        $sql = "CREATE TABLE urls (
            id INT AUTO_INCREMENT PRIMARY KEY,
            url VARCHAR(500) NOT NULL,
            sent TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        );";
        $pdo->execute($sql);
        echo "Table 'urls' created successfully.";
    } else {
        echo "Table 'urls' already exists.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}