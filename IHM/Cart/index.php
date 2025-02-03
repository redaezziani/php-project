<?php
session_start();
header('Location: /IHM/Produits/index.php');
exit();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/IHM/public/style/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>سلة التسوق - متجر المواد الغذائية</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>
    <?php include '../public/nav_barre.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
            <div class="text-center py-16">
                <h2 class="text-2xl font-bold mb-4">سلة التسوق فارغة</h2>
                <a href="/IHM/Produits/index.php" 
                   class="inline-block bg-green-800 text-amber-200 px-6 py-3 rounded-lg hover:bg-green-900">
                    تصفح المنتجات
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="md:col-span-2">
                    <h2 class="text-2xl font-bold mb-6">سلة التسوق</h2>
                    <div class="space-y-4">
                        <?php 
                        $total = 0;
                        foreach ($_SESSION['cart'] as $productId => $quantity):
                            $product = getProduct($productId);
                            if (!$product) continue;
                            
                            $price = $product['promotion'] > 0 
                                ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
                                : $product['prix_unitaire'];
                            $subtotal = $price * $quantity;
                            $total += $subtotal;
                        ?>
                            <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
                                <img src="/IHM/public/images/products/<?= htmlspecialchars($product['image']) ?>"
                                     alt="<?= htmlspecialchars($product['designation']) ?>"
                                     class="w-24 h-24 object-cover rounded">
                                
                                <div class="flex-1">
                                    <h3 class="font-semibold"><?= htmlspecialchars($product['designation']) ?></h3>
                                    <div class="text-gray-600">
                                        <span class="left-to-right inline-block"><?= number_format($price, 2) ?> درهم</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 mt-2">
                                        <input type="number" 
                                               value="<?= $quantity ?>" 
                                               min="1" 
                                               max="<?= $product['quantite_stock'] ?>"
                                               class="w-20 px-2 py-1 border rounded text-center"
                                               onchange="updateCartQuantity(<?= $productId ?>, this.value)">
                                        
                                        <button onclick="removeFromCart(<?= $productId ?>)" 
                                                class="text-red-600 hover:text-red-800">
                                            حذف
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-lg font-bold left-to-right">
                                    <?= number_format($subtotal, 2) ?> درهم
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                        <h3 class="text-xl font-bold mb-4">ملخص الطلب</h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span>المجموع الفرعي:</span>
                                <span class="left-to-right"><?= number_format($total, 2) ?> درهم</span>
                            </div>
                            <div class="flex justify-between font-bold text-lg border-t pt-2">
                                <span>المجموع الكلي:</span>
                                <span class="left-to-right cart-total"><?= number_format($total, 2) ?> درهم</span>
                            </div>
                        </div>

                        <a href="/IHM/Checkout/index.php" 
                           class="block w-full bg-green-800 text-amber-200 text-center py-3 rounded-lg hover:bg-green-900">
                            متابعة الشراء
                        </a>

                        <form action="/IHM/Checkout/process.php" method="POST" id="checkoutForm" class="mt-4">
                            <button type="submit" 
                                    class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors"
                                    onclick="return confirmCheckout()">
                                تأكيد الطلب
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../public/footer.php'; ?>
    
    <script>
        function updateCart(productId) {
            const quantity = document.getElementById(`quantity-${productId}`).value;
            $.ajax({
                url: '/api/cart.php',
                method: 'POST',
                data: { 
                    action: 'update',
                    productId: productId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        // Update total without page reload
                        $('.cart-total').text(response.cartTotal + ' DH');
                        // Update cart count in nav
                        $('.cart-count').text(response.cartCount);
                        // Update item subtotal
                        const price = products.find(p => p.id === productId).price;
                        $(`#subtotal-${productId}`).text((price * quantity).toFixed(2) + ' DH');
                    } else {
                        alert(response.message);
                    }
                }
            });
        }

        function removeFromCart(productId) {
            if (confirm('Êtes-vous sûr de vouloir retirer cet article ?')) {
                $.ajax({
                    url: '/api/cart.php',
                    method: 'POST',
                    data: { 
                        action: 'remove',
                        productId: productId
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove item element without page reload
                            $(`#cart-item-${productId}`).remove();
                            // Update cart count in nav
                            $('.cart-count').text(response.cartCount);
                            // Update total
                            $('.cart-total').text(response.cartTotal + ' DH');
                            
                            // If cart is empty, redirect to products
                            if (response.cartCount === 0) {
                                window.location.href = '/IHM/Produits';
                            }
                        }
                    }
                });
            }
        }

        function proceedToCheckout() {
            window.location.href = '/IHM/Checkout';
        }

        function confirmCheckout() {
            if (confirm('هل أنت متأكد من تأكيد الطلب؟')) {
                $.ajax({
                    url: '/api/checkout.php',
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('حدث خطأ أثناء معالجة طلبك');
                    }
                });
            }
            return false;
        }
    </script>
</body>
</html>
