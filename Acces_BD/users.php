<?php
require_once 'connexion.php';

function loginUser($email, $password) {
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']); // Don't include password in session
        return $user;
    }
    return false;
}

function updateUserPoints($userId, $points) {
    $conn = Connect();
    $stmt = $conn->prepare("
        UPDATE users 
        SET points = points + ? 
        WHERE id = ?
    ");
    return $stmt->execute([$points, $userId]);
}

function getUserById($id) {
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        unset($user['password']);
    }
    return $user;
}

function generateResetToken($email) {
    $conn = Connect();
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
    $stmt->execute([$token, $expires, $email]);
    
    return $conn->rowCount() > 0 ? $token : false;
}

function validateResetToken($token) {
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function resetPassword($token, $newPassword) {
    $conn = Connect();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("
        UPDATE users 
        SET password = ?, reset_token = NULL, reset_token_expires = NULL 
        WHERE reset_token = ? AND reset_token_expires > NOW()
    ");
    
    $stmt->execute([$hashedPassword, $token]);
    return $stmt->rowCount() > 0;
}
