<?php
header('Location: /IHM/Produits/index.php');
exit();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/IHM/public/style/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Magasin Alimentaire</title>
</head>
<body class="bg-gray-50">
    <?php include 'IHM/public/header.php'; ?>
    <?php include 'IHM/public/nav_barre.php'; ?>
    
    <main class="container mx-auto px-4 py-8">
        <div id="productsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php
            // Load and display products
            require_once 'Acces_BD/produits.php';
            $products = getAllProducts();
            foreach ($products as $product) {
                include 'IHM/Produits/product_card.php';
            }
            ?>
        </div>
    </main>

    <?php include 'IHM/public/footer.php'; ?>

    <script>
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
                    // Reset to show all products
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
    </script>
</body>
</html>
