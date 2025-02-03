<?php if (!empty($_SESSION['cart'])): ?>
    <div class="border-t border-gray-200 p-4">
        <div class="flex justify-between text-base font-medium text-gray-900 mb-3">
            <p>المجموع</p>
            <p class="cart-total"><?= number_format($total, 2) ?> درهم</p>
        </div>
        
        <div class="space-y-2">
            <a href="/IHM/Checkout"
               class="flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700">
                إتمام الطلب
            </a>
            
            <button @click="isCartOpen = false"
                    class="flex justify-center items-center px-6 py-3 w-full border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">
                متابعة التسوق
            </button>
        </div>
    </div>
<?php endif; ?>
