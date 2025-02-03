<?php
session_start();
require_once '../../Acces_BD/connexion.php';
require_once '../../Acces_BD/categories.php';
// No need to require produits.php since getProductsByCategory is in categories.php

$categories = getAllCategories();
$selectedCategoryId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$products = $selectedCategoryId ? getProductsByCategory($selectedCategoryId) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/IHM/public/style/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Catégories - Magasin Alimentaire</title>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include '../public/header.php'; ?>
    <?php include '../public/nav_barre.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-4 gap-6">
            <!-- Categories Sidebar -->
            <div class="col-span-1">
                <h2 class="text-xl font-bold mb-4">الفئات</h2>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <ul class="space-y-2">
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="?id=<?= $category['id'] ?>" 
                                   class="block p-2 rounded <?= ($selectedCategoryId == $category['id']) ? 'bg-green-100 text-green-600' : 'hover:bg-gray-100' ?>">
                                    <?= htmlspecialchars($category['nom']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-span-3">
                <?php if ($selectedCategoryId): ?>
                    <h2 class="text-xl font-bold mb-4">
                        <?= htmlspecialchars($categories[array_search($selectedCategoryId, array_column($categories, 'id'))]['nom']) ?>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <?php foreach ($products as $product): ?>
                            <?php include '../Produits/product_card.php'; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($products)): ?>
                        <div class="text-center py-8 text-gray-500">
                            لا توجد منتجات متوفرة في هذه الفئة.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        اختر فئة لعرض المنتجات.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../public/footer.php'; ?>

    <script>
        // Add cart functionality
        function addToCart(productId) {
            const quantity = document.getElementById(`quantity-${productId}`).value;
            $.ajax({
                url: '/api/cart.php',
                method: 'POST',
                data: { 
                    action: 'add',
                    productId: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    </script>
</body>
</html>
