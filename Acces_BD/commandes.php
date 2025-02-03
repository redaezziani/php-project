<?php
require_once 'connexion.php';

function createOrder($orderData) {
    $conn = Connect();
    try {
        $conn->beginTransaction();

        // Insert main order
        $stmt = $conn->prepare("
            INSERT INTO commandes (user_id, total, status, date_creation) 
            VALUES (?, ?, ?, datetime('now'))
        ");
        $stmt->execute([
            $orderData['user_id'],
            $orderData['total'],
            $orderData['status']
        ]);
        
        $orderId = $conn->lastInsertId();

        // Insert order items
        $stmt = $conn->prepare("
            INSERT INTO commande_items (commande_id, produit_id, quantite, prix_unitaire)
            VALUES (?, ?, ?, ?)
        ");

        // Update product stock
        $updateStock = $conn->prepare("
            UPDATE produits 
            SET quantite_stock = quantite_stock - ? 
            WHERE id = ?
        ");

        foreach ($orderData['items'] as $item) {
            // Insert order item
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);

            // Update stock
            $updateStock->execute([
                $item['quantity'],
                $item['product_id']
            ]);
        }

        $conn->commit();
        return $orderId;

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
}

function getOrder($orderId) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT c.*, u.nom as user_name, u.email
        FROM commandes c
        JOIN users u ON c.user_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        // Get order items
        $stmt = $conn->prepare("
            SELECT ci.*, p.designation, p.image
            FROM commande_items ci
            JOIN produits p ON ci.produit_id = p.id
            WHERE ci.commande_id = ?
        ");
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $order;
}

function getUserOrders($userId) {
    $conn = Connect();
    $stmt = $conn->prepare("
        SELECT * FROM commandes 
        WHERE user_id = ? 
        ORDER BY date_creation DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
