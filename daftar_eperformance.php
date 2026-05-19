<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';
include 'layouts/header.php';
include 'layouts/navbar.php';

// Sesuaikan daftar seksi ini dengan yang ada di kantormu
$daftar_seksi = ['Sub Bag Tata Usaha', 'Tikkim', 'Intaltuskim', 'Inteldakim', 'Lantaskim', 'TPI'];
?>

<div class="dashboard-header">
    <h2>Daftar E-Performance 📂</h2>
    <p>Pilih seksi untuk melihat arsip Laporan Kinerja Bulanan.</p>

    <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
        <?php foreach ($daftar_seksi as $seksi): 
            $slug = str_replace(' ', '_', $seksi);
        ?>
            <a href="e_performance.php?seksi=<?= $slug ?>" style="text-decoration: none;">
                <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); padding: 20px; border-radius: 10px; color: var(--text-primary); font-weight: bold; transition: all 0.3s; display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.background='var(--blue-lighter)'; this.style.borderColor='var(--blue-primary)';" onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border-color)';">
                    <span style="font-size: 15px;">📁 <?= ($seksi === 'Sub Bag Tata Usaha') ? $seksi : 'Seksi ' . $seksi ?></span>
                    <span style="color: var(--text-muted); font-size: 13px; font-weight: normal;">Buka Arsip →</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>


<?php include 'layouts/footer.php'; ?>