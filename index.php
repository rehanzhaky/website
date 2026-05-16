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
    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; border-left: 5px solid #4facfe;">
        <h1 style="margin: 0; font-size: 36px; color: #4facfe;"><?= $total_pengajuan ?></h1>
        <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.7; font-weight: bold; letter-spacing: 1px;">TOTAL PENGAJUAN</p>
    </div>

    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; border-left: 5px solid #ffb86c;">
        <h1 style="margin: 0; font-size: 36px; color: #ffb86c;"><?= $total_pending ?></h1>
        <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.7; font-weight: bold; letter-spacing: 1px;">MENUNGGU APPROVAL</p>
    </div>

    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; border-left: 5px solid #50fa7b;">
        <h1 style="margin: 0; font-size: 36px; color: #50fa7b;"><?= $total_acc ?></h1>
        <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.7; font-weight: bold; letter-spacing: 1px;">TELAH DISETUJUI</p>
    </div>
</div>

<div style="display: flex; gap: 20px;">
    
    <div class="glass" style="flex: 2; padding: 25px; border-radius: 15px;">
        <h3 style="margin-top: 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Aktivitas Pengajuan Terakhir</h3>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2); font-size: 11px; opacity: 0.6; text-align: left;">
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
                            $warna = '#50fa7b'; $label = 'ACC';
                        } elseif (in_array($status_db, ['reject', 'rejected', 'ditolak', 'tolak'])) {
                            $warna = '#ff5555'; $label = 'TOLAK';
                        } else {
                            $warna = '#ffb86c'; $label = 'PENDING';
                        }
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 13px;">
                        <td style="padding: 15px 5px;"><strong><?= htmlspecialchars($row['seksi']) ?></strong></td>
                        <td style="padding: 15px 5px; opacity: 0.8;"><?= htmlspecialchars($row['diketahui_oleh']) ?></td>
                        <td style="padding: 15px 5px; opacity: 0.8;"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td style="padding: 15px 5px; text-align: center;">
                            <span style="color: <?= $warna ?>; border: 1px solid <?= $warna ?>; padding: 4px 8px; border-radius: 5px; font-size: 10px; font-weight: bold;">
                                <?= $label ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; opacity: 0.5;">Belum ada aktivitas pengajuan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="glass" style="flex: 1; padding: 25px; border-radius: 15px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
        <div style="font-size: 50px; margin-bottom: 10px;">📦</div>
        <h3 style="margin: 0 0 10px 0;">Butuh Barang Cepat?</h3>
        <p style="font-size: 13px; opacity: 0.7; margin-bottom: 25px;">Langsung buat form pengajuan kebutuhan inventaris baru di sini.</p>
        <a href="buat_pengajuan.php" class="btn-shortcut" style="width: 100%; padding: 15px; text-decoration: none; display: block; box-sizing: border-box;">
            + Buat Pengajuan
        </a>
    </div>

</div>

<?php include 'layouts/footer.php'; ?>