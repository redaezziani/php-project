<?php
session_start();
require_once '../../Acces_BD/produits.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
    
    switch ($action) {
        case 'remove':
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                
                // Calculate new cart total
                $total = 0;
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    $product = getProduct($pid);
                    if ($product) {
                        $price = $product['promotion'] > 0 
                            ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
                            : $product['prix_unitaire'];
                        $total += $price * $qty;
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'cartCount' => array_sum($_SESSION['cart']),
                    'cartTotal' => number_format($total, 2)
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Product not found in cart'
                ]);
            }
            break;
            
        // ...existing update case...
    }
}
