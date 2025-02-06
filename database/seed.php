<?php
require_once __DIR__ . '/../Acces_BD/connexion.php';
require_once __DIR__ . '/seeders/CategorySeeder.php';
require_once __DIR__ . '/seeders/ProductSeeder.php';
require_once __DIR__ . '/seeders/UserSeeder.php';

try {
    $conn = Connect();
    
    echo "Starting database seeding...\n";
    
    // Initialize database with schema
    initializeDatabase();
    
    // Disable foreign key checks before seeding
    $conn->exec("SET FOREIGN_KEY_CHECKS=0");
    
    try {
        // Clear all tables in the correct order (child tables first)
        $tables = [
            'avis',
            'commande_items',
            'commandes',
            'produits',
            'categories',
            'users'
        ];
        
        foreach ($tables as $table) {
            $conn->exec("TRUNCATE TABLE `$table`");
        }
        
        // Seed in correct order (parent tables first)
        echo "Seeding users...\n";
        seedUsers();
        
        echo "Seeding categories...\n";
        seedCategories();
        
        echo "Seeding products...\n";
        seedProducts();
        
        echo "Database seeding completed successfully!\n";
    } finally {
        // Always re-enable foreign key checks
        $conn->exec("SET FOREIGN_KEY_CHECKS=1");
    }
} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
    exit(1);
}
