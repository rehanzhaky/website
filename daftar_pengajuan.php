<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$is_admin = in_array(strtolower($_SESSION['role']), ['admin', 'admin_utama']);

$search = $_GET['cari'] ?? '';
$sql = "SELECT * FROM pengajuan_inventaris WHERE nama_surat LIKE :search OR nomor_surat LIKE :search ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$data_pengajuan = $stmt->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Daftar Pengajuan Inventaris</h2>
    <p>Pantau seluruh status pengajuan barang yang sudah dibuat di sini.</p>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
    
    <form method="GET" action="" class="search-invisible">
        <input type="text" name="cari" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nomor atau nama surat...">
        <button type="submit" title="Cari">🔍</button>
    </form>

    <a href="buat_pengajuan.php" class="btn-shortcut" style="width: auto; padding: 12px 25px;">+ Buat Pengajuan Baru</a>

</div>

<div class="panel-tabel glass">
    <table class="table-minimal">
        <thead>
            <tr>
                <th>No Surat</th>
                <th>Nama Surat</th>
                <th>Seksi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi Dokumen</th>
                
                <?php if ($is_admin): ?>
                    <th style="text-align: center;">Persetujuan</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(count($data_pengajuan) > 0): ?>
                <?php foreach($data_pengajuan as $row): 
                    $status_db = strtolower($row['status']);
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['nomor_surat']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama_surat']) ?></td>
                    <td><?= htmlspecialchars($row['seksi']) ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                    
                    <td>
                        <?php 
                        if ($status_db == 'approve') {
                            $warna_badge = 'rgba(80, 250, 123, 0.15)';
                            $warna_teks = '#50fa7b';
                            $label_status = 'APPROVE';
                        } elseif ($status_db == 'reject') {
                            $warna_badge = 'rgba(255, 85, 85, 0.15)';
                            $warna_teks = '#ff5555';
                            $label_status = 'REJECT';
                        } else {
                            $warna_badge = 'rgba(255, 184, 108, 0.15)';
                            $warna_teks = '#ffb86c';
                            $label_status = 'PENDING';
                        }
                        ?>
                        <span class="badge" style="background: <?= $warna_badge ?>; color: <?= $warna_teks ?>; border: 1px solid <?= $warna_teks ?>; font-weight: bold; padding: 5px 10px; border-radius: 6px; font-size: 11px;">
                            <?= $label_status ?>
                        </span>
                    </td>
                    
                    <td>
                        <?php if ($status_db == 'pending'): ?>
                            <a href="javascript:void(0);" onclick="alert('Sabar bos! Suratnya masih nunggu antrean buat di-ACC.');" style="color: #999; text-decoration: none; cursor: not-allowed; font-weight: bold; opacity: 0.5;">
                                ⏳ Menunggu
                            </a>
                        <?php elseif ($status_db == 'reject'): ?>
                            <span style="color: #ff5555; font-weight: bold; font-size: 11px; opacity: 0.8; padding: 4px 8px; background: rgba(255,85,85,0.1); border-radius: 4px;">
                                ❌ Batal Dicetak
                            </span>
                        <?php else: ?>
                            <a href="cetak.php?id=<?= $row['id'] ?>" target="_blank" style="color: #4facfe; text-decoration: none; font-weight: bold;">
                                🖨️ Cetak PDF
                            </a>
                        <?php endif; ?>
                    </td>

                    <?php if ($is_admin): ?>
                        <td style="text-align: center;">
                            <?php if ($status_db == 'pending'): ?>
                                <a href="proses_approve.php?id=<?= $row['id'] ?>&aksi=terima" onclick="return confirm('Yakin ingin MENERIMA pengajuan ini?')" style="background: rgba(80, 250, 123, 0.1); color: #50fa7b; border: 1px solid rgba(80, 250, 123, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 11px; text-decoration: none; font-weight: bold; margin-right: 5px;">✅ ACC</a>
                                
                                <a href="proses_approve.php?id=<?= $row['id'] ?>&aksi=tolak" onclick="return confirm('Yakin ingin MENOLAK pengajuan ini?')" style="background: rgba(255, 85, 85, 0.1); color: #ff5555; border: 1px solid rgba(255, 85, 85, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 11px; text-decoration: none; font-weight: bold;">❌ Tolak</a>
                            <?php else: ?>
                                <span style="font-size: 11px; color: rgba(255,255,255,0.4); font-style: italic;">Selesai</span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>

                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $is_admin ? '7' : '6' ?>" style="text-align: center; padding: 30px; opacity: 0.7;">
                        Belum ada data pengajuan yang ditemukan.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layouts/footer.php'; ?>