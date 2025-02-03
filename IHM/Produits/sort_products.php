<?php
require_once '../../Acces_BD/connexion.php';
require_once '../../Acces_BD/produits.php';

$sort = $_GET['sort'] ?? 'newest';

$conn = Connect();

try {
    $sql = "SELECT * FROM produits ";
    
    switch ($sort) {
        case 'newest':
            $sql .= "ORDER BY id DESC";
            break;
        case 'price-asc':
            $sql .= "ORDER BY prix_unitaire ASC";
            break;
        case 'price-desc':
            $sql .= "ORDER BY prix_unitaire DESC";
            break;
        default:
            $sql .= "ORDER BY id DESC";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        include 'product_card.php';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
