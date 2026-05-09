<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_laporan = $_POST['tanggal_laporan'];
    $periode_mulai   = $_POST['periode_mulai'];
    $periode_sampai  = $_POST['periode_sampai'];
    
    $kode        = trim($_POST['kode']);
    $pagu        = $_POST['pagu'];
    $realisasi   = $_POST['realisasi'];
    $keterangan  = trim($_POST['keterangan']);

    if (strtotime($periode_mulai) > strtotime($periode_sampai)) {
        $error = "Tanggal periode mulai tidak boleh melebihi tanggal akhir!";
    } 
    
    elseif ($realisasi > $pagu) {
        $error = "Waduh! Angka realisasi nggak boleh lebih besar dari Target Pagu bos.";
    } 
    else {
        $sql = "INSERT INTO laporan_pnbp 
                (tanggal_laporan, periode_mulai, periode_sampai, kode, pagu, realisasi, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$tanggal_laporan, $periode_mulai, $periode_sampai, $kode, $pagu, $realisasi, $keterangan])) {
            header("Location: laporan_pnbp.php");
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
        <h2>Tambah Laporan PNBP</h2>
        <p>Unggah data target pagu dan serapan Penerimaan Negara Bukan Pajak.</p>
    </div>
    <a href="laporan_pnbp.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali ke Daftar</a>
</div>

<div class="glass panel-utama">
    
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.2); color: #ff4c4c; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Laporan Pembayaran PNBP K/L</h3>
        
        <div class="form-grid">
            <div class="form-group form-full">
                <label>K/L</label>
                <input type="text" name="k/l" placeholder="Contoh: Kementrian atau Lembaga" required autocomplete="off">
            </div>
            <div class="form-group form-full">
                <label>Unit Eselon I</label>
                <input type="text" name="unit_eselon_i" placeholder="Contoh: Ditjen Imigrasi" required autocomplete="off">
            </div>
            <div class="form-group form-full">
                <label>Satker</label>
                <input type="text" name="satker" placeholder="Contoh: Imigrasi Kelas I TPI" required autocomplete="off">
            </div>
        </div>

        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Periode Laporan</h3>
        
        <div class="form-grid">
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
        </div>

        <h3 style="margin-top: 25px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Detail Penerimaan</h3>

        <div class="form-grid">
            <div class="form-group form-full">
                <label>Kode / Jenis PNBP</label>
                <input type="text" name="kode" placeholder="Contoh: 425131 (Jasa Keimigrasian)" required autocomplete="off">
            </div>

            <div class="form-group form-full" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Target Pagu (Rp)</label>
                    <input type="number" name="pagu" placeholder="Contoh: 500000000 (Tanpa titik)" required>
                </div>
                <div style="flex: 1;">
                    <label>Realisasi (Rp)</label>
                    <input type="number" name="realisasi" placeholder="Contoh: 150000000 (Tanpa titik)" required>
                </div>
                <div style="flex: 1;">
                    <label>Sisa (Rp)</label>
                    <input type="number" name="sisa" placeholder="Contoh: 150000000 (Tanpa titik)" required>
                </div>
            </div>

            <div class="form-group form-full">
                <label>Keterangan / Deskripsi</label>
                <input type="text" name="keterangan" placeholder="Contoh: Penerimaan Paspor Bulan Mei..." required autocomplete="off">
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 200px;">Simpan Laporan</button>
        </div>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>