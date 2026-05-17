<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// Cek Role (Hanya Admin Utama yang boleh hapus)
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
if (!in_array($role_saat_ini, ['admin_utama'])) {
    echo "<script>alert('Akses Ditolak! Anda tidak memiliki izin untuk menghapus pengajuan.'); window.location.href='daftar_pengajuan.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo->beginTransaction();
        
        // 1. Hapus detail terlebih dahulu (child records)
        $stmt_detail = $pdo->prepare("DELETE FROM pengajuan_inventaris_detail WHERE id_pengajuan = ?");
        $stmt_detail->execute([$id]);
        
        // 2. Hapus master (parent record)
        $stmt_master = $pdo->prepare("DELETE FROM pengajuan_inventaris WHERE id = ?");
        $stmt_master->execute([$id]);
        
        $pdo->commit();
        
        header("Location: daftar_pengajuan.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: daftar_pengajuan.php");
        exit;
    }
} else {
    header("Location: daftar_pengajuan.php");
    exit;
}
?>
