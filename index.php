<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM pengajuan_inventaris");
$total_pengajuan = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pengajuan_inventaris WHERE status = 'pending'");
$total_pending = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pengajuan_inventaris WHERE status = 'disetujui'");
$total_disetujui = $stmt->fetchColumn();

$stmt_recent = $pdo->query("SELECT nomor_surat, nama_surat, status, tanggal FROM pengajuan_inventaris ORDER BY id DESC LIMIT 5");
$recent_data = $stmt_recent->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Selamat Datang, <?= $_SESSION['nama_lengkap'] ?></h2>
    <p>Ringkasan sistem tata usaha per tanggal <?= date('d F Y') ?></p>
</div>

<div class="card-grid">
    <div class="stat-card glass card-biru">
        <h3><?= $total_pengajuan ?></h3>
        <p>Total Pengajuan</p>
    </div>
    <div class="stat-card glass card-kuning">
        <h3><?= $total_pending ?></h3>
        <p>Menunggu Approval</p>
    </div>
    <div class="stat-card glass card-hijau">
        <h3><?= $total_disetujui ?></h3>
        <p>Telah Disetujui</p>
    </div>
</div>

<div class="bottom-grid">
    <div class="panel-tabel glass">
        <h3>Aktivitas Pengajuan Terakhir</h3>
        <table class="table-minimal">
            <thead>
                <tr>
                    <th>No. Surat</th>
                    <th>Keperluan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nomor_surat']) ?></td>
                    <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower($row['status']) ?>">
                            <?= strtoupper($row['status']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-shortcut glass">
        <h3>Butuh Barang Cepat?</h3>
        <p>Langsung buat form pengajuan baru di sini.</p>
        <a href="buat_pengajuan.php" class="btn btn-shortcut">+ Buat Pengajuan</a>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>