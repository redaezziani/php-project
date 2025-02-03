<?php
require_once '../../Acces_BD/connexion.php';
require_once '../../Acces_BD/produits.php';
session_start();

$promotionProducts = getPromotionProducts(4);
$latestProducts = getLatestProducts(10);
$cheapestProducts = getCheapestProducts(4);
$allProducts = getAllProducts();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/IHM/public/style/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>Nos Produits - Magasin Alimentaire</title>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <?php include '../public/header.php'; ?>
    <?php include '../public/nav_barre.php'; ?>
    
    <main class="container mx-auto px-4 py-8 flex-grow">
        <!-- Promotion Products Section -->
        <?php if (!empty($promotionProducts)): ?>
            <section class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-green-900 border-r-4 border-green-600 pr-4">
                        العروض المميزة
                    </h2>
                    <a href="?filter=promotions" class="text-green-600 hover:underline">عرض الكل</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <?php foreach ($promotionProducts as $product): ?>
                        <?php include 'product_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Latest Products Section -->
        <section class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-green-900 border-r-4 border-green-600 pr-4">
                    أحدث المنتجات
                </h2>
                <a href="?filter=latest" class="text-green-600 hover:underline">عرض الكل</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <?php foreach ($latestProducts as $product): ?>
                    <?php include 'product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Cheapest Products Section -->
        <section class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-green-900 border-r-4 border-green-600 pr-4">
                    أقل الأسعار
                </h2>
                <a href="?filter=cheapest" class="text-green-600 hover:underline">عرض الكل</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <?php foreach ($cheapestProducts as $product): ?>
                    <?php include 'product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- All Products Section -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-green-900 border-r-4 border-green-600 pr-4">
                    جميع المنتجات
                </h2>
                <div x-data="{ open: false, selected: 'newest' }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-64 px-4 py-2 bg-white border rounded-lg shadow hover:border-gray-300 focus:outline-none">
                        <span x-text="selected === 'newest' ? 'الأحدث' : 
                                    selected === 'price-asc' ? 'السعر: الأقل إلى الأعلى' : 
                                    'السعر: الأعلى إلى الأقل'"></span>
                        <svg class="w-5 h-5 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                             :class="{'rotate-180': open}">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 w-64 mt-2 bg-white border rounded-lg shadow-lg z-50">
                        <div class="py-1">
                            <a href="#" @click.prevent="selected = 'newest'; open = false" 
                               class="block px-4 py-2 hover:bg-gray-100 cursor-pointer">الأحدث</a>
                            <a href="#" @click.prevent="selected = 'price-asc'; open = false" 
                               class="block px-4 py-2 hover:bg-gray-100 cursor-pointer">السعر: الأقل إلى الأعلى</a>
                            <a href="#" @click.prevent="selected = 'price-desc'; open = false" 
                               class="block px-4 py-2 hover:bg-gray-100 cursor-pointer">السعر: الأعلى إلى الأقل</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <?php foreach ($allProducts as $product): ?>
                    <?php include 'product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include '../public/footer.php'; ?>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                const selected = Alpine.$data.selected;
                if (selected) {
                    $.ajax({
                        url: '/api/products.php',
                        data: { sort: selected },
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                updateProductsGrid(response.products);
                            }
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            let searchTimeout;
            
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val();
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: '/api/search.php',
                            data: { q: query },
                            method: 'GET',
                            success: function(response) {
                                if (response.success) {
                                    updateProductsGrid(response.products);
                                }
                            }
                        });
                    }, 300);
                } else if (query.length === 0) {
                    location.reload();
                }
            });
            
            function updateProductsGrid(products) {
                const grid = $('#productsGrid');
                grid.empty();
                
                products.forEach(product => {
                    const price = product.promotion > 0 
                        ? `<div class="flex items-center space-x-2">
                            <span class="line-through text-gray-500">${Number(product.prix_unitaire).toFixed(2)} DH</span>
                            <span class="text-red-600 font-bold">
                                ${(product.prix_unitaire * (1 - product.promotion/100)).toFixed(2)} DH
                            </span>
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                -${product.promotion}%
                            </span>
                           </div>`
                        : `<p class="text-lg font-bold">${Number(product.prix_unitaire).toFixed(2)} DH</p>`;
                    
                    grid.append(`
                        <div class="bg-white rounded-lg shadow-md p-4">
                            <img src="/IHM/public/images/products/${product.image}" 
                                 alt="${product.designation}"
                                 class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-4">
                                <h3 class="text-xl font-semibold">${product.designation}</h3>
                                ${price}
                                <button onclick="addToCart(${product.id})" 
                                        class="mt-4 w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>
                    `);
                });
            }
        });

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

        function removeFromCart(productId) {
            $.ajax({
                url: '/api/cart.php',
                method: 'POST',
                data: { 
                    action: 'remove',
                    productId: productId
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }

        let updateTimeout;
        function updateCartQuantity(productId, quantity) {
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(() => {
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
                            // Update cart count in nav
                            $('.cart-count').text(response.cartCount);
                            // Update cart total if on cart page
                            if($('.cart-total').length) {
                                $('.cart-total').text(response.cartTotal + ' DH');
                            }
                        } else {
                            alert(response.message);
                            // Reset to previous valid quantity
                            $('#quantity-' + productId).val(response.cartItems[productId] || 1);
                        }
                    }
                });
            }, 300); // Debounce for 300ms
        }

        function showProductDetails(product) {
            const modal = document.getElementById('productModal');
            const content = document.getElementById('modalContent');
            
            content.innerHTML = `
                <h2 class="text-2xl font-bold mb-4">${product.designation}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <img src="/IHM/public/images/products/${product.image}" 
                         alt="${product.designation}"
                         class="w-full h-64 object-cover rounded">
                         
                    <div>
                        <p class="text-gray-600 mb-4">${product.description || 'Aucune description disponible'}</p>
                        
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Ingrédients:</h3>
                            <p class="text-sm">${product.ingredients || 'Information non disponible'}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Informations nutritionnelles:</h3>
                            <p class="text-sm">${product.nutritional_info || 'Information non disponible'}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Allergènes:</h3>
                            <p class="text-sm">${product.allergens || 'Aucun allergène déclaré'}</p>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeProductModal() {
            const modal = document.getElementById('productModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });
    </script>
</body>
</html>
