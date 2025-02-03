<?php
session_start();
require_once '../Acces_BD/produits.php';

header('Content-Type: application/json');

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Verify that we have POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';
$productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Verify product exists and get its details
$product = getProduct($productId);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

switch ($action) {
    case 'add':
        if ($quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
            exit;
        }
        
        if ($quantity > $product['quantite_stock']) {
            echo json_encode([
                'success' => false, 
                'message' => 'Not enough stock available'
            ]);
            exit;
        }

        // Add or update quantity in cart
        if (isset($_SESSION['cart'][$productId])) {
            $newQuantity = $_SESSION['cart'][$productId] + $quantity;
            if ($newQuantity > $product['quantite_stock']) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cannot add more items than available in stock'
                ]);
                exit;
            }
            $_SESSION['cart'][$productId] = $newQuantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        break;

    case 'remove':
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        break;

    case 'update':
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } elseif ($quantity > $product['quantite_stock']) {
            echo json_encode([
                'success' => false, 
                'message' => 'Not enough stock available'
            ]);
            exit;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

// Calculate cart total
function calculateCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProduct($productId);
        if ($product) {
            $price = $product['promotion'] > 0 
                ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
                : $product['prix_unitaire'];
            $total += $price * $quantity;
        }
    }
    return $total;
}

// After the switch statement, before the final echo:
$cartTotal = calculateCartTotal();

echo json_encode([
    'success' => true,
    'cartCount' => array_sum($_SESSION['cart']),
    'cartItems' => $_SESSION['cart'],
    'cartTotal' => number_format($cartTotal, 2)
]);
