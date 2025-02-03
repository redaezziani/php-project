<?php
session_start();
require_once '../../Acces_BD/produits.php';

if (!isset($_SESSION['user'])) {
    header('Location: /IHM/Auth/login.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: /IHM/Produits/index.php');
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;
$cartItems = [];

foreach ($cart as $productId => $quantity) {
    $product = getProduct($productId);
    if ($product) {
        $price = $product['promotion'] > 0 
            ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
            : $product['prix_unitaire'];
        $total += $price * $quantity;
        $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity,
            'price' => $price
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Confirmer la commande - Magasin Alimentaire</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Confirmer la commande</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Résumé de la commande</h2>
                
                <?php foreach ($cartItems as $item): ?>
                    <div class="flex items-center justify-between py-4 border-b">
                        <div class="flex items-center">
                            <img src="/IHM/public/images/products/<?= htmlspecialchars($item['product']['image']) ?>" 
                                 alt="<?= htmlspecialchars($item['product']['designation']) ?>"
                                 class="w-16 h-16 object-cover rounded">
                            <div class="ml-4">
                                <h3 class="font-semibold"><?= htmlspecialchars($item['product']['designation']) ?></h3>
                                <p class="text-gray-600">Quantité: <?= $item['quantity'] ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold"><?= number_format($item['price'] * $item['quantity'], 2) ?> DH</p>
                            <p class="text-sm text-gray-500"><?= number_format($item['price'], 2) ?> DH/unité</p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-6 text-right">
                    <p class="text-lg font-bold">Total: <?= number_format($total, 2) ?> DH</p>
                    <p class="text-sm text-gray-500">Points à gagner: <?= floor($total / 10) ?></p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="/IHM/Produits/index.php" 
                   class="px-6 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                    Continuer les achats
                </a>
                <button onclick="processOrder()" 
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Confirmer la commande
                </button>
            </div>
        </div>
    </main>
    
    <?php include '../public/footer.php'; ?>

    <script>
        function processOrder() {
            const submitButton = document.querySelector('button[onclick="processOrder()"]');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Traitement en cours...';

            $.ajax({
                url: '/api/orders.php',
                method: 'POST',
                timeout: 10000, // 10 second timeout
                success: function(response) {
                    if (response.success) {
                        window.location.href = `/IHM/Orders/confirmation.php?id=${response.orderId}`;
                    } else {
                        alert(response.message || 'Une erreur est survenue lors du traitement de la commande');
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Confirmer la commande';
                    }
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Une erreur est survenue lors de la communication avec le serveur';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (status === 'timeout') {
                        errorMessage = 'Le serveur met trop de temps à répondre. Veuillez réessayer.';
                    }
                    
                    alert(errorMessage);
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Confirmer la commande';
                }
            });
        }
    </script>
</body>
</html>
