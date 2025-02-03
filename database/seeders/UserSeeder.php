<?php
require_once __DIR__ . '/../../Acces_BD/connexion.php';

function seedUsers() {
    $conn = Connect();
    
    try {
        // Check if users already exist
        $count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count > 0) {
            echo "Users already seeded, skipping...\n";
            return;
        }
        
        $users = [
            [
                'nom' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'points' => 0
            ],
            [
                'nom' => 'محمد أحمد',
                'email' => 'mohamed@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
                'points' => 100
            ],
            [
                'nom' => 'فاطمة علي',
                'email' => 'fatima@example.com',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
                'points' => 50
            ]
        ];

        $stmt = $conn->prepare("INSERT INTO users (nom, email, password, role, points) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($users as $user) {
            $stmt->execute([
                $user['nom'],
                $user['email'],
                $user['password'],
                $user['role'],
                $user['points']
            ]);
        }
    } catch (PDOException $e) {
        throw new Exception("Failed to seed users: " . $e->getMessage());
    }
}
