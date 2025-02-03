<?php
session_start();
require_once '../../Acces_BD/produits.php';

$response = [
    'items' => [],
    'total' => 0,
    'count' => 0
];

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProduct($productId);
        if ($product) {
            $price = $product['promotion'] > 0 
                ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
                : $product['prix_unitaire'];
            
            $response['items'][] = [
                'id' => $productId,
                'designation' => $product['designation'],
                'image' => $product['image'],
                'price' => number_format($price, 2),
                'quantity' => $quantity,
                'stock' => $product['quantite_stock'],
                'subtotal' => number_format($price * $quantity, 2)
            ];
            
            $response['total'] += $price * $quantity;
            $response['count'] += $quantity;
        }
    }
}

$response['total'] = number_format($response['total'], 2);
echo json_encode($response);
