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
    <link rel="icon" href="/IHM/public/images/favicon/icon.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <title>منتجاتنا - متجر المواد الغذائية</title>
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

        <!-- Latest Products Section with Swiper -->
        <section class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-green-900 border-r-4 border-green-600 pr-4">
                    أحدث المنتجات
                </h2>
                <div class="flex items-center gap-4">
                    <!-- Custom navigation buttons -->
                    <button class="swiper-custom-prev bg-gray-100 border border-gray-400/35  text-slate-400 rounded-full p-2 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>

                    </button>
                    <button class="swiper-custom-next bg-gray-100 border border-gray-400/35  text-slate-400 rounded-full p-2 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                </div>
            </div>
            <div class="swiper relative latestProductsSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($latestProducts as $product): ?>
                        <div class="swiper-slide">
                            <?php include 'product_card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
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
                                    selected === 'price-desc' ? 'السعر: الأعلى إلى الأقل' :
                                    'الترتيب حسب'"></span>
                        <svg class="w-5 h-5 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            :class="{'rotate-180': open}">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 w-64 mt-2 bg-white border rounded-lg shadow-lg z-50">
                        <div class="py-1">
                            <a href="#" @click.prevent="selected = 'newest'; sortProducts('newest')" 
                                class="block px-4 py-2 hover:bg-gray-100 cursor-pointer">الأحدث</a>
                            <a href="#" @click.prevent="selected = 'price-asc'; sortProducts('price-asc')" 
                                class="block px-4 py-2 hover:bg-gray-100 cursor-pointer">السعر: الأقل إلى الأعلى</a>
                            <a href="#" @click.prevent="selected = 'price-desc'; sortProducts('price-desc')" 
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

    <!-- Add Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div id="modalContent" class="bg-white p-6 rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto relative">
            <!-- Close button -->
            <button onclick="closeProductModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Modal content will be dynamically inserted here -->
        </div>
    </div>

    <script>
        // Global initialization
        document.addEventListener('DOMContentLoaded', function() {
            initializeModal();
            initializeAlpine();
            const swiper = new Swiper('.latestProductsSwiper', {
                slidesPerView: 1,
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-custom-next',
                    prevEl: '.swiper-custom-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 30,
                    },
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
            });

            // Add disabled state handling
            swiper.on('reachBeginning', function() {
                document.querySelector('.swiper-custom-prev').classList.add('opacity-50', 'cursor-not-allowed');
            });
            swiper.on('reachEnd', function() {
                document.querySelector('.swiper-custom-next').classList.add('opacity-50', 'cursor-not-allowed');
            });
            swiper.on('fromEdge', function() {
                document.querySelector('.swiper-custom-prev').classList.remove('opacity-50', 'cursor-not-allowed');
                document.querySelector('.swiper-custom-next').classList.remove('opacity-50', 'cursor-not-allowed');
            });
        });

        function initializeModal() {
            const modal = document.getElementById('productModal');
            if (modal) {
                // Close on overlay click
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeProductModal();
                    }
                });

                // Close on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeProductModal();
                    }
                });
            }
        }

        function initializeAlpine() {
            if (typeof Alpine !== 'undefined') {
                Alpine.data('sortDropdown', () => ({
                    selected: 'newest',
                    updateSort(value) {
                        this.selected = value;
                        // Add your sorting logic here
                    }
                }));
            }
        }

        // Modal functions
        window.showProductDetails = function(product) {
            const modal = document.getElementById('productModal');
            const content = document.getElementById('modalContent');
            if (!modal || !content) return;

            content.innerHTML = `
    <div class="container mx-auto p-6 ">
        <div class="space-y-6">
            <h2 class="text-3xl font-extrabold text-gray-800 border-b pb-3">${product.designation}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                <div class="overflow-hidden h-80 flex items-center justify-center">
                    <img src="/IHM/public/images/products/${product.image}" 
                         alt="${product.designation}"
                         class=" h-[60%]  object-cover transition-transform duration-300 hover:scale-105">
                </div>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">الوصف</h3>
                        <p class="text-gray-600 leading-relaxed">
                            ${product.description || 'لا يوجد وصف متاح'}
                        </p>
                    </div>
                    
                    <div class="flex flex-col gap-4">
                        <div class="text-2xl font-bold text-green-700 self-start">
                            ${formatPrice(product.prix_unitaire, product.promotion)} درهم
                        </div>
                        
                        <div class="flex items-center justify-between bg-gray-100 p-3 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-600">الكمية المتوفرة:</span>
                                <span class="text-green-600 font-bold">
                                    ${product.quantite_stock} ${product.quantite_stock > 1 ? 'قطع' : 'قطعة'}
                                </span>
                            </div>
                            
                            <div class="flex gap-2">
                                <button class="bg-green-600 text-white px-4 py-2 rounded-lg 
                                               hover:bg-green-700 transition-colors 
                                               focus:outline-none focus:ring-2 focus:ring-green-500"
                                        onclick="addToCart(${product.id})">
                                    أضف إلى السلة
                                </button>
                                <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg 
                                               hover:bg-gray-300 transition-colors 
                                               focus:outline-none focus:ring-2 focus:ring-gray-400"
                                        onclick="closeProductModal()">
                                    إغلاق
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">المكونات</h3>
                            <p class="text-gray-600">
                                ${product.ingredients || 'لا يوجد مكونات متاحة'}
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">المعلومات الغذائية</h3>
                            <p class="text-gray-600">
                                ${product.nutritional_info || 'لا توجد معلومات غذائية متاحة'}
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">الحساسيات</h3>
                            <p class="text-gray-600">
                                ${product.allergens || 'لا توجد حساسيات متاحة'}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
`;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        window.closeProductModal = function() {
            const modal = document.getElementById('productModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function formatPrice(price, promotion = 0) {
            if (promotion > 0) {
                return (price * (1 - promotion / 100)).toFixed(2);
            }
            return price.toFixed(2);
        }

        function sortProducts(sortBy) {
            $.ajax({
                url: 'sort_products.php',
                type: 'GET',
                data: { sort: sortBy },
                success: function(response) {
                    $('#productsGrid').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error sorting products:', error);
                }
            });
        }
    </script>

    <style>
        .swiper-pagination-bullet-active {
            background: #166534;
            /* green-900 */
        }

        .swiper {
            padding: 20px 0;
        }

        /* Optional: Add smooth transition for the disabled state */
        .swiper-custom-prev,
        .swiper-custom-next {
            transition: opacity 0.3s ease;
        }
    </style>
</body>

</html>