<?php
session_start();
require_once 'config/koneksi.php';

// PROTEKSI: Cuma Admin & Admin Utama yang boleh masuk
if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_laporan = $_POST['tanggal_laporan'];
    $periode_mulai   = $_POST['periode_mulai'];
    $periode_sampai  = $_POST['periode_sampai'];
    $keterangan      = trim($_POST['keterangan']);

    // Validasi simpel: Pastikan tanggal mulai nggak lebih besar dari tanggal sampai
    if (strtotime($periode_mulai) > strtotime($periode_sampai)) {
        $error = "Tanggal periode mulai tidak boleh melebihi tanggal akhir periode!";
    } else {
        // Insert ke tabel Master
        $sql = "INSERT INTO laporan_pnbp (tanggal_laporan, periode_mulai, periode_sampai, keterangan) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$tanggal_laporan, $periode_mulai, $periode_sampai, $keterangan])) {
            // Sukses bikin map laporan, langsung balik ke halaman daftar
            header("Location: laporan_pnbp.php");
            exit;
        } else {
            $error = "Gagal membuat laporan PNBP. Silakan coba lagi!";
        }
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Buat Laporan PNBP Baru 📁</h2>
        <p>Buat folder/periode laporan baru sebelum memasukkan rincian realisasi.</p>
    </div>
    <a href="laporan_pnbp.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali ke Daftar</a>
</div>

<div class="glass panel-utama" style="max-width: 800px;">
    
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.2); color: #ff4c4c; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Informasi Periode Laporan</h3>
        
        <div class="form-grid">
            <div class="form-group form-full">
                <label>Tanggal Pembuatan Laporan</label>
                <!-- Default diisi tanggal hari ini -->
                <input type="date" name="tanggal_laporan" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group form-full" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Periode Mulai</label>
                    <input type="date" name="periode_mulai" required>
                </div>
                <div style="flex: 1;">
                    <label>Periode Sampai</label>
                    <input type="date" name="periode_sampai" required>
                </div>
            </div>

            <div class="form-group form-full">
                <label>Keterangan / Nama Laporan</label>
                <input type="text" name="keterangan" placeholder="Contoh: Laporan Realisasi PNBP Bulan Mei 2026..." required autocomplete="off">
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 250px;">💾 Simpan & Buat Laporan</button>
        </div>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>