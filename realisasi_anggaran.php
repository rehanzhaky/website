<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

// CEK KASTA: Apakah dia Admin/Admin Utama?
$is_admin = in_array(strtolower($_SESSION['role']), ['admin', 'admin_utama']);

$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 
$tab_aktif      = $_GET['tab'] ?? 'jenis_belanja'; 

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
    <div>
        <h2>Realisasi Anggaran 🏛️</h2>
        <p>Pantau serapan anggaran berdasarkan jenis belanja atau sumber dana.</p>
    </div>
    
    <!-- TOMBOL TAMBAH CUMA MUNCUL BUAT ADMIN -->
    <?php if ($is_admin): ?>
        <a href="tambah_realisasi.php" class="btn-shortcut" style="padding: 10px 20px; width: auto; font-size: 13px;">
            + Tambah Laporan
        </a>
    <?php endif; ?>
</div>

<div class="badge-btn-group" style="margin-bottom: 20px;">
    <a href="?tab=jenis_belanja&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>" 
       class="badge-btn <?= $tab_aktif == 'jenis_belanja' ? 'badge-solid-primary' : 'badge-outline-primary' ?>">
       📦 Per Jenis Belanja
    </a>
    <a href="?tab=sumber_dana&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>" 
       class="badge-btn <?= $tab_aktif == 'sumber_dana' ? 'badge-solid-info' : 'badge-outline-info' ?>">
       🏦 Per Sumber Dana
    </a>
</div>

<div class="glass panel-utama">
    <?php
    $seksi_aktif = $_GET['seksi'] ?? '';
    ?>

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
            <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">seksi</label>
            <select name="seksi" class="input-full-width" style="padding: 10px; border-radius: 10px; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); outline: none;">
                <option value="" style="background: #0a192f; color: white;">Semua Seksi</option>
                <option value="Tata Usaha" <?= $seksi_aktif == 'Tata Usaha' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Tata Usaha</option>
                <option value="Tikkomkim" <?= $seksi_aktif == 'Tikkomkim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Tikkomkim</option>
                <option value="Intaltuskim" <?= $seksi_aktif == 'Intaltuskim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Intaltuskim</option>
                <option value="Inteldakim" <?= $seksi_aktif == 'Inteldakim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Inteldakim</option>
                <option value="Lantaskim" <?= $seksi_aktif == 'Lantaskim' ? 'selected' : '' ?> style="background: #0a192f; color: white;">Lantaskim</option>
            </select>
        </div>

            <div style="margin-top: 22px;">
                <button type="submit" class="btn-navy-pill">
                    terapkan filter 🔍
                </button>
            </div>
        </div>
    </form>

    <h3 style="margin-top: 35px; margin-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 18px;">
            <?= $tab_aktif == 'jenis_belanja' ? '📊 Realisasi Belanja Satker Per Jenis' : '🏢 Realisasi Belanja Satker Per Sumber Dana' ?>
        </span>
        <span class="badge badge-pending" style="font-size: 11px; text-transform: none; font-weight: normal;">
            <?= date('d M Y', strtotime($mulai_tanggal)) ?> - <?= date('d M Y', strtotime($sampai_tanggal)) ?>
        </span>
    </h3>

    <div class="panel-tabel">
        <table class="table-minimal">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 15%;">Tanggal Laporan</th>
                    <th style="width: 25%;">Kategori</th>
                    <th style="width: 40%;">Keterangan / Deskripsi</th>
                    <th style="width: 15%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $seksi_aktif = $_GET['seksi'] ?? '';

                $sql = "SELECT * FROM laporan_realisasi 
                        WHERE tipe_laporan = :tab 
                        AND periode_mulai >= :mulai 
                        AND periode_sampai <= :sampai";

                $params = [
                    'tab' => $tab_aktif,
                    'mulai' => $mulai_tanggal,
                    'sampai' => $sampai_tanggal
                ];

                if (!empty($seksi_aktif)) {
                    $sql .= " AND seksi = :seksi"; 
                    $params['seksi'] = $seksi_aktif; 
                }

                $sql .= " ORDER BY tanggal_laporan DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $data_laporan = $stmt->fetchAll();

                if (count($data_laporan) == 0): 
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; opacity: 0.5;">
                            <div style="font-size: 40px; margin-bottom: 10px;">📂</div>
                            Belum ada daftar laporan pada rentang tanggal dan seksi ini...
                        </td>
                    </tr>

                <?php 
                else: 
                    $no = 1;
                    foreach ($data_laporan as $row):
                        $tgl_indo = date('d M Y', strtotime($row['tanggal_laporan']));
                        
                        if ($row['tipe_laporan'] == 'jenis_belanja') {
                            $badge_style = "background: rgba(167, 139, 250, 0.15); color: #c4b5fd; border: 1px solid rgba(167, 139, 250, 0.4);";
                            $badge_text = "📦 Jenis Belanja";
                        } else {
                            $badge_style = "background: rgba(34, 211, 238, 0.15); color: #67e8f9; border: 1px solid rgba(34, 211, 238, 0.4);";
                            $badge_text = "🏦 Sumber Dana";
                        }
                ?>
                    <tr>
                        <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);"><?= $no++ ?></td>
                        
                        <td style="border-bottom: 1px solid rgba(255,255,255,0.1); font-weight: bold;"><?= $tgl_indo ?></td>
                        
                        <td style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <span style="padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 700; display: inline-block; <?= $badge_style ?>">
                                <?= $badge_text ?>
                            </span>
                        </td>
                        
                        <td style="border-bottom: 1px solid rgba(255,255,255,0.1); opacity: 0.9;">
                            <?= htmlspecialchars($row['keterangan']) ?> 
                            <br><small style="color: #64ffda;">Seksi: <?= htmlspecialchars($row['seksi']) ?></small>
                        </td>
                        
                        <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <!-- Tombol Detail (Semua bisa lihat) -->
                            <a href="detail_realisasi.php?id=<?= $row['id'] ?>" class="btn-navy-pill" style="padding: 6px 15px; font-size: 11px; margin: 0; display: inline-block; margin-bottom: 8px;">
                                👁️ Detail
                            </a>
                            
                            <!-- TOMBOL EDIT & HAPUS (CUMA ADMIN YANG BISA LIHAT) -->
                            <?php if ($is_admin): ?>
                                <br>
                                <a href="edit_realisasi.php?id=<?= $row['id'] ?>" style="color: #64ffda; text-decoration: none; font-size: 11px; font-weight: bold; margin-right: 10px;">Edit</a>
                                <a href="hapus_realisasi.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin mau hapus laporan ini?')" style="color: #ff4c4c; text-decoration: none; font-size: 11px; font-weight: bold;">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php 
                    endforeach; 
                endif; 
                ?>
            </tbody>
        </table>
    </div>

<?php include 'layouts/footer.php'; ?>