<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// Cek Role (Hanya Admin Utama & TU Keuangan yang boleh hapus)
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
if (!in_array($role_saat_ini, ['admin_utama', 'tu_keuangan'])) {
    echo "<script>alert('Akses Ditolak! Anda tidak memiliki izin untuk menghapus laporan.'); window.location.href='realisasi_anggaran.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo->beginTransaction();
        
        // 1. Hapus detail terlebih dahulu (child records)
        $stmt_detail = $pdo->prepare("DELETE FROM laporan_realisasi_bidang_detail WHERE id_laporan = ?");
        $stmt_detail->execute([$id]);
        
        // 2. Hapus master (parent record)
        $stmt_master = $pdo->prepare("DELETE FROM laporan_realisasi_bidang WHERE id = ?");
        $stmt_master->execute([$id]);
        
        $pdo->commit();
        
        header("Location: realisasi_anggaran.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: realisasi_anggaran.php");
        exit;
    }
} else {
    header("Location: realisasi_anggaran.php");
    exit;
}
?>
