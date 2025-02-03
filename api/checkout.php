<?php
session_start();
require_once '../Acces_BD/commandes.php';
require_once '../Acces_BD/produits.php';

header('Content-Type: application/json');

// Verify if user is logged in
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

// Verify if cart is not empty
if (empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

try {
    // Calculate total and prepare items
    $total = 0;
    $items = [];
    
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProduct($productId);
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        // Check stock availability
        if ($product['quantite_stock'] < $quantity) {
            throw new Exception("Not enough stock for {$product['designation']}");
        }
        
        $price = $product['promotion'] > 0 
            ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
            : $product['prix_unitaire'];
            
        $total += $price * $quantity;
        $items[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ];
    }

    // Create order
    $orderId = createOrder([
        'user_id' => $_SESSION['user']['id'],
        'total' => $total,
        'status' => 'pending',
        'items' => $items
    ]);

    if ($orderId) {
        // Clear cart after successful order
        $_SESSION['cart'] = [];
        echo json_encode([
            'success' => true, 
            'orderId' => $orderId,
            'redirect' => '/IHM/Orders/confirmation.php?id=' . $orderId
        ]);
    } else {
        throw new Exception('Failed to create order');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
