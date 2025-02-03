<?php
session_start();
require_once '../../Acces_BD/commandes.php';
require_once '../../Acces_BD/produits.php';

// Verify if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: /IHM/Auth/login.php');
    exit();
}

// Verify if cart is not empty
if (empty($_SESSION['cart'])) {
    header('Location: /IHM/Cart/index.php');
    exit();
}

try {
    // Calculate total
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
        // Clear cart
        $_SESSION['cart'] = [];
        
        // Add success message
        $_SESSION['success_message'] = 'تم تأكيد طلبك بنجاح!';
        
        // Redirect to order confirmation
        header('Location: /IHM/Orders/confirmation.php?id=' . $orderId);
        exit();
    } else {
        throw new Exception('Failed to create order');
    }

} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: /IHM/Cart/index.php');
    exit();
}
