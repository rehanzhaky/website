<?php
session_start();
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {

        // Bersihkan sisa data session lama dan regenerate ID supaya tiap login dapat session segar
        $_SESSION = [];
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['seksi'] = $user['seksi'];

        header("Location: index.php");
        exit;
    } else {
        // Kalau gagal, lempar balik ke form login
        $_SESSION['error'] = "Username atau kata sandi tidak sesuai!";
        header("Location: login.php");
        exit;
    }
}
?>