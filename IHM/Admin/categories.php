<?php
session_start();
require_once '../../Acces_BD/connexion.php';
require_once '../../Acces_BD/categories.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /IHM/Auth/login.php');
    exit;
}

$categories = getAllCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = '../public/images/categories/';
    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageInfo = pathinfo($_FILES['image']['name']);
        $image = uniqid() . '.' . $imageInfo['extension'];
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }

    $categoryData = [
        'nom' => $_POST['nom'],
        'description' => $_POST['description'],
        'image' => $image
    ];

    if (addCategory($categoryData)) {
        header('Location: categories.php?success=1');
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
    <title>إدارة الفئات</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">إدارة الفئات</h1>
            <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                إضافة فئة جديدة
            </button>
        </div>

        <!-- Categories Grid -->
        <div
