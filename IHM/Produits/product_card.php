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
        <div x-data="{
    quantity: <?= isset($_SESSION['cart'][$product['id']]) ? $_SESSION['cart'][$product['id']] : 1 ?>,
    inCart: <?= isset($_SESSION['cart'][$product['id']]) ? 'true' : 'false' ?>,
    isLoading: false,
    maxStock: <?= $product['quantite_stock'] ?>,
    
    async updateCart(action, qty = null) {
        if (this.isLoading) return;
        this.isLoading = true;
        
        try {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('productId', <?= $product['id'] ?>);
            if (qty !== null) formData.append('quantity', qty);
            
            const response = await fetch('/IHM/api/cart.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (!response.ok) throw new Error(data.message);
            
            if (data.success) {
                if (action === 'remove') {
                    this.inCart = false;
                    this.quantity = 1;
                } else {
                    this.inCart = true;
                    this.quantity = data.cartContents[<?= $product['id'] ?>] || 1;
                }
                
                // Update cart count in header
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent = data.cartCount;
                });
                
                showToast(data.message, 'success');
            }
        } catch (error) {
            console.error('Cart error:', error);
            showToast(error.message || 'حدث خطأ في السلة', 'error');
        } finally {
            this.isLoading = false;
        }
    }
}"
class="flex items-center gap-2 px-2">
    <!-- Quantity Controls -->
    <div class="flex items-center border border-gray-300 rounded-lg">
        <button type="button"
                @click="quantity > 1 && updateCart('update', quantity - 1)"
                :disabled="isLoading || quantity <= 1"
                class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg transition-colors 
                       disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
        </button>
        
        <input type="number" 
               x-model.number="quantity"
               min="1" 
               max="<?= $product['quantite_stock'] ?>"
               @change="updateCart('update', quantity)"
               :disabled="isLoading"
               class="w-16 text-center border-x border-gray-300 py-1 text-gray-700 
                      focus:outline-none focus:ring-1 focus:ring-green-500
                      disabled:opacity-50 disabled:cursor-not-allowed">
        
        <button type="button"
                @click="quantity < maxStock && updateCart('update', quantity + 1)"
                :disabled="isLoading || quantity >= maxStock"
                class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg transition-colors
                       disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </button>
    </div>

    <!-- Add/Remove Button -->
    <button @click="inCart ? updateCart('remove') : updateCart('add', quantity)"
            :disabled="isLoading"
            :class="{'bg-red-500 hover:bg-red-700': inCart,
                    'bg-green-500 hover:bg-green-700': !inCart}"
            class="flex-1 text-white px-4 py-2 rounded-lg transition-colors 
                   flex items-center justify-center gap-2
                   disabled:opacity-50 disabled:cursor-not-allowed">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path x-show="!inCart" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            <path x-show="inCart" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        <!-- <span x-text="inCart ? (isLoading ? 'جاري الحذف...' : 'إزالة من السلة') : 
                        (isLoading ? 'جاري الإضافة...' : 'أضف إلى السلة')"></span> -->
    </button>
</div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cartItem', () => ({
        quantity: <?= isset($_SESSION['cart'][$product['id']]) ? $_SESSION['cart'][$product['id']] : 1 ?>,
        isLoading: false,
        inCart: <?= isset($_SESSION['cart'][$product['id']]) ? 'true' : 'false' ?>,
        maxStock: <?= $product['quantite_stock'] ?>,
        productId: <?= $product['id'] ?>,

        updateQuantity(action) {
            let newQuantity = this.quantity;
            
            switch(action) {
                case 'increase':
                    if (this.quantity < this.maxStock) newQuantity++;
                    break;
                case 'decrease':
                    if (this.quantity > 1) newQuantity--;
                    break;
                case 'input':
                    newQuantity = Math.min(Math.max(1, this.quantity), this.maxStock);
                    break;
            }

            if (newQuantity === this.quantity) return;
            this.quantity = newQuantity;

            if (this.inCart) {
                this.updateCartQuantity(newQuantity);
            }
        },

        updateCartQuantity(quantity) {
            if (this.isLoading) return;
            this.isLoading = true;

            fetch('/IHM/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'update',
                    productId: this.productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.cartCount;
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('حدث خطأ في تحديث السلة', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        removeFromCart() {
            if (this.isLoading) return;
            this.isLoading = true;

            fetch('/IHM/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'remove',
                    productId: this.productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.inCart = false;
                    this.quantity = 1;
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.cartCount;
                    });
                    showToast('تم إزالة المنتج من السلة', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('حدث خطأ في إزالة المنتج', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        },

        addToCart() {
            if (this.isLoading) return;
            this.isLoading = true;

            const data = new URLSearchParams({
                action: 'add',
                productId: this.productId,
                quantity: this.quantity
            });

            fetch('/IHM/api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data,
                credentials: 'same-origin'
            })
            .then(async response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new TypeError("Expected JSON response");
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    this.inCart = true;
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.cartCount;
                    });
                    showToast('تمت إضافة المنتج إلى السلة', 'success');
                } else {
                    throw new Error(data.message || 'Failed to add to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'حدث خطأ في الاتصال', 'error');
            })
            .finally(() => {
                this.isLoading = false;
            });
        }
    }));
});

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 p-4 rounded-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } shadow-lg z-50 transition-opacity duration-300`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Fade out and remove after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
