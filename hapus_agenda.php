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
    $stmt = $pdo->prepare("SELECT dokumentasi, dokumentasi_png, dokumentasi_pdf FROM agenda_kegiatan WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if ($row) {
        // Hapus file dokumentasi lama jika ada
        $file_path = "uploads/agenda/" . $row['dokumentasi'];
        if (!empty($row['dokumentasi']) && file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Hapus file PNG jika ada
        $file_png = "uploads/agenda/" . $row['dokumentasi_png'];
        if (!empty($row['dokumentasi_png']) && file_exists($file_png)) {
            unlink($file_png);
        }
        
        // Hapus file PDF jika ada
        $file_pdf = "uploads/agenda/" . $row['dokumentasi_pdf'];
        if (!empty($row['dokumentasi_pdf']) && file_exists($file_pdf)) {
            unlink($file_pdf);
        }

        // 2. Hapus data dari database
        $del = $pdo->prepare("DELETE FROM agenda_kegiatan WHERE id = ?");
        if ($del->execute([$id])) {
            header("Location: agenda_kegiatan.php");
            exit;
        } else {
            header("Location: agenda_kegiatan.php");
            exit;
        }
    }
} else {
    header("Location: agenda_kegiatan.php");
}