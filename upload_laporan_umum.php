<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$slug_seksi = $_GET['seksi'] ?? '';
$nama_seksi = str_replace('_', ' ', $slug_seksi);

if (empty($slug_seksi)) { 
    header("Location: laporan_umum.php"); 
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul_laporan'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $seksi = $_POST['seksi'];
    $keterangan = $_POST['keterangan'];

    $nama_file_asli = $_FILES['file_laporan']['name'];
    $tmp_file = $_FILES['file_laporan']['tmp_name'];
    $path_folder = 'uploads/laporan_umum/';
    
    $nama_file_baru = time() . '_' . str_replace(' ', '_', $nama_file_asli);

    if (move_uploaded_file($tmp_file, $path_folder . $nama_file_baru)) {
        $sql = "INSERT INTO laporan_umum (seksi, bulan, tahun, judul_laporan, nama_file, tanggal_upload, keterangan) 
                VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$seksi, $bulan, $tahun, $judul, $nama_file_baru, $keterangan]);

        echo "<script>alert('Laporan Bulan $bulan Tahun $tahun Berhasil!'); window.location.href='daftar_laporan_umum.php?seksi=$slug_seksi';</script>";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Upload Laporan 📤</h2>
    <p>Laporan Kinerja <strong>Seksi <?= $nama_seksi ?></strong></p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label>Seksi</label>
                <input type="text" name="seksi" value="<?= $nama_seksi ?>" readonly style="background: var(--bg-secondary); color: #072749; font-weight: bold;">
            </div>

            <div class="form-group">
                <label>Judul Laporan</label>
                <input type="text" name="judul_laporan" placeholder="Contoh: Laporan Capaian Kinerja" required>
            </div>

            <div class="form-group">
                <label>Periode Bulan</label>
                <select name="bulan" required style="width: 100%; padding: 10px; border-radius: 8px; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color);">
                    <?php
                    $list_bulan = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                    foreach ($list_bulan as $num => $nama):
                        $selected = ($num == date('n')) ? 'selected' : '';
                        echo "<option value='$num' $selected style='color:black;'>$nama</option>";
                    endforeach;
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tahun</label>
                <select name="tahun" required style="width: 100%; padding: 10px; border-radius: 8px; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color);">
                    <option value="<?= date('Y') ?>" style="color:black;"><?= date('Y') ?></option>
                    <option value="<?= date('Y')-1 ?>" style="color:black;"><?= date('Y')-1 ?></option>
                </select>
            </div>

            <div class="form-group form-full">
                <label>File Dokumen</label>
                <input type="file" name="file_laporan" required style="padding: 8px; background: var(--bg-secondary); border: 1px dashed var(--border-color); width: 100%; border-radius: 8px; color: var(--text-primary);">
            </div>

            <div class="form-group form-full">
                <label>Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" style="width: 100%; padding: 12px; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-primary);"></textarea></textarea>
            </div>
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <a href="laporan_umum.php" style="color: var(--text-secondary); text-decoration: none; margin-right: 20px;">Batal</a>
            <button type="submit" class="btn-navy-pill" style="background: rgba(255, 215, 0, 0.2); border-color: #ffd700; color: #ffd700;">💾 Simpan & Upload</button>
        </div>
    </form>
</div>  