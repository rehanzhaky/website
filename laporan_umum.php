<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$is_admin = in_array(strtolower($_SESSION['role']), ['admin', 'admin_utama']);
$user_seksi = $_SESSION['seksi'] ?? '';

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Laporan Umum 📂</h2>
    <p>Kelola dan pantau dokumen laporan kinerja dari masing-masing seksi.</p>
</div>

<?php
$daftar_seksi = ['Sub Bag Tata Usaha', 'TIKKIM', 'Intaltuskim', 'Inteldakim', 'Lantaskim', 'TPI'];

foreach ($daftar_seksi as $seksi):
    if (!$is_admin && strtolower($seksi) !== strtolower($user_seksi)) {
        continue; 
    }

    $slug_seksi = str_replace(' ', '_', $seksi);
?>
    
<div class="accordion-item">
    <div class="accordion-header" onclick="toggleAccordion(this)">
        <span style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">🏢</span> <?= ($seksi === 'Sub Bag Tata Usaha') ? htmlspecialchars($seksi) : 'Seksi ' . htmlspecialchars($seksi) ?>
        </span>
        <span class="accordion-icon">▼</span>
    </div>
        
    <div class="accordion-body">
        <div class="accordion-content">
            <a href="daftar_laporan_umum.php?seksi=<?= $slug_seksi ?>" class="action-card view">
                <div class="card-title">👁️ Lihat Daftar Laporan</div>
                <div class="card-desc">
                    Buka dan pantau rekam jejak dokumen laporan yang sudah diarsipkan oleh <?= ($seksi === 'Sub Bag Tata Usaha') ? htmlspecialchars($seksi) : 'Seksi ' . htmlspecialchars($seksi) ?>. Anda dapat mengunduh atau mencetak dokumen dari menu ini.
                </div>
            </a>
                
            <a href="upload_laporan_umum.php?seksi=<?= $slug_seksi ?>" class="action-card upload">
                <div class="card-title">📤 Upload Laporan Baru</div>
                <div class="card-desc">
                    Unggah file dokumen laporan terbaru untuk <?= ($seksi === 'Sub Bag Tata Usaha') ? htmlspecialchars($seksi) : 'Seksi ' . htmlspecialchars($seksi) ?> ke dalam sistem arsip. Pastikan file berformat PDF atau Excel yang telah disetujui.
                </div>
            </a>
        </div>
    </div>
</div>

<?php endforeach; ?>

<script>
function toggleAccordion(header) {
    const item = header.parentElement;
    const body = item.querySelector('.accordion-body');
    const isActive = item.classList.contains('active');

    document.querySelectorAll('.accordion-item').forEach(el => {
        el.classList.remove('active');
        el.querySelector('.accordion-body').style.maxHeight = null;
    });

    if (!isActive) {
        item.classList.add('active');
        body.style.maxHeight = body.scrollHeight + "px"; 
    }
}
</script>

<?php include 'layouts/footer.php'; ?>