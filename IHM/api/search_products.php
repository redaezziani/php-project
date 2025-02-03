<?php
require_once '../../Acces_BD/produits.php';

if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    
    if (strlen($query) >= 2) {
        $results = searchProducts($query);
        echo json_encode($results);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
