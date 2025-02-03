<?php
session_start();
require_once '../Acces_BD/connexion.php';
require_once '../Acces_BD/orders.php';
require_once '../Acces_BD/produits.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed', 405);
    }

    if (!isset($_SESSION['user'])) {
        throw new Exception('Authentication required', 401);
    }

    $userId = $_SESSION['user']['id'];
    $cart = $_SESSION['cart'] ?? [];
    
    if (empty($cart)) {
        throw new Exception('Cart is empty', 400);
    }

    $conn = Connect();
    $conn->beginTransaction();

    // Calculate total and validate stock
    $total = 0;
    $items = [];
    foreach ($cart as $productId => $quantity) {
        $product = getProduct($productId);
        if (!$product) {
            throw new Exception('Invalid product');
        }
        if ($product['quantite_stock'] < $quantity) {
            throw new Exception('Not enough stock for ' . $product['designation']);
        }

        $price = $product['promotion'] > 0 
            ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
            : $product['prix_unitaire'];
        
        $items[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ];
        
        $total += $price * $quantity;
    }

    if ($total <= 0) {
        throw new Exception('Invalid order total', 400);
    }

    $orderId = createOrder($userId, $total);

    foreach ($items as $item) {
        $currentStock = getCurrentStock($item['product_id']);
        if ($currentStock < $item['quantity']) {
            throw new Exception('Stock changed during checkout', 409);
        }

        addOrderItem($orderId, $item['product_id'], $item['quantity'], $item['price']);
        updateProductStock($item['product_id'], $item['quantity']);
    }

    $pointsEarned = floor($total / 10);
    updateUserPoints($userId, $pointsEarned);

    unset($_SESSION['cart']);
    $conn->commit();

    echo json_encode([
        'success' => true,
        'orderId' => $orderId,
        'pointsEarned' => $pointsEarned,
        'message' => 'Order processed successfully'
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
