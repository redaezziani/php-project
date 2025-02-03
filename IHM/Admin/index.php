<?php
session_start();
require_once '../../Acces_BD/connexion.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /IHM/Auth/login.php');
    exit;
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
    <title>لوحة التحكم - المدير</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">لوحة التحكم</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="products.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-2">إدارة المنتجات</h2>
                <p class="text-gray-600">إضافة، تعديل، وحذف المنتجات</p>
            </a>
            
            <a href="categories.php" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-2">إدارة الفئات</h2>
                <p class="text-gray-600">إدارة فئات المنتجات</p>
            </a>
        </div>
    </div>
</body>
</html>
