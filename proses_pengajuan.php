<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cuma nangkap data yang bener-bener ada di form sekarang
    $seksi = $_POST['seksi']; 
    $tanggal = $_POST['tanggal'];
    $diketahui_oleh = $_POST['diketahui_oleh'];

    $lampiran_name = "";
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["lampiran"]["name"], PATHINFO_EXTENSION);
        $lampiran_name = time() . "_" . uniqid() . "." . $file_extension;
        move_uploaded_file($_FILES["lampiran"]["tmp_name"], $target_dir . $lampiran_name);
    }

    try {
        $pdo->beginTransaction();

        // Auto-isi field NOT NULL legacy dari sesi & data yg ada
        $nama_pengaju = $_SESSION['nama_lengkap'] ?? ($_SESSION['username'] ?? 'User');
        $nama_surat   = "Pengajuan Inventaris - " . $seksi . " - " . date('d/m/Y', strtotime($tanggal));

        $sql_master = "INSERT INTO pengajuan_inventaris
                       (seksi, nama_surat, nama_pengaju, diketahui_oleh, lampiran, tanggal, status)
                       VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt_master = $pdo->prepare($sql_master);
        $stmt_master->execute([$seksi, $nama_surat, $nama_pengaju, $diketahui_oleh, $lampiran_name, $tanggal]);
        
        $id_pengajuan = $pdo->lastInsertId();

        if (isset($_POST['nama_barang']) && is_array($_POST['nama_barang'])) {
            $sql_detail = "INSERT INTO pengajuan_inventaris_detail (id_pengajuan, nama_barang, jumlah, satuan, keterangan) VALUES (?, ?, ?, ?, ?)";
            $stmt_detail = $pdo->prepare($sql_detail);

            foreach ($_POST['nama_barang'] as $key => $val) {
                if (!empty(trim($val))) {
                    $stmt_detail->execute([
                        $id_pengajuan,
                        $val,
                        $_POST['jumlah'][$key],
                        $_POST['satuan'][$key],
                        $_POST['keterangan'][$key]
                    ]);
                }
            }
        }

        $pdo->commit();
        echo "<script>alert('Berhasil!'); window.location.href='daftar_pengajuan.php';</script>";
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Waduh Error Bos: " . $e->getMessage());
    }
}
?>