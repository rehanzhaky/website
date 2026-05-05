<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';
include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header text-center" style="margin-bottom: 50px;">
    <h2>Pilih Jenis Realisasi</h2>
    <p>Silakan pilih kategori laporan keuangan yang ingin Anda akses atau kelola.</p>
</div>

<div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; padding: 0 50px;">
    <a href="realisasi_anggaran.php" style="text-decoration: none; color: inherit;">
        <div class="stat-card glass panel-shortcut" style="height: 250px; justify-content: center; border: 1px solid rgba(100, 255, 218, 0.3);">
            <div style="font-size: 50px; margin-bottom: 15px;">🏛️</div> 
            <h3 style="color: var(--accent-blue);">Realisasi Anggaran</h3>
            <p>Belanja Satker per Jenis & Sumber Dana</p>
        </div>
    </a>

    <a href="laporan_pnbp.php" style="text-decoration: none; color: inherit;">
        <div class="stat-card glass panel-shortcut" style="height: 250px; justify-content: center; border: 1px solid rgba(79, 172, 254, 0.3);">
            <div style="font-size: 50px; margin-bottom: 15px;">💰</div>
            <h3 style="color: #4facfe;">Realisasi PNBP</h3>
            <p>Penerimaan Negara Bukan Pajak</p>
        </div>
    </a>
</div>

<?php include 'layouts/footer.php'; ?>