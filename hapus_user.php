<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

$id_user = $_GET['id'] ?? null;

if ($id_user) {
    if ($id_user == $_SESSION['user_id']) {
        echo "<script>
            alert('Waduh bos! Masa mau hapus akun sendiri? Nanti malah ga bisa masuk sistem lagi lho!');
            window.location.href = 'kelola_pengguna.php';
        </script>";
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id_user]);
}

header("Location: kelola_pengguna.php");
exit;
?>