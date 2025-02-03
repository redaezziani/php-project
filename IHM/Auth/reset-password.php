<?php
session_start();
require_once '../../Acces_BD/users.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = false;

// Validate token
$user = $token ? validateResetToken($token) : null;
if (!$user) {
    $error = 'رابط إعادة تعيين كلمة المرور غير صالح أو منتهي الصلاحية';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (strlen($password) < 6) {
        $error = 'يجب أن تكون كلمة المرور 6 أحرف على الأقل';
    } elseif ($password !== $password_confirm) {
        $error = 'كلمات المرور غير متطابقة';
    } else {
        try {
            if (resetPassword($token, $password)) {
                $success = true;
            } else {
                $error = 'حدث خطأ أثناء إعادة تعيين كلمة المرور';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
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
    <title>إعادة تعيين كلمة المرور - متجر المواد الغذائية</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold mb-6 text-center text-green-900">إعادة تعيين كلمة المرور</h1>

            <?php if ($success): ?>
                <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
                    <p class="text-right">تم إعادة تعيين كلمة المرور بنجاح.</p>
                    <p class="text-right mt-2">
                        <a href="login.php" class="text-green-700 font-bold hover:underline">
                            اضغط هنا لتسجيل الدخول
                        </a>
                    </p>
                </div>
            <?php elseif ($error): ?>
                <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                    <p class="text-right"><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!$success && $user): ?>
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="password" class="block text-gray-700 mb-2 text-right">كلمة المرور الجديدة</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label for="password_confirm" class="block text-gray-700 mb-2 text-right">تأكيد كلمة المرور</label>
                        <input type="password" 
                               id="password_confirm" 
                               name="password_confirm" 
                               required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200 font-bold">
                        تحديث كلمة المرور
                    </button>
                </form>
            <?php endif; ?>

            <div class="mt-6 text-center">
                <a href="login.php" class="text-green-600 hover:underline font-bold">
                    العودة إلى صفحة تسجيل الدخول
                </a>
            </div>
        </div>
    </main>

    <?php include '../public/footer.php'; ?>
</body>
</html>
