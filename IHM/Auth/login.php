<?php
session_start();
require_once '../../Acces_BD/users.php';

if (isset($_SESSION['user'])) {
    header('Location: /IHM/Produits/index.php');
    exit();
}

$error = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $user = loginUser($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            $redirectUrl = $redirect ? "/IHM/Orders/{$redirect}.php" : '/IHM/Produits/index.php';
            header("Location: $redirectUrl");
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
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
    <title>تسجيل الدخول - متجر المواد الغذائية</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold mb-6 text-center text-green-900">تسجيل الدخول</h1>

            <?php if ($error): ?>
                <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                    <p class="text-right"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-gray-700 mb-2 text-right">البريد الإلكتروني</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           dir="ltr"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 mb-2 text-right">كلمة المرور</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200 font-bold">
                    تسجيل الدخول
                </button>
            </form>

            <div class="mt-6 text-center space-y-4">
                <p class="text-gray-600">
                    ليس لديك حساب؟ 
                    <a href="register.php" class="text-green-600 hover:underline font-bold">
                        إنشاء حساب جديد
                    </a>
                </p>
                <a href="/IHM/Auth/forgot-password.php" class="text-gray-500 hover:text-gray-700 block text-sm">
                    نسيت كلمة المرور؟
                </a>
            </div>
        </div>
    </main>

    <?php include '../public/footer.php'; ?>
</body>
</html>
