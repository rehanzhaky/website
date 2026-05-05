<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

$id_pengajuan = $_GET['id'] ?? null;
$aksi         = $_GET['aksi'] ?? null;

if ($id_pengajuan && $aksi) {
    $status_baru = ($aksi === 'terima') ? 'approve' : 'reject';

    $sql = "UPDATE pengajuan_inventaris SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status_baru, $id_pengajuan]);
}

header("Location: daftar_pengajuan.php");
exit;
?>