<?php
session_start();
require_once '../../Acces_BD/produits.php';
require_once '../../Acces_BD/categories.php';  // Add this line to include categories functions

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProduct($productId);

if (!$product) {
    header('Location: /IHM/Produits/index.php');
    exit();
}

// Get related products from same category
$relatedProducts = getProductsByCategory($product['category_id']);
// Remove current product from related products
$relatedProducts = array_filter($relatedProducts, fn($p) => $p['id'] != $productId);
// Limit to 4 related products
$relatedProducts = array_slice($relatedProducts, 0, 4);
?>
<!DOCTYPE html>
<html lang="fr" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/IHM/public/style/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title><?= htmlspecialchars($product['designation']) ?> - متجر المواد الغذائية</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>
    <?php include '../public/nav_barre.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8 text-sm">
            <ol class="list-none p-0 flex items-center space-x-2 space-x-reverse">
                <li><a href="/IHM/Produits/index.php" class="text-green-600 hover:text-green-800">الرئيسية</a></li>
                <li class="text-gray-500">/</li>
                <li><a href="/IHM/Categories/index.php?id=<?= $product['category_id'] ?>" class="text-green-600 hover:text-green-800">
                    <?= htmlspecialchars(getCategoryName($product['category_id'])) ?>
                </a></li>
                <li class="text-gray-500">/</li>
                <li class="text-gray-500"><?= htmlspecialchars($product['designation']) ?></li>
            </ol>
        </nav>

        <!-- Product Details -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Product Image -->
                <div class="relative">
                    <img src="/IHM/public/images/products/<?= htmlspecialchars($product['image']) ?>" 
                         alt="<?= htmlspecialchars($product['designation']) ?>"
                         class="w-full h-auto rounded-lg shadow-md">
                    <?php if ($product['promotion'] > 0): ?>
                        <div class="absolute top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-full">
                            -<?= $product['promotion'] ?>%
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="space-y-6">
                    <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($product['designation']) ?></h1>
                    
                    <div class="text-xl">
                        <?php if ($product['promotion'] > 0): ?>
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <span class="line-through text-gray-400">
                                    <?= number_format($product['prix_unitaire'], 2) ?> درهم
                                </span>
                                <span class="text-red-600 font-bold">
                                    <?= number_format($product['prix_unitaire'] * (1 - $product['promotion']/100), 2) ?> درهم
                                </span>
                            </div>
                        <?php else: ?>
                            <span class="font-bold text-gray-900">
                                <?= number_format($product['prix_unitaire'], 2) ?> درهم
                            </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                    <!-- Stock Status -->
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <span class="text-gray-700">الحالة:</span>
                        <span class="<?= $product['quantite_stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?= $product['quantite_stock'] > 0 ? 'متوفر في المخزون' : 'غير متوفر' ?>
                            <?= $product['quantite_stock'] > 0 ? "({$product['quantite_stock']} قطعة)" : '' ?>
                        </span>
                    </div>

                    <?php if ($product['quantite_stock'] > 0): ?>
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <input type="number" 
                                   id="quantity-<?= $product['id'] ?>" 
                                   min="1" 
                                   max="<?= $product['quantite_stock'] ?>" 
                                   value="1"
                                   class="w-20 px-3 py-2 border rounded text-center">
                            <button onclick="addToCart(<?= $product['id'] ?>)" 
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                إضافة إلى السلة
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Additional Info -->
                    <?php if (!empty($product['ingredients'])): ?>
                        <div class="border-t pt-4">
                            <h2 class="font-bold text-lg mb-2">المكونات:</h2>
                            <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['ingredients'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($product['nutritional_info'])): ?>
                        <div class="border-t pt-4">
                            <h2 class="font-bold text-lg mb-2">القيمة الغذائية:</h2>
                            <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['nutritional_info'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($product['allergens'])): ?>
                        <div class="border-t pt-4">
                            <h2 class="font-bold text-lg mb-2">مسببات الحساسية:</h2>
                            <p class="text-gray-600"><?= nl2br(htmlspecialchars($product['allergens'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <section class="mt-12">
                <h2 class="text-2xl font-bold mb-6">منتجات ذات صلة</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <?php foreach ($relatedProducts as $product): ?>
                        <?php include 'product_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include '../public/footer.php'; ?>

    <script>
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
