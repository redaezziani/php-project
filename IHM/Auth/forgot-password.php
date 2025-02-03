<?php
session_start();
require_once '../../Acces_BD/users.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    try {
        $token = generateResetToken($email);
        if ($token) {
            // In a real application, you would send an email here
            // For demonstration, we'll just show the reset link
            $resetLink = "http://{$_SERVER['HTTP_HOST']}/IHM/Auth/reset-password.php?token=" . $token;
            $message = "رابط إعادة تعيين كلمة المرور تم إرساله إلى بريدك الإلكتروني.";
            
            // For development purposes only:
            $message .= "<br>رابط إعادة التعيين: <a href='$resetLink'>$resetLink</a>";
        } else {
            $error = 'البريد الإلكتروني غير مسجل في النظام';
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
    <title>نسيت كلمة المرور - متجر المواد الغذائية</title>
</head>
<body class="bg-gray-50">
    <?php include '../public/header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold mb-6 text-center text-green-900">استعادة كلمة المرور</h1>

            <?php if ($message): ?>
                <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
                    <p class="text-right"><?= $message ?></p>
                </div>
            <?php endif; ?>

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

                <button type="submit" 
                        class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200 font-bold">
                    إرسال رابط إعادة التعيين
                </button>
            </form>

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
