<?php
require_once __DIR__ . '/../Acces_BD/connexion.php';
require_once __DIR__ . '/seeders/UserSeeder.php';
require_once __DIR__ . '/seeders/CategorySeeder.php';
require_once __DIR__ . '/seeders/ProductSeeder.php';

try {
    echo "Starting database seeding...\n";
    
    // Force database initialization
    $conn = Connect();
    initializeDatabase();
    
    // Verify tables exist
    $tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table';")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('users', $tables)) {
        throw new Exception("Database tables not created properly. Check schema.sql file.");
    }
    
    // Run seeders
    echo "Seeding users...\n";
    seedUsers();
    
    echo "Seeding categories...\n";
    seedCategories();
    
    echo "Seeding products...\n";
    seedProducts();
    
    echo "Seeding completed successfully!\n";
} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
    exit(1);
}
