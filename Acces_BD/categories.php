<?php
require_once 'connexion.php';

function getAllCategories() {
    $conn = Connect();
    $stmt = $conn->query("SELECT * FROM categories ORDER BY nom");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategory($id) {
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductsByCategory($categoryId) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT * FROM produits 
        WHERE category_id = ? 
        AND quantite_stock > 0
        ORDER BY designation
    ");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getCategoryName($categoryId) {
    $category = getCategory($categoryId);
    return $category ? $category['nom'] : '';
}