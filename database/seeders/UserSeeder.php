<?php
require_once __DIR__ . '/../../Acces_BD/connexion.php';

function seedUsers() {
    $conn = Connect();
    
    try {
        // Disable foreign key checks
        $conn->exec("SET FOREIGN_KEY_CHECKS=0");
        
        // Clear existing users
        $conn->exec("TRUNCATE TABLE users");
        
        $users = [
            [
                'nom' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ],
            [
                'nom' => 'Test User',
                'email' => 'user@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'client'
            ]
        ];

        $stmt = $conn->prepare("
            INSERT INTO users (nom, email, password, role)
            VALUES (:nom, :email, :password, :role)
        ");

        foreach ($users as $user) {
            $stmt->execute($user);
        }

        // Re-enable foreign key checks
        $conn->exec("SET FOREIGN_KEY_CHECKS=1");
        
        echo "Users seeded successfully!\n";
    } catch (PDOException $e) {
        // Make sure to re-enable foreign key checks even if there's an error
        $conn->exec("SET FOREIGN_KEY_CHECKS=1");
        throw new Exception("Error seeding users: " . $e->getMessage());
    }
}
