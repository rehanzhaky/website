<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

// CEK KASTA
$is_admin = in_array(strtolower($_SESSION['role']), ['admin', 'admin_utama']);

// Logika Filter & Tab
$tab_aktif      = $_GET['tab'] ?? 'inventaris'; 
$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 
$seksi_filter   = $_GET['seksi'] ?? '';
$search_nama    = $_GET['search'] ?? '';

// Mapping Bulan Indo
$list_bulan = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 
    7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
    <div>
        <h2>Pusat Arsip Digital 📂</h2>
        <p>Manajemen data inventaris dan laporan umum <strong style="color: #072749;">SITUAN PADUKA</strong>.</p>
    </div>
</div>

<div class="badge-btn-group" style="margin-bottom: 20px;">
    <a href="?tab=inventaris&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>&seksi=<?= $seksi_filter ?>&search=<?= urlencode($search_nama) ?>" 
       class="badge-btn <?= $tab_aktif == 'inventaris' ? 'badge-solid-primary' : 'badge-outline-primary' ?>">
       📦 Arsip Inventaris
    </a>
    <a href="?tab=umum&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>&seksi=<?= $seksi_filter ?>&search=<?= urlencode($search_nama) ?>" 
       class="badge-btn <?= $tab_aktif == 'umum' ? 'badge-solid-info' : 'badge-outline-info' ?>">
       📝 Laporan Umum
    </a>
</div>

