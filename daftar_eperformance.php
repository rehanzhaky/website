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
$daftar_seksi = ['Tata Usaha', 'Tikkomkim', 'Intaltuskim', 'Inteldakim', 'Lantaskim'];
?>

<div class="dashboard-header">
    <h2>Daftar E-Performance 📂</h2>
    <p>Pilih seksi untuk melihat arsip Laporan Kinerja Bulanan.</p>

    <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
        <?php foreach ($daftar_seksi as $seksi): 
            $slug = str_replace(' ', '_', $seksi);
        ?>
            <a href="e_performance.php?seksi=<?= $slug ?>" style="text-decoration: none;">
                <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; color: var(--text-white); font-weight: bold; transition: all 0.3s; display: flex; justify-content: space-between; align-items: center;" onmouseover="this.style.background='rgba(79, 172, 254, 0.2)'; this.style.borderColor='#4facfe';" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.borderColor='rgba(255,255,255,0.1)';">
                    <span style="font-size: 15px;">📁 Seksi <?= $seksi ?></span>
                    <span style="opacity: 0.6; font-size: 13px; font-weight: normal;">Buka Arsip →</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>


<?php include 'layouts/footer.php'; ?>