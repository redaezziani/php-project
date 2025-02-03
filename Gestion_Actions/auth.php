<?php
session_start();
require_once '../Acces_BD/connexion.php';

if (isset($_POST['login'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $conn = Connect();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nom' => $user['nom'],
            'email' => $user['email'],
            'role' => $user['role'],
            'points' => $user['points']
        ];
        header('Location: ../index.php');
    } else {
        header('Location: ../IHM/Auth/login.php?error=1');
    }
}

if (isset($_POST['register'])) {
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $conn = Connect();
    $stmt = $conn->prepare("
        INSERT INTO users (nom, email, telephone, adresse, password, role, points)
        VALUES (?, ?, ?, ?, ?, 'client', 0)
    ");
    
    if ($stmt->execute([$nom, $email, $telephone, $adresse, $password])) {
        header('Location: ../IHM/Auth/login.php?registered=1');
    } else {
        header('Location: ../IHM/Auth/register.php?error=1');
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../index.php');
}
