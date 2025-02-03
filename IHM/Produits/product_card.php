<div class="bg-white rounded-xl border border-slate-300/45 shadow-sm hover:shadow-md transition-shadow duration-300">
    <div class="relative group">
        <a
        class=" block w-full h-60 relative rounded-t-xl overflow-hidden flex items-center justify-center"
        href="/IHM/Produits/details.php?id=<?= $product['id'] ?>">
            <img src="/IHM/public/images/products/<?= htmlspecialchars($product['image']) ?>" 
                 alt="<?= htmlspecialchars($product['designation']) ?>"
                 class="w-[70%]  object-cover rounded-t-xl">
        </a>
             
        <?php if ($product['promotion'] > 0): ?>
            <div class="absolute top-2 left-2 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                -<?= $product['promotion'] ?>%
            </div>
        <?php endif; ?>
        
        <!-- Quick view button -->
        <button onclick="showProductDetails(<?= htmlspecialchars(json_encode($product)) ?>)"
                class="absolute bottom-2 right-2 bg-white/90 hover:bg-white text-green-800 px-3 py-1 rounded-full 
                       text-sm font-medium  transition-opacity duration-200">
            عرض سريع
        </button>
    </div>
    <hr class="border-t border-gray-100">
    <div class="py-2  space-y-3">
        <a href="/IHM/Produits/details.php?id=<?= $product['id'] ?>" 
           class="block hover:text-green-600 px-2 transition-colors">
            <h3 class="text-lg font-bold text-green-900 min-h-[1rem] line-clamp-2">
                <?= htmlspecialchars($product['designation']) ?>
            </h3>
        </a>

        <!-- Product Description -->
        <p class="text-gray-600 px-2 text-sm line-clamp-2 min-h-[1.5rem]">
            <?= htmlspecialchars($product['description'] ?? '') ?>
        </p>

        <!-- Price Section -->
        <div class="flex justify-between items-center border-t border-gray-100 px-2">
            <div class="space-y-1">
                <?php if ($product['promotion'] > 0): ?>
                    <div class="flex flex-col">
                        <span class="line-through text-gray-400 text-sm">
                            <?= number_format($product['prix_unitaire'], 2) ?> درهم
                        </span>
                        <span class="text-red-600 font-bold text-lg">
                            <?= number_format($product['prix_unitaire'] * (1 - $product['promotion']/100), 2) ?> درهم
                        </span>
                    </div>
                <?php else: ?>
                    <span class="text-green-900 font-bold text-lg">
                        <?= number_format($product['prix_unitaire'], 2) ?> درهم
                    </span>
                <?php endif; ?>
            </div>

            <!-- Stock Status -->
            <div class="text-sm <?= $product['quantite_stock'] > 10 ? 'text-green-600' : 'text-orange-500' ?>">
                <?= $product['quantite_stock'] > 0 ? 'متوفر' : 'نفذ المخزون' ?>
            </div>
        </div>

        <!-- Add to Cart Section -->
        <div class="flex items-center gap-2 px-2">
            <div class="flex items-center border border-gray-300 rounded-lg">
                <button type="button"
                        onclick="updateProductQuantity(<?= $product['id'] ?>, 'decrease')"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <input type="number" 
                       id="product-quantity-<?= $product['id'] ?>"
                       min="1" 
                       max="<?= $product['quantite_stock'] ?>" 
                       value="1"
                       class="w-16 text-center border-x border-gray-300 py-1 text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-500"
                       oninput="validateQuantity(this, <?= $product['quantite_stock'] ?>)">
                <button type="button"
                        onclick="updateProductQuantity(<?= $product['id'] ?>, 'increase')"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
            
            <button onclick="addToCart(<?= $product['id'] ?>)" 
                    class="flex-1 w-9 bg-amber-100 text-amber-400 border border-amber-500/45 px-4 py-2 rounded-lg hover:bg-green-900 transition-colors 
                           flex items-center justify-center gap-2">
                           <svg
    class="size-5"
    width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
 <path d="M5.00014 14H18.1359C19.1487 14 19.6551 14 20.0582 13.8112C20.4134 13.6448 20.7118 13.3777 20.9163 13.0432C21.1485 12.6633 21.2044 12.16 21.3163 11.1534L21.9013 5.88835C21.9355 5.58088 21.9525 5.42715 21.9031 5.30816C21.8597 5.20366 21.7821 5.11697 21.683 5.06228C21.5702 5 21.4155 5 21.1062 5H4.50014M2 2H3.24844C3.51306 2 3.64537 2 3.74889 2.05032C3.84002 2.09463 3.91554 2.16557 3.96544 2.25376C4.02212 2.35394 4.03037 2.48599 4.04688 2.7501L4.95312 17.2499C4.96963 17.514 4.97788 17.6461 5.03456 17.7462C5.08446 17.8344 5.15998 17.9054 5.25111 17.9497C5.35463 18 5.48694 18 5.75156 18H19M7.5 21.5H7.51M16.5 21.5H16.51M8 21.5C8 21.7761 7.77614 22 7.5 22C7.22386 22 7 21.7761 7 21.5C7 21.2239 7.22386 21 7.5 21C7.77614 21 8 21.2239 8 21.5ZM17 21.5C17 21.7761 16.7761 22 16.5 22C16.2239 22 16 21.7761 16 21.5C16 21.2239 16.2239 21 16.5 21C16.7761 21 17 21.2239 17 21.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
 </svg>
            </button>
        </div>
    </div>
</div>

<!-- Add this script at the bottom of your page or in a separate JS file -->
<script>
function updateProductQuantity(productId, action) {
    const input = document.getElementById(`product-quantity-${productId}`);
    const currentQty = parseInt(input.value);
    const maxQty = parseInt(input.max);

    switch(action) {
        case 'increase':
            if (currentQty < maxQty) {
                input.value = currentQty + 1;
            }
            break;
        case 'decrease':
            if (currentQty > 1) {
                input.value = currentQty - 1;
            }
            break;
    }
}

function validateQuantity(input, maxStock) {
    let value = parseInt(input.value);
    if (isNaN(value) || value < 1) {
        input.value = 1;
    } else if (value > maxStock) {
        input.value = maxStock;
        showToast('عذراً، لا يمكن تجاوز الكمية المتوفرة في المخزون', 'error');
    }
}

function addToCart(productId) {
    const quantity = parseInt(document.getElementById(`product-quantity-${productId}`).value);
    
    $.ajax({
        url: '/IHM/api/cart.php',
        method: 'POST',
        data: {
            action: 'add',
            productId: productId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                // Update cart count
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent = response.cartCount;
                });
                showToast('تمت إضافة المنتج إلى السلة', 'success');
            } else {
                showToast(response.message || 'حدث خطأ ما', 'error');
            }
        },
        error: function() {
            showToast('حدث خطأ في الاتصال', 'error');
        }
    });
}
</script>
