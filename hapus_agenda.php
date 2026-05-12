<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// Cek Role (Cuma Admin yang boleh hapus)
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
if (!in_array($role_saat_ini, ['admin_utama', 'tu_keuangan', 'admin'])) {
    echo "<script>alert('Akses Ditolak!'); window.location.href='agenda_kegiatan.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    // 1. Ambil nama file foto dulu buat dihapus dari server
    $stmt = $pdo->prepare("SELECT dokumentasi FROM agenda_kegiatan WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        $file_path = "uploads/agenda/" . $row['dokumentasi'];
        if (!empty($row['dokumentasi']) && file_exists($file_path)) {
            unlink($file_path); // Hapus foto dari folder
        }

        // 2. Hapus data dari database
        $del = $pdo->prepare("DELETE FROM agenda_kegiatan WHERE id = ?");
        if ($del->execute([$id])) {
            echo "<script>alert('Agenda berhasil dihapus! 🗑️'); window.location.href='agenda_kegiatan.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data.'); window.location.href='agenda_kegiatan.php';</script>";
        }
    }
} else {
    header("Location: agenda_kegiatan.php");
}