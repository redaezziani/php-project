<?php
require_once __DIR__ . '/../../Acces_BD/connexion.php';

function seedCategories() {
    $conn = Connect();
    
    $categories = [
        ['nom' => 'الخضروات والفواكه'],
        ['nom' => 'اللحوم والدواجن'],
        ['nom' => 'منتجات الألبان'],
        ['nom' => 'المشروبات'],
        ['nom' => 'المعلبات'],
        ['nom' => 'البقوليات'],
        ['nom' => 'التوابل والبهارات'],
        ['nom' => 'الحلويات والشوكولاتة']
    ];

    $stmt = $conn->prepare("INSERT INTO categories (nom) VALUES (?)");
    
    foreach ($categories as $category) {
        $stmt->execute([$category['nom']]);
    }
}
