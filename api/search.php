<?php
require_once '../Acces_BD/produits.php';

header('Content-Type: application/json');

if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    if (strlen($query) >= 2) {
        $results = searchProducts($query);
        echo json_encode(['success' => true, 'products' => $results]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Query too short']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No query provided']);
}
