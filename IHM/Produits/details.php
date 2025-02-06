<?php
require_once '../../Acces_BD/connexion.php';
require_once '../../Acces_BD/produits.php';
session_start();

// Get product ID from URL and validate it
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch the specific product
$conn = Connect();
try {
    $stmt = $conn->prepare("
        SELECT p.*, c.nom as category_name 
        FROM produits p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    // Handle error
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/IHM/public/style/main.css">
    <link rel="icon" href="/IHM/public/images/favicon/icon.png" type="image/x-icon">
    <title><?php echo htmlspecialchars($product['designation']); ?> - متجر المواد الغذائية</title>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <?php include '../public/header.php'; ?>
    <?php include '../public/nav_barre.php'; ?>

    <main class="container mx-auto px-4 py-8 flex-grow">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Product Image -->
                <div class="flex items-center justify-center bg-gray-100 rounded-lg p-4">
                    <img src="/IHM/public/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['designation']); ?>"
                         class="max-h-[400px] object-contain">
                </div>

                <!-- Product Details -->
                <div class="space-y-6">
                    <h1 class="text-3xl font-bold text-gray-900">
                        <?php echo htmlspecialchars($product['designation']); ?>
                    </h1>
                    
                    <div class="text-gray-600">
                        <p class="text-lg leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold text-green-600">
                            <?php 
                            if ($product['promotion'] > 0) {
                                $discounted_price = $product['prix_unitaire'] * (1 - $product['promotion']/100);
                                echo '<span class="line-through text-gray-400 text-lg ml-2">' . 
                                     number_format($product['prix_unitaire'], 2) . '</span>';
                                echo number_format($discounted_price, 2);
                            } else {
                                echo number_format($product['prix_unitaire'], 2);
                            }
                            ?> درهم
                        </div>
                        <?php if ($product['promotion'] > 0): ?>
                            <div class="bg-red-100 text-red-800 text-sm font-semibold px-3 py-1 rounded">
                                خصم <?php echo $product['promotion']; ?>%
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex items-center gap-4">
                            <div class="text-gray-600">الكمية المتوفرة:</div>
                            <div class="font-semibold <?php echo $product['quantite_stock'] > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $product['quantite_stock']; ?> قطعة
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <h3 class="font-semibold text-gray-900">المكونات</h3>
                            <p class="text-gray-600"><?php echo $product['ingredients']; ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">المعلومات الغذائية</h3>
                            <p class="text-gray-600"><?php echo $product['nutritional_info']; ?></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">الحساسية</h3>
                            <p class="text-gray-600"><?php echo $product['allergens']; ?></p>
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <?php if ($product['quantite_stock'] > 0): ?>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors">
                            أضف إلى السلة
                        </button>
                    <?php else: ?>
                        <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-lg cursor-not-allowed">
                            غير متوفر حالياً
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../public/footer.php'; ?>
</body>
</html>
