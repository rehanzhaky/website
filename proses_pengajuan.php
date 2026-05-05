<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_surat = $_POST['nama_surat'];
    $seksi = $_POST['seksi']; 
    $tanggal = $_POST['tanggal'];
    $nama_pengaju = $_POST['nama_pengaju'];
    $diketahui_oleh = $_POST['diketahui_oleh'];
    $keperluan = $_POST['keperluan'];

    // Nomor Surat Otomatis
    $bulan_romawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
    $bulan_ini = $bulan_romawi[date('n')];
    $tahun_ini = date('Y');
    $stmt_cek = $pdo->query("SELECT COUNT(*) FROM pengajuan_inventaris WHERE YEAR(tanggal) = '$tahun_ini'");
    $urutan = $stmt_cek->fetchColumn() + 1;
    $nomor_surat = sprintf("%03d", $urutan) . "/SITAU/" . $bulan_ini . "/" . $tahun_ini;

    // Upload File
    $lampiran_name = "";
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["lampiran"]["name"], PATHINFO_EXTENSION);
        $lampiran_name = time() . "_" . uniqid() . "." . $file_extension;
        move_uploaded_file($_FILES["lampiran"]["tmp_name"], $target_dir . $lampiran_name);
    }

    try {
        $pdo->beginTransaction();

        // 1. Insert ke Tabel Master
        $sql_master = "INSERT INTO pengajuan_inventaris 
                       (nomor_surat, nama_surat, seksi, nama_pengaju, diketahui_oleh, keperluan, lampiran, tanggal, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt_master = $pdo->prepare($sql_master);
        $stmt_master->execute([$nomor_surat, $nama_surat, $seksi, $nama_pengaju, $diketahui_oleh, $keperluan, $lampiran_name, $tanggal]);
        
        $id_pengajuan = $pdo->lastInsertId();

        // 2. Insert ke Tabel Detail (PASTIKAN NAMA TABEL SESUAI GAMBAR KAMU)
        if (isset($_POST['nama_barang']) && is_array($_POST['nama_barang'])) {
            // Sesuai gambar: pengajuan_inventaris_detail
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
        // Pakai redirect meta biar lebih "galak" kalau JS-nya diblokir
        echo "<script>alert('Berhasil!'); window.location.href='daftar_pengajuan.php';</script>";
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Waduh Error Bos: " . $e->getMessage());
    }
}