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
        <p>Manajemen data inventaris dan laporan umum <strong>SITAU</strong>.</p>
    </div>
</div>

<div class="badge-btn-group" style="margin-bottom: 20px;">
    <a href="?tab=inventaris&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>&seksi=<?= $seksi_filter ?>" 
       class="badge-btn <?= $tab_aktif == 'inventaris' ? 'badge-solid-primary' : 'badge-outline-primary' ?>">
       📦 Arsip Inventaris
    </a>
    <a href="?tab=umum&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>&seksi=<?= $seksi_filter ?>" 
       class="badge-btn <?= $tab_aktif == 'umum' ? 'badge-solid-info' : 'badge-outline-info' ?>">
       📝 Laporan Umum
    </a>
</div>

<div class="glass panel-utama">
    <form method="GET" action="" class="filter-pill-bar">
        <input type="hidden" name="tab" value="<?= $tab_aktif ?>">
        <div class="filter-row-full" style="display: flex; align-items: center; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">tanggal mulai</label>
                <input type="date" name="mulai_tanggal" class="input-full-width" value="<?= $mulai_tanggal ?>" required>
            </div>
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">tanggal akhir</label>
                <input type="date" name="sampai_tanggal" class="input-full-width" value="<?= $sampai_tanggal ?>" required>
            </div>
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">filter seksi</label>
                <select name="seksi" class="input-full-width" style="padding: 10px; border-radius: 10px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); outline: none;">
                    <option value="" style="background: #0a192f; color: white;">Semua Seksi</option>
                    <option value="Tata Usaha" <?= $seksi_filter == 'Tata Usaha' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Tata Usaha</option>
                    <option value="Tikkomkim" <?= $seksi_filter == 'Tikkomkim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Tikkomkim</option>
                    <option value="Intaltuskim" <?= $seksi_filter == 'Intaltuskim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Intaltuskim</option>
                    <option value="Inteldakim" <?= $seksi_filter == 'Inteldakim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Inteldakim</option>
                    <option value="Lantaskim" <?= $seksi_filter == 'Lantaskim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Lantaskim</option>
                </select>
            </div>
            <div style="margin-top: 22px;">
                <button type="submit" class="btn-navy-pill">terapkan filter 🔍</button>
            </div>
        </div>
    </form>

    <h3 style="margin-top: 35px; margin-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
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
                    $sql .= " ORDER BY pi.id DESC";
                    $stmt = $pdo->prepare($sql);
                    if (!empty($seksi_filter)) $stmt->bindParam(':seksi', $seksi_filter);
                } else {
                    $sql = "SELECT * FROM laporan_umum WHERE tanggal_upload BETWEEN :mulai AND :sampai";
                    if (!empty($seksi_filter)) { $sql .= " AND seksi = :seksi"; }
                    $sql .= " ORDER BY tahun DESC, bulan DESC, id DESC";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':mulai', $mulai_tanggal);
                    $stmt->bindParam(':sampai', $sampai_tanggal);
                    if (!empty($seksi_filter)) $stmt->bindParam(':seksi', $seksi_filter);
                }
                $stmt->execute();
                $data_arsip = $stmt->fetchAll();

                if (!$data_arsip): 
                ?>
                    <tr><td colspan="6" style="text-align: center; padding: 50px; opacity: 0.5;">📂 Belum ada data arsip...</td></tr>
                <?php else: 
                    $no = 1;
                    foreach ($data_arsip as $row):
                ?>
                    <tr>
                        <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05);"><?= $no++ ?></td>
                        <?php if ($tab_aktif == 'inventaris'): ?>
                            <td style="font-weight: bold; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <?= htmlspecialchars($row['nama_barang'] ?? $row['barang'] ?? '-') ?>
                            </td>
                            <td style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <?= htmlspecialchars($row['jumlah'] ?? $row['qty'] ?? '0') ?> Unit
                            </td>
                            <td style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <?= htmlspecialchars($row['ruangan'] ?? $row['keterangan'] ?? $row['lokasi'] ?? '-') ?>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <?php 
                                    $st = strtolower($row['status'] ?? 'pending');
                                    $clr = ($st == 'approve' || $st == 'selesai') ? '#64ffda' : (($st == 'reject') ? '#ff4c4c' : '#ffb86c');
                                ?>
                                <span class="badge" style="background: rgba(255,255,255,0.05); color: <?= $clr ?>; border: 1px solid <?= $clr ?>;">
                                    <?= strtoupper($st) ?>
                                </span>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <a href="detail_inventaris.php?id=<?= $row['id_master'] ?>" class="btn-navy-pill" style="padding: 6px 15px; font-size: 11px;">👁️ detail</a>
                            </td>
                        <?php else: ?>
                            <td style="color: #64ffda; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <?= $list_bulan[$row['bulan']] ?? '-' ?> <?= $row['tahun'] ?? '' ?>
                            </td>
                            <td style="font-weight: bold; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <?= htmlspecialchars($row['judul_laporan'] ?? '-') ?>
                            </td>
                            <td style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span style="color: #bd93f9;"><?= htmlspecialchars($row['seksi'] ?? '-') ?></span>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 12px;">
                                <?= date('d/m/Y', strtotime($row['tanggal_upload'])) ?>
                            </td>
                            <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05);">
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