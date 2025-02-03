<?php
require_once 'connexion.php';

function createOrder($userId, $total) {
    $conn = Connect();
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total, status, created_at) 
        VALUES (?, ?, 'pending', datetime('now'))
    ");
    $stmt->execute([$userId, $total]);
    return $conn->lastInsertId();
}

function addOrderItem($orderId, $productId, $quantity, $price) {
    $conn = Connect();
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$orderId, $productId, $quantity, $price]);
}

function updateProductStock($productId, $quantity) {
    $conn = Connect();
    $stmt = $conn->prepare("
        UPDATE produits 
        SET quantite_stock = quantite_stock - ? 
        WHERE id = ?
    ");
    $stmt->execute([$quantity, $productId]);
}

function getCurrentStock($productId) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT quantite_stock 
        FROM produits 
        WHERE id = ?
    ");
    $stmt->execute([$productId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['quantite_stock'] : 0;
}

function updateUserPoints($userId, $points) {
    $conn = Connect();
    $stmt = $conn->prepare("
        UPDATE users 
        SET points = points + ? 
        WHERE id = ?
    ");
    $stmt->execute([$points, $userId]);
}

function getOrder($orderId) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT o.*, u.nom as user_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getOrderItems($orderId) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT oi.*, p.designation, p.image 
        FROM order_items oi 
        JOIN produits p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserOrders($userId) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT * 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
