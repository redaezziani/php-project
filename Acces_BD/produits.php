<?php
require_once 'connexion.php';

// Initialize database if needed
initializeDatabase();

function getAllProducts() {
    $conn = Connect();
    $stmt = $conn->query("SELECT * FROM produits WHERE quantite_stock > 0");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProduct($id) {
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get a single product by its ID
 * @param int $id Product ID
 * @return array|null Product data or null if not found
 */
function getProductById($id) {
    $conn = Connect(); // Use Connect() instead of global $connexion
    
    try {
        $query = "SELECT * FROM produits WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute(['id' => $id]);
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ?: null;
    } catch (PDOException $e) {
        // Log error if needed
        error_log("Error retrieving product: " . $e->getMessage());
        return null;
    }
}

function addProduct($data) {
    $conn = Connect();
    $stmt = $conn->prepare("
        INSERT INTO produits (reference, designation, prix_unitaire, quantite_stock, promotion)
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['reference'],
        $data['designation'],
        $data['prix_unitaire'],
        $data['quantite_stock'],
        $data['promotion'] ?? 0
    ]);
}

function searchProducts($query) {
    $conn = Connect();
    $searchTerm = "%$query%";
    $stmt = $conn->prepare("
        SELECT p.*, c.nom as category_name 
        FROM produits p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE (p.designation LIKE ? 
        OR p.description LIKE ? 
        OR c.nom LIKE ?)
        AND p.quantite_stock > 0
    ");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPromotionProducts($limit = null) {
    $conn = Connect();
    $sql = "SELECT * FROM produits WHERE promotion > 0 AND quantite_stock > 0 ORDER BY promotion DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLatestProducts($limit = 10) {
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM produits WHERE quantite_stock > 0 ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCheapestProducts($limit = 4) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT *, 
        CASE 
            WHEN promotion > 0 THEN prix_unitaire * (1 - promotion/100)
            ELSE prix_unitaire 
        END as final_price 
        FROM produits 
        WHERE quantite_stock > 0 
        ORDER BY final_price ASC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Remove this duplicate function
/* function getProductsByCategory($categoryId) {
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM produits WHERE category_id = ? AND quantite_stock > 0");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} */
