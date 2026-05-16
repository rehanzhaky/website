<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$is_admin = in_array(strtolower($_SESSION['role']), ['admin', 'admin_utama']);

$search = $_GET['cari'] ?? ''; // Benerin dikit, di form name-nya 'cari' bukan 'search'
$sql = "SELECT * FROM pengajuan_inventaris 
        WHERE seksi LIKE :search 
        OR diketahui_oleh LIKE :search 
        ORDER BY id DESC"; 

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$data_pengajuan = $stmt->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Daftar Pengajuan Inventaris 📦</h2>
    <p style="color: var(--text-secondary);">Pantau seluruh status pengajuan barang yang sudah dibuat di sini.</p>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
    
    <form method="GET" action="" class="search-invisible">
        <input type="text" name="cari" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nomor atau nama surat...">
        <button type="submit" title="Cari">🔍</button>
    </form>

    <a href="buat_pengajuan.php" class="btn-shortcut" style="width: auto; padding: 12px 25px;">+ Buat Pengajuan Baru</a>

</div>

<div class="panel-tabel glass" style="overflow-x: auto; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
    <table class="table-minimal" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">No</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Seksi</th> 
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Tanggal</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Diketahui Oleh</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Status</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Aksi</th>
                
                <?php if ($is_admin): ?>
                    <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; text-align: center;">Validasi & Hapus</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(count($data_pengajuan) > 0): ?>
                <?php 
                $no = 1;
                foreach($data_pengajuan as $row): 
                    $status_db = strtolower(trim($row['status']));
                    
                    if (in_array($status_db, ['approve', 'approved', 'diterima', 'terima', 'acc', 'disetujui', 'selesai'])) {
                        $warna_badge = 'rgba(80, 250, 123, 0.15)';
                        $warna_teks = '#50fa7b';
                        $label_status = 'APPROVED';
                        $is_pending = false;
                        $is_reject = false;
                    } 
                    elseif (in_array($status_db, ['reject', 'rejected', 'ditolak', 'tolak'])) {
                        $warna_badge = 'rgba(255, 85, 85, 0.15)';
                        $warna_teks = '#ff5555';
                        $label_status = 'REJECTED';
                        $is_pending = false;
                        $is_reject = true;
                    } 
                    else {
                        $warna_badge = 'rgba(255, 184, 108, 0.15)';
                        $warna_teks = '#ffb86c';
                        $label_status = 'PENDING';
                        $is_pending = true;
                        $is_reject = false;
                    }
                ?>
                <tr style="transition: 0.2s;" onmouseover="this.style.background='var(--blue-lighter)'" onmouseout="this.style.background='transparent'">
                    <td style="text-align: center; border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-primary);"><?= $no++ ?></td>
                    <td style="border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-primary);"><strong><?= htmlspecialchars($row['seksi']) ?></strong></td>
                    <td style="border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-primary);"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                    <td style="border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-primary);"><?= htmlspecialchars($row['diketahui_oleh']) ?></td>
                    
                    <td style="border-bottom: 1px solid var(--border-color); padding: 15px;">
                        <span class="badge" style="background: <?= $warna_badge ?>; color: <?= $warna_teks ?>; border: 1px solid <?= $warna_teks ?>; font-weight: bold; padding: 5px 10px; border-radius: 6px; font-size: 11px;">
                            <?= $label_status ?>
                        </span>
                    </td>
                    
                    <td style="border-bottom: 1px solid var(--border-color); padding: 15px;">
                        <a href="detail_pengajuan.php?id=<?= $row['id'] ?>" style="color: var(--blue-primary); text-decoration: none; font-weight: bold; margin-right: 10px; font-size: 12px;">
                            👁️ Detail
                        </a>

                        <?php if ($is_pending): ?>
                            <a href="javascript:void(0);" onclick="alert('Sabar bos! Suratnya masih nunggu antrean buat di-ACC.');" style="color: #999; text-decoration: none; cursor: not-allowed; font-weight: bold; opacity: 0.5; font-size: 12px;">
                                ⏳ Menunggu
                            </a>
                        <?php elseif ($is_reject): ?>
                            <span style="color: #ff5555; font-weight: bold; font-size: 11px; opacity: 0.8; padding: 4px 8px; background: rgba(255,85,85,0.1); border-radius: 4px;">
                                ❌ Batal Dicetak
                            </span>
                        <?php else: ?>
                            <a href="cetak.php?id=<?= $row['id'] ?>" target="_blank" style="color: var(--blue-primary); text-decoration: none; font-weight: bold; font-size: 12px;">
                                🖨️ Cetak PDF
                            </a>
                        <?php endif; ?>
                    </td>

                    <?php if ($is_admin): ?>
                        <td style="text-align: center; border-bottom: 1px solid var(--border-color); padding: 15px; white-space: nowrap;">
                            <?php if ($is_pending): ?>
                                <a href="proses_approve.php?id=<?= $row['id'] ?>&aksi=terima" onclick="return confirm('Yakin ingin MENERIMA pengajuan ini?')" style="background: rgba(80, 250, 123, 0.1); color: #50fa7b; border: 1px solid rgba(80, 250, 123, 0.3); padding: 5px 10px; border-radius: 6px; font-size: 11px; text-decoration: none; font-weight: bold; margin-right: 5px;">✅ ACC</a>
                                
                                <a href="proses_approve.php?id=<?= $row['id'] ?>&aksi=tolak" onclick="return confirm('Yakin ingin MENOLAK pengajuan ini?')" style="background: rgba(255, 184, 108, 0.1); color: #ffb86c; border: 1px solid rgba(255, 184, 108, 0.3); padding: 5px 10px; border-radius: 6px; font-size: 11px; text-decoration: none; font-weight: bold; margin-right: 5px;">❌ Tolak</a>
                            <?php else: ?>
                                <span style="font-size: 11px; color: var(--text-muted); font-style: italic; margin-right: 10px;">Validasi Selesai</span>
                            <?php endif; ?>

                            <a href="hapus_pengajuan.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin MENGHAPUS PERMANEN pengajuan ini beserta rincian barangnya?')" style="background: rgba(255, 85, 85, 0.1); color: #ff5555; border: 1px solid rgba(255, 85, 85, 0.3); padding: 5px 10px; border-radius: 6px; font-size: 11px; text-decoration: none; font-weight: bold;">🗑️ Hapus</a>
                        </td>
                    <?php endif; ?>

                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= $is_admin ? '7' : '6' ?>" style="text-align: center; padding: 50px; opacity: 0.7; color: var(--text-primary);">
                        Belum ada data pengajuan yang ditemukan.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layouts/footer.php'; ?>