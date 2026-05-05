<?php
session_start();
require_once 'config/koneksi.php';

// PROTEKSI: Cuma Admin & Admin Utama yang boleh masuk
if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tangkap data kategori & filter
    $tipe_laporan    = $_POST['tipe_laporan'];
    $tanggal_laporan = $_POST['tanggal_laporan'];
    $periode_mulai   = $_POST['periode_mulai'];
    $periode_sampai  = $_POST['periode_sampai'];
    $seksi           = $_POST['seksi'];
    
    // Tangkap data INTI (Duit & Tahun)
    $tahun       = $_POST['tahun'];
    $pagu        = $_POST['pagu'];
    $realisasi   = $_POST['realisasi'];
    $keterangan  = trim($_POST['keterangan']);

    // Validasi dasar: Realisasi nggak boleh lebih dari Pagu
    if ($realisasi > $pagu) {
        $error = "Waduh! Angka realisasi nggak boleh lebih besar dari Pagu Anggaran bos.";
    } else {
        // Masukin SEMUANYA ke database
        $sql = "INSERT INTO laporan_realisasi 
                (tipe_laporan, tanggal_laporan, periode_mulai, periode_sampai, seksi, tahun, pagu, realisasi, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$tipe_laporan, $tanggal_laporan, $periode_mulai, $periode_sampai, $seksi, $tahun, $pagu, $realisasi, $keterangan])) {
            // Kalau sukses, lempar balik ke halaman tabel di TAB yang sesuai
            header("Location: realisasi_anggaran.php?tab=" . $tipe_laporan);
            exit;
        } else {
            $error = "Gagal menyimpan laporan. Coba lagi!";
        }
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Tambah Laporan Realisasi</h2>
        <p>Unggah data pagu dan serapan anggaran per Jenis Belanja atau Sumber Dana.</p>
    </div>
    <a href="realisasi_anggaran.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali ke Daftar</a>
</div>

<div class="glass panel-utama">
    
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.2); color: #ff4c4c; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Klasifikasi Laporan</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Kategori Laporan</label>
                <select name="tipe_laporan" required>
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    <option value="jenis_belanja">📦 Per Jenis Belanja</option>
                    <option value="sumber_dana">🏦 Per Sumber Dana</option>
                </select>
            </div>

            <div class="form-group">
                <label>Seksi Pengguna Anggaran</label>
                <select name="seksi" required>
                    <option value="" disabled selected>-- Pilih Seksi --</option>
                    <option value="Tata Usaha">Tata Usaha</option>
                    <option value="Tikkomkim">Tikkomkim</option>
                    <option value="Intaltuskim">Intaltuskim</option>
                    <option value="Inteldakim">Inteldakim</option>
                    <option value="Lantaskim">Lantaskim</option>
                </select>
            </div>

            <div class="form-group form-full" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Tanggal Laporan</label>
                    <input type="date" name="tanggal_laporan" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div style="flex: 1;">
                    <label>Periode Mulai</label>
                    <input type="date" name="periode_mulai" required>
                </div>
                <div style="flex: 1;">
                    <label>Periode Sampai</label>
                    <input type="date" name="periode_sampai" required>
                </div>
            </div>
        </div>

        <h3 style="margin-top: 25px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Detail Anggaran</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>Tahun Anggaran</label>
                <input type="number" name="tahun" value="<?= date('Y') ?>" required>
            </div>

            <div class="form-group"></div>

            <div class="form-group form-full" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Pagu Anggaran (Rp)</label>
                    <input type="number" name="pagu" placeholder="Contoh: 1500000000 (Tanpa titik)" required>
                </div>
                <div style="flex: 1;">
                    <label>Realisasi Anggaran (Rp)</label>
                    <input type="number" name="realisasi" placeholder="Contoh: 500000000 (Tanpa titik)" required>
                </div>
            </div>

            <div class="form-group form-full">
                <label>Keterangan / Deskripsi Singkat</label>
                <input type="text" name="keterangan" placeholder="Contoh: Belanja Keperluan Perkantoran (521111) Triwulan 1..." required autocomplete="off">
            </div>

        </div>

        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 200px;">Simpan Laporan</button>
        </div>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>