<?php
session_start();
require_once 'config/koneksi.php';

$role = strtolower($_SESSION['role'] ?? '');
$akses_admin = ['admin_utama', 'tu_kepegawaian', 'tu_keuangan', 'tu_umum']; 

if (!isset($_SESSION['user_id']) || !in_array($role, $akses_admin)) {
    echo "<script>alert('Waduh! Anda tidak memiliki hak akses.'); window.location.href='daftar_pengajuan.php';</script>";
    exit;
}

$id = $_GET['id'] ?? 0;
$aksi = strtolower($_GET['aksi'] ?? '');

if ($aksi === 'terima') {
    $status_baru = 'approve';
    $pesan = 'Mantap! Pengajuan berhasil di-ACC. ✅';
} elseif ($aksi === 'tolak') {
    $status_baru = 'reject';
    $pesan = 'Pengajuan resmi DITOLAK. ❌';
} else {
    echo "<script>alert('Aksi tidak dikenali sistem!'); window.location.href='daftar_pengajuan.php';</script>";
    exit;
}

try {
    $sql = "UPDATE pengajuan_inventaris SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$status_baru, $id])) {
        echo "<script>alert('$pesan'); window.location.href='daftar_pengajuan.php';</script>";
    }
} catch (Exception $e) {
    die("Waduh, Error Database Bos: " . $e->getMessage());
}
?>