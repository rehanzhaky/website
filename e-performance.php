<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

include 'layouts/header.php';
include 'layouts/navbar.php';

$sql_recent = "SELECT * FROM laporan_umum ORDER BY created_at DESC LIMIT 10";
$stmt = $pdo->prepare($sql_recent);
$stmt->execute();
$recent_activities = $stmt->fetchAll();

$bulan_ini = date('n');
$tahun_ini = date('Y');
$sql_count = "SELECT COUNT(id) as total FROM laporan_umum WHERE bulan = ? AND tahun = ?";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute([$bulan_ini, $tahun_ini]);
$total_laporan = $stmt_count->fetchColumn();
?>

<div class="dashboard-header">
    <h2>E-Performance 🚀</h2>
    <p>Pantau aktivitas unggahan dan rekam jejak kinerja lintas seksi secara real-time.</p>
</div>

<div class="form-grid" style="grid-template-columns: 1fr 2.5fr; gap: 25px; align-items: start;">
    
    <div class="glass" style="padding: 25px; text-align: center; border-radius: 20px;">
        <div style="font-size: 40px; margin-bottom: 10px;">📊</div>
        <h3 style="margin: 0 0 5px 0; color: white;">Total Kinerja</h3>
        <p style="margin: 0; font-size: 13px; color: rgba(255,255,255,0.6);">Bulan <?= date('F Y') ?></p>
        
        <div style="font-size: 48px; font-weight: bold; color: #64ffda; margin: 15px 0;">
            <?= $total_laporan ?>
        </div>
        <p style="margin: 0; font-size: 12px; color: rgba(255,255,255,0.6);">Dokumen berhasil diunggah bulan ini lintas seksi.</p>
    </div>

    <div class="glass" style="padding: 30px; border-radius: 20px;">
        <h3 style="margin-top: 0; margin-bottom: 25px; color: white; display: flex; align-items: center; gap: 10px;">
            🕒 Aktivitas Terbaru (Open Library)
        </h3>

        <?php if (count($recent_activities) == 0): ?>
            <div style="text-align: center; padding: 40px; opacity: 0.5;">
                <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                Belum ada aktivitas dokumen yang terekam.
            </div>
        <?php else: ?>
            <div class="timeline-container">
                <?php foreach ($recent_activities as $act): 
                    $waktu_upload = date('d M Y - H:i', strtotime($act['created_at']));
                ?>
                    <div class="timeline-card">
                        <div class="time-badge"><?= $waktu_upload ?> WIB</div>
                        
                        <div style="margin-bottom: 10px;">
                            <strong style="font-size: 16px; color: white;"><?= $act['judul_laporan'] ?></strong>
                            <span class="seksi-badge"><?= $act['seksi'] ?></span>
                        </div>
                        
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: rgba(255,255,255,0.7); line-height: 1.5;">
                            <?= empty($act['keterangan']) ? 'Tidak ada catatan tambahan.' : $act['keterangan'] ?>
                        </p>

                        <a href="uploads/laporan_umum/<?= $act['nama_file'] ?>" target="_blank" class="btn-navy-pill" style="margin: 0; font-size: 11px; padding: 5px 12px; background: transparent; border-color: rgba(255,255,255,0.3); color: white;">
                            📥 Buka Dokumen
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include 'layouts/footer.php'; ?>