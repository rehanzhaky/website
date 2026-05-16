<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$stmt_total = $pdo->query("SELECT COUNT(*) FROM pengajuan_inventaris");
$total_pengajuan = $stmt_total->fetchColumn();

$stmt_pending = $pdo->query("SELECT COUNT(*) FROM pengajuan_inventaris WHERE status = 'pending' OR status = ''");
$total_pending = $stmt_pending->fetchColumn();

$stmt_acc = $pdo->query("SELECT COUNT(*) FROM pengajuan_inventaris WHERE status IN ('approve', 'approved', 'diterima', 'terima', 'acc', 'disetujui', 'selesai')");
$total_acc = $stmt_acc->fetchColumn();

$stmt_recent = $pdo->query("SELECT * FROM pengajuan_inventaris ORDER BY tanggal DESC, id DESC LIMIT 5");
$recent_pengajuan = $stmt_recent->fetchAll();

$jam = date('H');
if ($jam < 12) $sapaan = "Selamat Pagi";
elseif ($jam < 15) $sapaan = "Selamat Siang";
elseif ($jam < 18) $sapaan = "Selamat Sore";
else $sapaan = "Selamat Malam";

$nama_user = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pegawai SITUAN PADUKA';

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="margin-bottom: 30px;">
    <h2><?= $sapaan ?>, <?= htmlspecialchars($nama_user) ?> 👋</h2>
    <p>Ringkasan sistem penatausahaan per tanggal <?= date('d F Y') ?></p>
</div>

<div style="display: flex; gap: 20px; margin-bottom: 30px;">
    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; border-left: 5px solid #072749;">
        <h1 style="margin: 0; font-size: 36px; color: #072749;"><?= $total_pengajuan ?></h1>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--text-secondary); font-weight: bold; letter-spacing: 1px;">TOTAL PENGAJUAN</p>
    </div>

    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; border-left: 5px solid #fbbf24;">
        <h1 style="margin: 0; font-size: 36px; color: #fbbf24;"><?= $total_pending ?></h1>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--text-secondary); font-weight: bold; letter-spacing: 1px;">MENUNGGU APPROVAL</p>
    </div>

    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; border-left: 5px solid #10b981;">
        <h1 style="margin: 0; font-size: 36px; color: #10b981;"><?= $total_acc ?></h1>
        <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--text-secondary); font-weight: bold; letter-spacing: 1px;">TELAH DISETUJUI</p>
    </div>
</div>

<div style="display: flex; gap: 20px;">
    
    <div class="glass" style="flex: 2; padding: 25px; border-radius: 15px;">
        <h3 style="margin-top: 0; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; color: var(--text-primary);">Aktivitas Pengajuan Terakhir</h3>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); font-size: 11px; color: var(--text-muted); text-align: left;">
                    <th style="padding: 10px 5px;">SEKSI</th>
                    <th style="padding: 10px 5px;">DIKETAHUI OLEH</th>
                    <th style="padding: 10px 5px;">TANGGAL</th>
                    <th style="padding: 10px 5px; text-align: center;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recent_pengajuan) > 0): ?>
                    <?php foreach ($recent_pengajuan as $row): 
                        $status_db = strtolower(trim($row['status']));
                        
                        if (in_array($status_db, ['approve', 'approved', 'diterima', 'terima', 'acc', 'disetujui', 'selesai'])) {
                            $warna = '#10b981'; $label = 'ACC';
                        } elseif (in_array($status_db, ['reject', 'rejected', 'ditolak', 'tolak'])) {
                            $warna = '#dc2626'; $label = 'TOLAK';
                        } else {
                            $warna = '#f59e0b'; $label = 'PENDING';
                        }
                    ?>
                    <tr style="border-bottom: 1px solid var(--border-color); font-size: 13px;">
                        <td style="padding: 15px 5px; color: var(--text-primary);"><strong><?= htmlspecialchars($row['seksi']) ?></strong></td>
                        <td style="padding: 15px 5px; color: var(--text-secondary);"><?= htmlspecialchars($row['diketahui_oleh']) ?></td>
                        <td style="padding: 15px 5px; color: var(--text-secondary);"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td style="padding: 15px 5px; text-align: center;">
                            <span style="color: <?= $warna ?>; border: 1px solid <?= $warna ?>; padding: 4px 8px; border-radius: 5px; font-size: 10px; font-weight: bold;">
                                <?= $label ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: var(--text-muted);">Belum ada aktivitas pengajuan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
        <div style="font-size: 50px; margin-bottom: 10px;">📦</div>
        <h3 style="margin: 0 0 10px 0; color: var(--text-primary);">Butuh Barang Cepat?</h3>
        <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 25px;">Langsung buat form pengajuan kebutuhan inventaris baru di sini.</p>
        <a href="buat_pengajuan.php" class="btn-shortcut" style="width: 100%; padding: 15px; text-decoration: none; display: block; box-sizing: border-box;">
            + Buat Pengajuan
        </a>
    </div>

</div>

<?php include 'layouts/footer.php'; ?>