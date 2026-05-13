<?php
@session_start(); // Pakai @ biar PHP tutup mulut soal Notice Session!
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';
include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header text-center" style="margin-bottom: 50px;">
    <h2>Pilih Jenis Realisasi 📊</h2>
    <p style="color: rgba(255,255,255,0.7);">Silakan pilih kategori laporan keuangan yang ingin Anda akses atau lihat.</p>
</div>

<div class="card-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; padding: 0 50px;">
    
    <a href="realisasi_anggaran.php" style="text-decoration: none; color: inherit;">
        <div class="stat-card glass panel-shortcut" style="height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border: 1px solid rgba(100, 255, 218, 0.3);">
            <div style="font-size: 50px; margin-bottom: 15px;">🏛️</div> 
            <h3 style="color: #64ffda; margin-bottom: 10px;">Realisasi Anggaran</h3>
            <p style="color: rgba(255,255,255,0.7); font-size: 14px;">Pemantauan Serapan Anggaran Per Bidang / Seksi</p>
        </div>
    </a>

    <a href="laporan_pnbp.php" style="text-decoration: none; color: inherit;">
        <div class="stat-card glass panel-shortcut" style="height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border: 1px solid rgba(79, 172, 254, 0.3);">
            <div style="font-size: 50px; margin-bottom: 15px;">💰</div>
            <h3 style="color: #4facfe; margin-bottom: 10px;">Realisasi PNBP</h3>
            <p style="color: rgba(255,255,255,0.7); font-size: 14px;">Penerimaan Negara Bukan Pajak Per Akun</p>
        </div>
    </a>

</div>

<?php include 'layouts/footer.php'; ?>