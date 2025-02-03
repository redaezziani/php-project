
<header class="bg-green-600 text-white">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between gap-x-3 items-center">
            <h1 class="text-2xl font-bold">متجر المواد الغذائية</h1>
            
            <div class="flex-1 mx-8">
                <input type="text" 
                       id="searchInput" 
                       placeholder="ابحث عن منتج..." 
                       class="w-full flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm text-foreground shadow-sm shadow-black/5 transition-shadow placeholder:text-muted-foreground/70 focus-visible:border-ring focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/20 disabled:cursor-not-allowed disabled:opacity-50">
            </div>
            
            <?php if (isset($_SESSION['user'])): ?>
                <div x-data="{ isOpen: false }" class="relative inline-block">
                    <button @click="isOpen = !isOpen" class="relative z-10 flex items-center p-2 text-white rounded-md focus:outline-none">
                        <span class="mx-1">مرحباً، <?= htmlspecialchars($_SESSION['user']['nom']) ?></span>
                        <span class="mx-1">(<?= htmlspecialchars($_SESSION['user']['points']) ?> نقطة)</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="isOpen" 
                        @click.away="isOpen = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-90"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-90" 
                        class="absolute left-0 z-20 w-48 py-2 mt-2 origin-top-right bg-white rounded-md shadow-xl"
                    >
                        <a href="/IHM/Profile" class="block px-4 py-3 text-sm text-gray-600 text-right hover:bg-gray-100">الملف الشخصي</a>
                        <a href="/IHM/Orders" class="block px-4 py-3 text-sm text-gray-600 text-right hover:bg-gray-100">طلباتي</a>
                        <a href="/IHM/Support" class="block px-4 py-3 text-sm text-gray-600 text-right hover:bg-gray-100">المساعدة</a>
                        <a href="/IHM/Settings" class="block px-4 py-3 text-sm text-gray-600 text-right hover:bg-gray-100">الإعدادات</a>
                        <a href="/IHM/Auth/logout.php" class="block px-4 py-3 text-sm text-red-600 text-right hover:bg-gray-100">تسجيل خروج</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="space-x-4 md:flex gap-x-3 hidden">
                    <a href="/IHM/Auth/login.php" class="text-amber-200 font-bold underline">تسجيل دخول</a>
                    <a href="/IHM/Auth/register.php" class="text-amber-200 font-bold underline">إنشاء حساب</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Search Overlay -->
<div id="searchOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="container mx-auto px-4 pt-20">
        <div class="relative max-w-2xl mx-auto">
            <!-- Close button -->
            <button onclick="closeSearchOverlay()" class="absolute -top-10 right-0 text-white hover:text-amber-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
            <!-- Search input -->
            <div class="bg-white rounded-lg shadow-xl">
                <div class="p-4 border-b">
                    <input type="text" 
                           id="overlaySearchInput" 
                           class="w-full text-lg p-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-500" 
                           placeholder="ابحث عن المنتجات..."
                           dir="rtl">
                </div>
                
                <!-- Search results -->
                <div id="searchResults" class="max-h-96 overflow-y-auto p-4">
                    <!-- Results will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>



<script>
function openSearchOverlay() {
    document.getElementById('searchOverlay').classList.remove('hidden');
    document.getElementById('overlaySearchInput').focus();
    document.body.style.overflow = 'hidden';
}

function closeSearchOverlay() {
    document.getElementById('searchOverlay').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Add event listener for the search input
document.getElementById('overlaySearchInput').addEventListener('input', function(e) {
    const query = e.target.value;
    if (query.length >= 2) {
        searchProducts(query);
    } else {
        document.getElementById('searchResults').innerHTML = '';
    }
});

function searchProducts(query) {
    $.ajax({
        url: '/IHM/api/search_products.php',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            const results = JSON.parse(response);
            displaySearchResults(results);
        }
    });
}

function displaySearchResults(results) {
    const resultsContainer = document.getElementById('searchResults');
    
    if (results.length === 0) {
        resultsContainer.innerHTML = '<p class="text-center text-gray-500 py-4">لا توجد نتائج</p>';
        return;
    }
    
    let html = '';
    results.forEach(product => {
        const price = product.promotion > 0 
            ? `<div class="flex flex-col">
                 <span class="line-through text-gray-400 text-sm">${product.prix_unitaire} درهم</span>
                 <span class="text-red-600 font-bold">${(product.prix_unitaire * (1 - product.promotion/100)).toFixed(2)} درهم</span>
               </div>`
            : `<span class="text-green-900 font-bold">${product.prix_unitaire} درهم</span>`;
            
        html += `
            <a href="/IHM/Produits/details.php?id=${product.id}" class="flex items-center p-2 hover:bg-gray-50 rounded-lg">
                <img src="/IHM/public/images/products/${product.image}" 
                     alt="${product.designation}"
                     class="w-16 h-16 object-cover rounded">
                <div class="mr-4 flex-grow">
                    <h3 class="font-medium text-green-900">${product.designation}</h3>
                    ${price}
                </div>
            </a>
        `;
    });
    
    resultsContainer.innerHTML = html;
}

// Close overlay when clicking outside
document.getElementById('searchOverlay').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSearchOverlay();
    }
});

// Close overlay when pressing ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSearchOverlay();
    }
});
</script>
