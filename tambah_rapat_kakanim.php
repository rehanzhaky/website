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
    
    $nama_file_png = "";
    $nama_file_pdf = "";
    
    // Upload PNG
    if (isset($_FILES['dokumentasi_png']) && $_FILES['dokumentasi_png']['error'] != UPLOAD_ERR_NO_FILE) {
        $file_tmp = $_FILES['dokumentasi_png']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['dokumentasi_png']['name'], PATHINFO_EXTENSION));
        
        if ($file_ext == 'png') {
            $nama_file_png = time() . "_rapat_" . uniqid() . ".png";
            $folder_tujuan = "uploads/agenda/";
            
            if (!is_dir($folder_tujuan)) {
                mkdir($folder_tujuan, 0777, true);
            }
            
            move_uploaded_file($file_tmp, $folder_tujuan . $nama_file_png);
        } else {
            $error = "File PNG harus berformat .png!";
        }
    }
    
    // Upload PDF
    if (isset($_FILES['dokumentasi_pdf']) && $_FILES['dokumentasi_pdf']['error'] != UPLOAD_ERR_NO_FILE) {
        $file_tmp = $_FILES['dokumentasi_pdf']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['dokumentasi_pdf']['name'], PATHINFO_EXTENSION));
        
        if ($file_ext == 'pdf') {
            $nama_file_pdf = time() . "_rapat_" . uniqid() . ".pdf";
            $folder_tujuan = "uploads/agenda/";
            
            if (!is_dir($folder_tujuan)) {
                mkdir($folder_tujuan, 0777, true);
            }
            
            move_uploaded_file($file_tmp, $folder_tujuan . $nama_file_pdf);
        } else {
            $error = "File PDF harus berformat .pdf!";
        }
    }

    if (!isset($error)) {
        $sql = "INSERT INTO agenda_kegiatan (nama_kegiatan, lokasi, tanggal, keterangan, dokumentasi_png, dokumentasi_pdf, jenis_agenda) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$nama_kegiatan, $lokasi, $tanggal, $keterangan, $nama_file_png, $nama_file_pdf, 'rapat_kakanim'])) {
            header("Location: agenda_kegiatan.php");
            exit;
        } else {
            $error = "Gagal menyimpan agenda rapat!";
        }
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>📋 Tambah Rapat Kakanim</h2>
        <p>Catat rapat Kepala Kantor Imigrasi dengan dokumentasi PNG & PDF.</p>
    </div>
    <a href="pilih_agenda.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali</a>
</div>

<div class="glass panel-utama">
    
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.2); color: #ff4c4c; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Detail Rapat</h3>
        
        <div class="form-grid">
            <div class="form-group form-full">
                <label>Nama Rapat</label>
                <input type="text" name="nama_kegiatan" placeholder="Contoh: Rapat Koordinasi Timpora" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Lokasi</label>
                <input type="text" name="lokasi" placeholder="Contoh: Ruang Rapat Utama" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Tanggal Rapat</label>
                <input type="date" name="tanggal" required>
            </div>

            <div class="form-group form-full">
                <label>Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" placeholder="Agenda dan hasil rapat..."></textarea>
            </div>

            <div class="form-group">
                <label>📸 Upload File PNG <span style="color: #ff4c4c;">*</span></label>
                <input type="file" name="dokumentasi_png" accept=".png" required
                       style="background: rgba(10,25,47,0.05); border: 1px solid rgba(10,25,47,0.2); color: #0a192f; padding: 12px; border-radius: 8px; box-sizing: border-box; width: 100%;">
                <small style="color: rgba(10,25,47,0.7); font-size: 12px;">Format: PNG | Max: 5MB</small>
            </div>

            <div class="form-group">
                <label>📄 Upload File PDF <span style="color: #ff4c4c;">*</span></label>
                <input type="file" name="dokumentasi_pdf" accept=".pdf" required
                       style="background: rgba(10,25,47,0.05); border: 1px solid rgba(10,25,47,0.2); color: #0a192f; padding: 12px; border-radius: 8px; box-sizing: border-box; width: 100%;">
                <small style="color: rgba(10,25,47,0.7); font-size: 12px;">Format: PDF | Max: 10MB</small>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
            <a href="pilih_agenda.php" class="btn-outline-danger" style="padding: 12px 25px; text-decoration: none; display: inline-block; border-radius: 8px;">Batal</a>
            <button type="submit" class="btn-solid-primary" style="padding: 12px 35px; border: none; cursor: pointer; border-radius: 8px; font-weight: bold;">
                💾 Simpan Rapat
            </button>
        </div>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>
