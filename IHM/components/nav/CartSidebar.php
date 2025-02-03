<div x-data="{ isCartOpen: false }">
    <!-- Cart Button -->
    <button @click="isCartOpen = true" class="relative p-2 hover:text-amber-200">
    <svg
    class="size-6"
    width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
 <path d="M5.00014 14H18.1359C19.1487 14 19.6551 14 20.0582 13.8112C20.4134 13.6448 20.7118 13.3777 20.9163 13.0432C21.1485 12.6633 21.2044 12.16 21.3163 11.1534L21.9013 5.88835C21.9355 5.58088 21.9525 5.42715 21.9031 5.30816C21.8597 5.20366 21.7821 5.11697 21.683 5.06228C21.5702 5 21.4155 5 21.1062 5H4.50014M2 2H3.24844C3.51306 2 3.64537 2 3.74889 2.05032C3.84002 2.09463 3.91554 2.16557 3.96544 2.25376C4.02212 2.35394 4.03037 2.48599 4.04688 2.7501L4.95312 17.2499C4.96963 17.514 4.97788 17.6461 5.03456 17.7462C5.08446 17.8344 5.15998 17.9054 5.25111 17.9497C5.35463 18 5.48694 18 5.75156 18H19M7.5 21.5H7.51M16.5 21.5H16.51M8 21.5C8 21.7761 7.77614 22 7.5 22C7.22386 22 7 21.7761 7 21.5C7 21.2239 7.22386 21 7.5 21C7.77614 21 8 21.2239 8 21.5ZM17 21.5C17 21.7761 16.7761 22 16.5 22C16.2239 22 16 21.7761 16 21.5C16 21.2239 16.2239 21 16.5 21C16.7761 21 17 21.2239 17 21.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
 </svg>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs">
            <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>
        </span>
    </button>

    <!-- Cart Sidebar -->
    <div x-show="isCartOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 overflow-hidden z-50"
         @click.away="isCartOpen = false"
         @keydown.escape.window="isCartOpen = false">
        
        <div class="absolute inset-0 overflow-hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="fixed inset-y-0 left-0 max-w-full flex">
                <div class="w-screen max-w-md transform transition ease-in-out duration-500"
                     x-transition:enter="transform transition ease-in-out duration-500"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-500"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">
                    
                    <div class="h-full flex flex-col bg-white shadow-xl">
                        <!-- Cart Header -->
                        <div class="flex items-center justify-between px-4 py-6 bg-gray-50">
                            <h2 class="text-lg font-medium text-gray-900">سلة التسوق</h2>
                            <button @click="isCartOpen = false" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Cart Content -->
                        <div class="flex-1 overflow-y-auto">
                            <div class="px-4 py-6">
                                <?php 
                                require_once $_SERVER['DOCUMENT_ROOT'].'/Acces_BD/produits.php';
                                include 'CartItems.php'; 
                                ?>
                            </div>
                        </div>

                        <!-- Cart Footer -->
                        <?php include 'CartFooter.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this script at the end of CartSidebar.php -->
<script>
function updateQuantity(productId, action, value = null) {
    const input = document.getElementById(`quantity-${productId}`);
    const currentQty = parseInt(input.value);
    const maxQty = parseInt(input.max);
    let newQty;

    switch(action) {
        case 'increase':
            newQty = Math.min(currentQty + 1, maxQty);
            break;
        case 'decrease':
            newQty = Math.max(currentQty - 1, 1);
            break;
        case 'input':
            newQty = Math.max(1, Math.min(parseInt(value), maxQty));
            break;
        default:
            return;
    }

    if (newQty !== currentQty) {
        input.value = newQty;
        
        // Update subtotal immediately
        const itemContainer = document.getElementById(`cart-item-${productId}`);
        const subtotalElement = itemContainer.querySelector('.item-subtotal');
        const price = parseFloat(subtotalElement.dataset.price);
        const newSubtotal = price * newQty;
        subtotalElement.textContent = newSubtotal.toFixed(2);
        
        // Update server-side cart
        updateCartQuantity(productId, newQty);
    }
}

function updateCartQuantity(productId, quantity) {
    $.ajax({
        url: '/IHM/api/cart.php',
        method: 'POST',
        data: {
            action: 'update',
            productId: productId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                // Update cart count in header
                document.querySelectorAll('.cart-count').forEach(el => {
                    el.textContent = response.cartCount;
                });
                
                // Update cart total
                document.querySelectorAll('.cart-total').forEach(el => {
                    el.textContent = `${response.cartTotal} درهم`;
                });
                
                // Optional: Show success message
                showToast('تم تحديث السلة بنجاح', 'success');
            } else {
                showToast(response.message || 'حدث خطأ ما', 'error');
            }
        },
        error: function() {
            showToast('حدث خطأ في الاتصال', 'error');
        }
    });
}

function removeFromCart(productId) {
    const cartItem = document.getElementById(`cart-item-${productId}`);
    
    $.ajax({
        url: '/IHM/api/cart.php',
        method: 'POST',
        data: {
            action: 'remove',
            productId: productId
        },
        success: function(response) {
            if (response.success) {
                // Fade out the item
                cartItem.style.transition = 'all 0.3s ease-out';
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    cartItem.remove();
                    
                    // Update cart counts
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = response.cartCount;
                    });
                    
                    // Update cart total
                    document.querySelectorAll('.cart-total').forEach(el => {
                        el.textContent = `${response.cartTotal} درهم`;
                    });
                    
                    // Check if cart is empty
                    if (response.cartCount === 0) {
                        const cartContent = document.querySelector('.cart-items-container');
                        cartContent.innerHTML = `
                            <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-lg font-medium mb-2">سلة التسوق فارغة</p>
                                <a href="/IHM/Produits" class="text-green-600 hover:text-green-700">
                                    تصفح المنتجات
                                </a>
                            </div>
                        `;
                    }
                    
                    showToast('تم إزالة المنتج من السلة', 'success');
                }, 300);
            } else {
                showToast(response.message || 'حدث خطأ ما', 'error');
            }
        },
        error: function() {
            showToast('حدث خطأ في الاتصال', 'error');
        }
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white z-50 transition-opacity duration-500`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
</script>
