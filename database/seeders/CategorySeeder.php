<?php
require_once __DIR__ . '/../../Acces_BD/connexion.php';

function seedCategories() {
    $conn = Connect();
    
    try {
        // Disable foreign key checks
        $conn->exec("SET FOREIGN_KEY_CHECKS=0");
        
        // Clear existing categories
        $conn->exec("TRUNCATE TABLE categories");
        
        $categories = [
            [
                'nom' => 'خضروات',
                'description' => 'خضروات طازجة وعضوية',
                'image' => 'vegetables.jpg'
            ],
            [
                'nom' => 'فواكه',
                'description' => 'فواكه طازجة وموسمية',
                'image' => 'fruits.jpg'
            ]
        ];

        $stmt = $conn->prepare("
            INSERT INTO categories (nom, description, image)
            VALUES (:nom, :description, :image)
        ");

        foreach ($categories as $category) {
            $stmt->execute($category);
        }

        // Re-enable foreign key checks
        $conn->exec("SET FOREIGN_KEY_CHECKS=1");
        
        echo "Categories seeded successfully!\n";
    } catch (PDOException $e) {
        // Make sure to re-enable foreign key checks even if there's an error
        $conn->exec("SET FOREIGN_KEY_CHECKS=1");
        throw new Exception("Error seeding categories: " . $e->getMessage());
    }
}
