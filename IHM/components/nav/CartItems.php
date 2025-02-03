<?php
$total = 0;
?>
<div class="cart-items-container">
<?php
if (!empty($_SESSION['cart'])):
    foreach ($_SESSION['cart'] as $productId => $quantity):
        $product = getProduct($productId);
        if (!$product) continue;
        
        $price = $product['promotion'] > 0 
            ? $product['prix_unitaire'] * (1 - $product['promotion']/100)
            : $product['prix_unitaire'];
        $subtotal = $price * $quantity;
        $total += $subtotal;
?>
    <div id="cart-item-<?= $productId ?>" class="flex flex-col p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors">
        <!-- Product Header -->
        <div class="flex gap-4">
            <div class="relative w-24 h-24 border border-amber-300/35 rounded-lg overflow-hidden flex items-center justify-center bg-white p-2">
                <img src="/IHM/public/images/products/<?= htmlspecialchars($product['image']) ?>"
                     alt="<?= htmlspecialchars($product['designation']) ?>"
                     class="w-full h-full object-contain">
                <?php if ($product['promotion'] > 0): ?>
                    <div class="absolute top-0 left-0 bg-red-500 text-white text-xs px-2 py-1 rounded-br">
                        -<?= $product['promotion'] ?>%
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex-1">
                <h3 class="font-medium text-green-900 mb-1">
                    <?= htmlspecialchars($product['designation']) ?>
                </h3>
                
                <!-- Price Section -->
                <div class="flex items-center gap-2">
                    <?php if ($product['promotion'] > 0): ?>
                        <span class="line-through text-gray-400 text-sm">
                            <?= number_format($product['prix_unitaire'], 2) ?> درهم
                        </span>
                        <span class="text-red-600 font-bold">
                            <?= number_format($price, 2) ?> درهم
                        </span>
                    <?php else: ?>
                        <span class="text-green-900 font-bold">
                            <?= number_format($price, 2) ?> درهم
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Stock Info -->
                <div class="text-sm <?= $product['quantite_stock'] > 10 ? 'text-green-600' : 'text-orange-500' ?> mt-1">
                    <?= $product['quantite_stock'] > 0 ? 'متوفر' : 'نفذ المخزون' ?>
                    (<?= $product['quantite_stock'] ?> كلغ)
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center border border-gray-300 rounded-lg">
                    <button type="button"
                            onclick="updateQuantity(<?= $productId ?>, 'decrease')"
                            class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <input type="number" 
                           id="quantity-<?= $productId ?>"
                           value="<?= $quantity ?>"
                           min="1"
                           max="<?= $product['quantite_stock'] ?>"
                           class="w-14 text-center border-x border-gray-300 py-1 text-gray-700"
                           onchange="updateQuantity(<?= $productId ?>, 'input', this.value)">
                    <button type="button"
                            onclick="updateQuantity(<?= $productId ?>, 'increase')"
                            class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>

                <button onclick="removeFromCart(<?= $productId ?>)"
                        class="flex items-center gap-1 text-red-600 hover:text-red-700 text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>إزالة</span>
                </button>
            </div>

            <!-- Subtotal -->
            <div class="text-right">
                <div class="font-bold text-green-900">
                    <span class="item-subtotal" data-price="<?= $price ?>">
                        <?= number_format($subtotal, 2) ?>
                    </span>
                    درهم
                </div>
                <div class="text-xs text-gray-500">
                    المجموع الفرعي
                </div>
            </div>
        </div>
    </div>
<?php
    endforeach;
else:
?>
    <div class="flex flex-col items-center justify-center py-8 text-gray-500">
        <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="text-lg font-medium mb-2">سلة التسوق فارغة</p>
        <a href="/IHM/Produits" class="text-green-600 hover:text-green-700">
            تصفح المنتجات
        </a>
    </div>
<?php
endif;
?>
</div>


