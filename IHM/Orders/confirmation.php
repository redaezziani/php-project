<?php
session_start();
require_once '../../Acces_BD/commandes.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = getOrder($orderId);

if (!$order || $order['user_id'] !== $_SESSION['user']['id']) {
    header('Location: /IHM/Produits/index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/IHM/public/style/main.css">
    <title>تأكيد الطلب - متجر المواد الغذائية</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>
    <?php include '../public/nav_barre.php'; ?>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-6 max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تم تأكيد طلبك بنجاح!</h1>
                <p class="text-gray-600">رقم الطلب: #<?= $orderId ?></p>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <p class="text-center text-gray-600 mb-6">
                    شكراً لك على طلبك. سنقوم بمعالجته في أقرب وقت ممكن.
                </p>
                
                <div class="flex justify-center space-x-4 space-x-reverse">
                    <a href="/IHM/Orders/index.php" 
                       class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        متابعة طلباتي
                    </a>
                    <a href="/IHM/Produits/index.php" 
                       class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        العودة للتسوق
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include '../public/footer.php'; ?>
</body>
</html>
