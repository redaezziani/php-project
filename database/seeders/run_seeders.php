<?php

require_once 'CategorySeeder.php';
require_once 'ProductSeeder.php';
require_once 'UserSeeder.php';

try {
    // Run seeders in order (due to foreign key constraints)
    echo "Starting to seed the database...\n";
    
    echo "Seeding categories...\n";
    seedCategories();
    echo "Categories seeded successfully!\n";
    
    echo "Seeding products...\n";
    seedProducts();
    echo "Products seeded successfully!\n";
    
    echo "Seeding users...\n";
    seedUsers();
    echo "Users seeded successfully!\n";
    
    echo "All seeds completed successfully!\n";
} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
}