<div class="glass panel-utama">
    <form method="GET" action="" class="filter-pill-bar">
        <input type="hidden" name="tab" value="<?= $tab_aktif ?>">
        <div class="filter-row-full" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; color: var(--text-secondary);">tanggal mulai</label>
                <input type="date" name="mulai_tanggal" class="input-full-width" value="<?= $mulai_tanggal ?>" required>
            </div>
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; color: var(--text-secondary);">tanggal akhir</label>
                <input type="date" name="sampai_tanggal" class="input-full-width" value="<?= $sampai_tanggal ?>" required>
            </div>
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; color: var(--text-secondary);">filter seksi</label>
                <select name="seksi" class="input-full-width" style="padding: 10px; border-radius: 10px; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); outline: none;">
                    <option value="" style="background: var(--bg-primary); color: var(--text-primary);">Semua Seksi</option>
                    <option value="Sub Bag Tata Usaha" <?= $seksi_filter == 'Sub Bag Tata Usaha' ? 'selected' : '' ?> style="background: var(--bg-primary); color: var(--text-primary);">Sub Bag Tata Usaha</option>
                    <option value="Tikkomkim" <?= $seksi_filter == 'Tikkomkim' ? 'selected' : '' ?> style="background: var(--bg-primary); color: var(--text-primary);">Tikkomkim</option>
                    <option value="Intaltuskim" <?= $seksi_filter == 'Intaltuskim' ? 'selected' : '' ?> style="background: var(--bg-primary); color: var(--text-primary);">Intaltuskim</option>
                    <option value="Inteldakim" <?= $seksi_filter == 'Inteldakim' ? 'selected' : '' ?> style="background: var(--bg-primary); color: var(--text-primary);">Inteldakim</option>
                    <option value="Lantaskim" <?= $seksi_filter == 'Lantaskim' ? 'selected' : '' ?> style="background: var(--bg-primary); color: var(--text-primary);">Lantaskim</option>
                </select>
            </div>
        </div>
        <div class="filter-row-full" style="display: flex; align-items: center; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; color: var(--text-secondary);">
                    🔍 cari <?= $tab_aktif == 'inventaris' ? 'nama barang' : 'judul laporan' ?>
                </label>
                <input type="text" name="search" class="input-full-width" value="<?= htmlspecialchars($search_nama) ?>" 
                       placeholder="Ketik untuk mencari..." 
                       style="padding: 10px; border-radius: 10px; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); outline: none;">
            </div>
            <div style="margin-top: 22px;">
                <button type="submit" class="btn-navy-pill">terapkan filter 🔍</button>
            </div>
        </div>
    </form>

    <h3 style="margin-top: 35px; margin-bottom: 5px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
        <span style="font-size: 18px;">
            <?= $tab_aktif == 'inventaris' ? '📋 Riwayat Pengajuan Inventaris' : '📑 Arsip Laporan Umum' ?>
        </span>
    </h3>

    <div class="panel-tabel">
        <table class="table-minimal">
            <thead>
                <?php if ($tab_aktif == 'inventaris'): ?>
                    <tr>
                        <th style="width: 5%; text-align: center;">No.</th>
                        <th style="width: 25%;">Nama Barang</th>
                        <th style="width: 15%;">Jumlah</th>
                        <th style="width: 30%;">Ruangan / Keterangan</th>
                        <th style="width: 10%; text-align: center;">Status</th>
                        <th style="width: 15%; text-align: center;">Aksi</th>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th style="width: 5%; text-align: center;">No.</th>
                        <th style="width: 15%;">Periode</th>
                        <th style="width: 30%;">Judul Laporan</th>
                        <th style="width: 20%;">Seksi</th>
                        <th style="width: 15%; text-align: center;">Tgl Upload</th>
                        <th style="width: 15%; text-align: center;">Aksi</th>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody>
                <?php
                if ($tab_aktif == 'inventaris') {
                    /** * JOIN Master-Detail: Kita tarik semua kolom (*) dari detail, 
                     * dan status + seksi + ruangan dari master (pi).
                     */
                    $sql = "SELECT pi.status, pi.seksi as seksi_master, pi.id as id_master, pid.* FROM pengajuan_inventaris pi 
                            JOIN pengajuan_inventaris_detail pid ON pi.id = pid.id_pengajuan 
                            WHERE 1=1";
                    if (!empty($seksi_filter)) { $sql .= " AND pi.seksi = :seksi"; }
                    if (!empty($search_nama)) { $sql .= " AND pid.nama_barang LIKE :search"; }
                    $sql .= " ORDER BY pi.id DESC";
                    $stmt = $pdo->prepare($sql);
                    if (!empty($seksi_filter)) $stmt->bindParam(':seksi', $seksi_filter);
                    if (!empty($search_nama)) {
                        $search_param = "%{$search_nama}%";
                        $stmt->bindParam(':search', $search_param);
                    }
                } else {
                    $sql = "SELECT * FROM laporan_umum WHERE tanggal_upload BETWEEN :mulai AND :sampai";
                    if (!empty($seksi_filter)) { $sql .= " AND seksi = :seksi"; }
                    if (!empty($search_nama)) { $sql .= " AND judul_laporan LIKE :search"; }
                    $sql .= " ORDER BY tahun DESC, bulan DESC, id DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':mulai', $mulai_tanggal);
                    $stmt->bindParam(':sampai', $sampai_tanggal);
                    if (!empty($seksi_filter)) $stmt->bindParam(':seksi', $seksi_filter);
                    if (!empty($search_nama)) {
                        $search_param = "%{$search_nama}%";
                        $stmt->bindParam(':search', $search_param);
                    }
                }
                $stmt->execute();
                $data_arsip = $stmt->fetchAll();

                if (!$data_arsip): 
                ?>
                    <tr><td colspan="6" style="text-align: center; padding: 50px; opacity: 0.5; color: var(--text-primary);">📂 Belum ada data arsip...</td></tr>
                <?php else: 
                    $no = 1;
                    foreach ($data_arsip as $row):
                ?>
                    <tr>
                        <td style="text-align: center; border-bottom: 1px solid var(--border-color); color: var(--text-primary);"><?= $no++ ?></td>
                        <?php if ($tab_aktif == 'inventaris'): ?>
                            <td style="font-weight: bold; color: var(--text-primary); border-bottom: 1px solid var(--border-color);">
                                <?= htmlspecialchars($row['nama_barang'] ?? '-') ?>
                            </td>
                            <td style="border-bottom: 1px solid var(--border-color); color: var(--text-primary);">
                                <?= htmlspecialchars($row['jumlah'] ?? '0') ?> Unit
                            </td>
                            <td style="border-bottom: 1px solid var(--border-color); color: var(--text-primary);">
                                <?= htmlspecialchars($row['ruangan'] ?? $row['keterangan'] ?? '-') ?>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid var(--border-color);">
                                <?php 
                                    $st = strtolower($row['status'] ?? 'pending');
                                    $clr = ($st == 'approve' || $st == 'selesai') ? '#10b981' : (($st == 'reject') ? '#dc2626' : '#f59e0b');
                                ?>
                                <span class="badge" style="background: rgba(255,255,255,0.05); color: <?= $clr ?>; border: 1px solid <?= $clr ?>;">
                                    <?= strtoupper($st) ?>
                                </span>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid var(--border-color);">
                                <a href="detail_inventaris.php?id=<?= $row['id_master'] ?>" class="btn-navy-pill" style="padding: 6px 15px; font-size: 11px;">👁️ detail</a>
                            </td>
                        <?php else: ?>
                            <td style="color: var(--blue-primary); font-weight: bold; border-bottom: 1px solid var(--border-color);">
                                <?= $list_bulan[$row['bulan']] ?? '-' ?> <?= $row['tahun'] ?? '' ?>
                            </td>
                            <td style="font-weight: bold; color: var(--text-primary); border-bottom: 1px solid var(--border-color);">
                                <?= htmlspecialchars($row['judul_laporan'] ?? '-') ?>
                            </td>
                            <td style="border-bottom: 1px solid var(--border-color);">
                                <span style="color: var(--blue-secondary);"><?= htmlspecialchars($row['seksi'] ?? '-') ?></span>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-primary);">
                                <?= date('d/m/Y', strtotime($row['tanggal_upload'])) ?>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid var(--border-color);">
                                <a href="uploads/laporan_umum/<?= $row['nama_file'] ?>" target="_blank" class="btn-navy-pill" style="padding: 6px 15px; font-size: 11px;">📥 buka</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>