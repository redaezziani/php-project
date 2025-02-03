<?php
session_start();
require_once '../../Acces_BD/connexion.php';
require_once '../../Acces_BD/produits.php';
require_once '../../Acces_BD/categories.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /IHM/Auth/login.php');
    exit;
}

$categories = getAllCategories();
$products = getAllProducts();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../public/images/products/';
    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageInfo = pathinfo($_FILES['image']['name']);
        $image = uniqid() . '.' . $imageInfo['extension'];
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }

    $productData = [
        'reference' => $_POST['reference'],
        'designation' => $_POST['designation'],
        'description' => $_POST['description'],
        'prix_unitaire' => $_POST['prix_unitaire'],
        'quantite_stock' => $_POST['quantite_stock'],
        'promotion' => $_POST['promotion'],
        'category_id' => $_POST['category_id'],
        'image' => $image,
        'ingredients' => $_POST['ingredients'],
        'nutritional_info' => $_POST['nutritional_info'],
        'allergens' => $_POST['allergens']
    ];

    if (addProduct($productData)) {
        header('Location: products.php?success=1');
        exit;
    }
}
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
    <title>إدارة المنتجات</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">إدارة المنتجات</h1>
            <button onclick="document.getElementById('addProductModal').classList.remove('hidden')"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                إضافة منتج جديد
            </button>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المخزون</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التخفيض</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full object-cover" 
                                         src="/IHM/public/images/products/<?= htmlspecialchars($product['image']) ?>" 
                                         alt="">
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($product['designation']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($product['reference']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= number_format($product['prix_unitaire'], 2) ?> درهم
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= $product['quantite_stock'] ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= $product['promotion'] ?>%
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <button onclick="editProduct(<?= $product['id'] ?>)"
                                        class="text-green-600 hover:text-green-900">تعديل</button>
                                <button onclick="deleteProduct(<?= $product['id'] ?>)"
                                        class="mr-3 text-red-600 hover:text-red-900">حذف</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Product Modal -->
        <div id="addProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
            <div class="relative top-20 mx-auto p-5 border w-[37rem] shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">إضافة منتج جديد</h3>
                    <form
                    
                    class="mt-4 w-full" method="POST" enctype="multipart/form-data">
                        <div class=" w-full gap-x-2  flex justify-between">
                        <div class="mb-4">
                            <input type="text" name="reference" placeholder="المرجع" required
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <input type="text" name="designation" placeholder="اسم المنتج" required
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        </div>
                        <div class="mb-4">
                            <textarea name="description" placeholder="وصف المنتج" 
                                      class="w-full px-3 py-2 border rounded-lg"></textarea>
                        </div>
                        <div class="mb-4">
                            <input type="number" name="prix_unitaire" placeholder="السعر" required step="0.01"
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <input type="number" name="quantite_stock" placeholder="المخزون" required
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <input type="number" name="promotion" placeholder="نسبة التخفيض" min="0" max="100"
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <select name="category_id" required class="w-full px-3 py-2 border rounded-lg">
                                <option value="">اختر الفئة</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>">
                                        <?= htmlspecialchars($category['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <input type="file" name="image" accept="image/*" required
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="mb-4">
                            <textarea name="ingredients" placeholder="المكونات"
                                      class="w-full px-3 py-2 border rounded-lg"></textarea>
                        </div>
                        <div class="mb-4">
                            <textarea name="nutritional_info" placeholder="المعلومات الغذائية"
                                      class="w-full px-3 py-2 border rounded-lg"></textarea>
                        </div>
                        <div class="mb-4">
                            <textarea name="allergens" placeholder="مسببات الحساسية"
                                      class="w-full px-3 py-2 border rounded-lg"></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                    onclick="document.getElementById('addProductModal').classList.add('hidden')"
                                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                إلغاء
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                إضافة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteProduct(id) {
            if (confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
                fetch('/api/products.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            }
        }

        function editProduct(id) {
            // Implement edit functionality
        }
    </script>
</body>
</html>
