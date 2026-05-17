<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';
include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <div class="dashboard-header">
        <h2>📅 Pilih Jenis Agenda Kakanim</h2>
        <p style="color: var(--text-secondary);">Silakan pilih jenis agenda yang ingin Anda tambahkan.</p>
    </div>
    <a href="agenda_kegiatan.php" style="color: var(--text-primary); text-decoration: none; padding: 12px 20px; background: var(--bg-secondary); border-radius: 8px; border: 1px solid var(--border-color); font-weight: bold; transition: all 0.3s;" onmouseover="this.style.background='var(--blue-lighter)'" onmouseout="this.style.background='var(--bg-secondary)'">
        📋 Lihat Semua Agenda
    </a>
</div>

<div class="card-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; padding: 0 20px;">
    
    <a href="tambah_kegiatan_kakanim.php" style="text-decoration: none; color: inherit;">
        <div class="stat-card glass panel-shortcut" style="height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; background: #072749; border: 1px solid #0a3a5c; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 50px; margin-bottom: 15px;">🎯</div> 
            <h3 style="color: #ffffff; margin-bottom: 10px;">Kegiatan Kakanim</h3>
            <p style="color: #ffffff; font-size: 14px;">Upload dokumentasi PNG & PDF kegiatan Kepala Kantor Imigrasi</p>
        </div>
    </a>

    <a href="tambah_rapat_kakanim.php" style="text-decoration: none; color: inherit;">
        <div class="stat-card glass panel-shortcut" style="height: 250px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; background: #072749; border: 1px solid #0a3a5c; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 50px; margin-bottom: 15px;">📋</div>
            <h3 style="color: #ffffff; margin-bottom: 10px;">Rapat Kakanim</h3>
            <p style="color: #ffffff; font-size: 14px;">Upload dokumentasi PNG & PDF rapat Kepala Kantor Imigrasi</p>
        </div>
    </a>

</div>

<?php include 'layouts/footer.php'; ?>
