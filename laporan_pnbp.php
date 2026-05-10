<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 1. PROTEKSI ROLE (Pengganti keuangan_unlocked yang bikin error)
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
if ($role_saat_ini !== 'admin_utama' && $role_saat_ini !== 'tu_keuangan') {
    echo "<script>alert('Akses Ditolak! Khusus Bagian Keuangan dan Admin Utama.'); window.location.href='pilih_laporan.php';</script>";
    exit;
}

require_once 'config/koneksi.php';

// 2. PERBAIKAN HAK AKSES TOMBOL (tu_keuangan sekarang bisa nambah data)
$is_admin = in_array($role_saat_ini, ['admin_utama', 'tu_keuangan']);

$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
    <div>
        <h2>Daftar Laporan PNBP 💸</h2>
        <p>Pantau rekapitulasi laporan Penerimaan Negara Bukan Pajak.</p>
    </div>
    
    <?php if ($is_admin): ?>
        <a href="tambah_laporan_pnbp.php" class="btn-shortcut" style="padding: 10px 20px; width: auto; font-size: 13px;">
            + Buat Laporan Baru
        </a>
    <?php endif; ?>
</div>

<div class="glass panel-utama">
    <form method="GET" action="" class="filter-pill-bar">
        <div class="filter-row-full" style="display: flex; align-items: center; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">tanggal mulai</label>
                <input type="date" name="mulai_tanggal" class="input-full-width" value="<?= $mulai_tanggal ?>" required>
            </div>
            
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">tanggal akhir</label>
                <input type="date" name="sampai_tanggal" class="input-full-width" value="<?= $sampai_tanggal ?>" required>
            </div>

            <div style="margin-top: 22px;">
                <button type="submit" class="btn-navy-pill">terapkan filter 🔍</button>
            </div>
        </div>
    </form>

    <h3 style="margin-top: 35px; margin-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 18px;">📊 Rekapitulasi Laporan PNBP</span>
    </h3>

    <div class="panel-tabel">
        <table class="table-minimal">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 15%;">Tgl Laporan</th>
                    <th style="width: 25%;">Periode Laporan</th>
                    <th style="width: 40%;">Keterangan</th>
                    <th style="width: 15%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM laporan_pnbp WHERE periode_mulai >= :mulai AND periode_sampai <= :sampai ORDER BY tanggal_laporan DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['mulai' => $mulai_tanggal, 'sampai' => $sampai_tanggal]);
                $data_laporan = $stmt->fetchAll();

                if (count($data_laporan) == 0): 
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; opacity: 0.5;">
                            Belum ada laporan PNBP pada rentang tanggal ini...
                        </td>
                    </tr>
                <?php 
                else: 
                    $no = 1;
                    foreach ($data_laporan as $row):
                ?>
                    <tr>
                        <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);"><?= $no++ ?></td>
                        <td style="border-bottom: 1px solid rgba(255,255,255,0.1); font-weight: bold;"><?= date('d M Y', strtotime($row['tanggal_laporan'])) ?></td>
                        <td style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <span class="badge" style="background: rgba(34, 211, 238, 0.15); color: #67e8f9; border: 1px solid rgba(34, 211, 238, 0.4);">
                                <?= date('d M Y', strtotime($row['periode_mulai'])) ?> s/d <?= date('d M Y', strtotime($row['periode_sampai'])) ?>
                            </span>
                        </td>
                        <td style="border-bottom: 1px solid rgba(255,255,255,0.1); opacity: 0.9;">
                            <?= htmlspecialchars($row['keterangan']) ?> 
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
                            <a href="detail_pnbp.php?id=<?= $row['id'] ?>" class="btn-navy-pill" style="padding: 6px 15px; font-size: 11px; margin-bottom: 8px; display: inline-block;">
                                👁️ Buka Rincian
                            </a>
                            
                            <?php if ($is_admin): ?>
                                <br>
                                <a href="hapus_laporan_pnbp.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus laporan ini beserta semua rincian di dalamnya?')" style="color: #ff4c4c; text-decoration: none; font-size: 11px; font-weight: bold;">Hapus Laporan</a>
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
</div>

<?php include 'layouts/footer.php'; ?>