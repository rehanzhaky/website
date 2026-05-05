<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $lokasi        = trim($_POST['lokasi']);
    $tanggal       = $_POST['tanggal'];
    $keterangan    = trim($_POST['keterangan']);
    
    // Urusan Upload Foto Dokumentasi (Opsional)
    $nama_file = "";
    if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] != UPLOAD_ERR_NO_FILE) {
        $file_tmp = $_FILES['dokumentasi']['tmp_name'];
        // Bikin nama file unik biar ga saling timpa
        $nama_file = time() . "_" . $_FILES['dokumentasi']['name'];
        $folder_tujuan = "uploads/agenda/";
        
        // Cek kalau foldernya belum ada, dibikin otomatis
        if (!is_dir($folder_tujuan)) {
            mkdir($folder_tujuan, 0777, true);
        }
        
        move_uploaded_file($file_tmp, $folder_tujuan . $nama_file);
    }

    $sql = "INSERT INTO agenda_kegiatan (nama_kegiatan, lokasi, tanggal, keterangan, dokumentasi) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nama_kegiatan, $lokasi, $tanggal, $keterangan, $nama_file])) {
        header("Location: agenda_kegiatan.php");
        exit;
    } else {
        $error = "Gagal menyimpan agenda kegiatan!";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Tambah Agenda Kegiatan</h2>
        <p>Catat jadwal kegiatan baru Kantor Imigrasi.</p>
    </div>
    <a href="agenda_kegiatan.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali ke Daftar</a>
</div>

<div class="glass panel-utama">
    
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.2); color: #ff4c4c; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- PENTING: enctype="multipart/form-data" wajib kalau ada upload file -->
    <form action="" method="POST" enctype="multipart/form-data">
        
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Detail Kegiatan</h3>
        
        <div class="form-grid">
            <div class="form-group form-full">
                <label>Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" placeholder="Contoh: Rapat Koordinasi Timpora" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" placeholder="Contoh: Ruang Rapat Utama" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Tanggal Kegiatan</label>
                <input type="date" name="tanggal" required>
            </div>

            <div class="form-group form-full">
                <label>Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" placeholder="Tuliskan catatan tambahan (peserta, urgensi, dll)..."></textarea>
            </div>

            <div class="form-group form-full">
                <label>Foto Dokumentasi <span style="color: rgba(255,255,255,0.4); font-size: 10px; font-weight: normal;">(Opsional, format JPG/PNG)</span></label>
                <!-- Pakai style sederhana dulu, kalau mau pakai JS yang ribet kayak di inventaris nanti bisa disesuaikan -->
                <input type="file" name="dokumentasi" accept="image/png, image/jpeg, image/jpg" style="padding: 10px; background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.3); border-radius: 8px; width: 100%; color: white;">
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 200px;">Simpan Agenda</button>
        </div>

    </form>
</div>

<?php include 'layouts/footer.php'; ?>