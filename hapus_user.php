<?php
session_start();
require_once 'config/koneksi.php';

// Proteksi halaman: Harus Login & Role harus admin/admin_utama
if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

// Tangkap ID yang dilempar dari tombol "Hapus" di tabel
$id_user = $_GET['id'] ?? null;

if ($id_user) {
    // FITUR KEAMANAN: Cegah admin menghapus akunnya sendiri yang lagi dipakai!
    if ($id_user == $_SESSION['user_id']) {
        echo "<script>
            alert('Waduh bos! Masa mau hapus akun sendiri? Nanti malah ga bisa masuk sistem lagi lho!');
            window.location.href = 'kelola_pengguna.php';
        </script>";
        exit;
    }

    // Kalau aman (bukan akun sendiri), langsung eksekusi tebas dari database
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id_user]);
}

// Lempar balik ke tabel kelola pengguna
header("Location: kelola_pengguna.php");
exit;
?>