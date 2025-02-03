<?php
session_start();
require_once '../../Acces_BD/connexion.php';
require_once '../../Acces_BD/produits.php';

// Clear any output buffers
while (ob_get_level()) ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed', 405);
    }

    if (!isset($_POST['action'], $_POST['productId'])) {
        throw new Exception('Missing required parameters', 400);
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $action = $_POST['action'];
    $productId = (int)$_POST['productId'];
    $product = getProductById($productId);

    if (!$product) {
        throw new Exception('Product not found', 404);
    }

    $response = ['success' => true];

    switch ($action) {
        case 'add':
            $quantity = max(1, min((int)($_POST['quantity'] ?? 1), $product['quantite_stock']));
            
            // Check if adding this quantity exceeds stock
            $currentQty = $_SESSION['cart'][$productId] ?? 0;
            $newQty = $currentQty + $quantity;
            
            if ($newQty > $product['quantite_stock']) {
                throw new Exception('Requested quantity exceeds available stock', 400);
            }
            
            $_SESSION['cart'][$productId] = $newQty;
            $response['message'] = 'Product added to cart';
            break;

        case 'update':
            $quantity = max(1, min((int)$_POST['quantity'], $product['quantite_stock']));
            $_SESSION['cart'][$productId] = $quantity;
            $response['message'] = 'Cart updated';
            break;

        case 'remove':
            unset($_SESSION['cart'][$productId]);
            $response['message'] = 'Product removed from cart';
            break;

        default:
            throw new Exception('Invalid action', 400);
    }

    // Calculate cart totals
    $cartCount = array_sum($_SESSION['cart']);
    $cartTotal = calculateCartTotal();

    // Add cart info to response
    $response += [
        'cartCount' => $cartCount,
        'cartTotal' => number_format($cartTotal, 2),
        'cartContents' => $_SESSION['cart']
    ];

    echo json_encode($response, JSON_THROW_ON_ERROR);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'code' => $e->getCode() ?: 500
    ], JSON_THROW_ON_ERROR);
}

function calculateCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        $product = getProductById($pid);
        if ($product) {
            $price = $product['promotion'] > 0 
                ? $product['prix_unitaire'] * (1 - $product['promotion']/100) 
                : $product['prix_unitaire'];
            $total += $price * $qty;
        }
    }
    return $total;
}
