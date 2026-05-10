<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// Proteksi: Hanya Admin Utama dan TU Keuangan
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
if ($role_saat_ini !== 'admin_utama' && $role_saat_ini !== 'tu_keuangan') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='index.php';</script>";
    exit;
}

// Ambil status Tab, default ke 'jenis_belanja'
$tab_aktif = $_GET['tab'] ?? 'jenis_belanja';
$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
    <div>
        <h2>Realisasi Anggaran 🏛️</h2>
        <p>Manajemen laporan serapan anggaran kantor.</p>
    </div>
    
    <?php if ($tab_aktif == 'jenis_belanja'): ?>
        <a href="tambah_realisasi.php" class="btn-shortcut" style="padding: 10px 20px; width: auto; font-size: 13px; background: #64ffda; color: #0a192f;">
            + Tambah Jenis Belanja
        </a>
    <?php else: ?>
        <a href="tambah_sumber_dana.php" class="btn-shortcut" style="padding: 10px 20px; width: auto; font-size: 13px; background: #c4b5fd; color: #0a192f;">
            + Tambah Sumber Dana
        </a>
    <?php endif; ?>
</div>

<div class="badge-btn-group" style="margin-bottom: 25px; display: flex; gap: 10px;">
    <a href="?tab=jenis_belanja&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>" 
       class="badge-btn <?= $tab_aktif == 'jenis_belanja' ? 'badge-solid-primary' : 'badge-outline-primary' ?>" 
       style="text-decoration: none; padding: 10px 20px; border-radius: 50px; font-size: 13px;">
       📦 Per Jenis Belanja
    </a>
    <a href="?tab=sumber_dana&mulai_tanggal=<?= $mulai_tanggal ?>&sampai_tanggal=<?= $sampai_tanggal ?>" 
       class="badge-btn <?= $tab_aktif == 'sumber_dana' ? 'badge-solid-info' : 'badge-outline-info' ?>" 
       style="text-decoration: none; padding: 10px 20px; border-radius: 50px; font-size: 13px;">
       🏦 Per Sumber Dana
    </a>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    <form method="GET" action="" class="filter-pill-bar" style="margin-bottom: 30px;">
        <input type="hidden" name="tab" value="<?= $tab_aktif ?>">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">Dari Tanggal</label>
                <input type="date" name="mulai_tanggal" class="input-full-width" value="<?= $mulai_tanggal ?>">
            </div>
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" class="input-full-width" value="<?= $sampai_tanggal ?>">
            </div>
            <div style="margin-top: 22px;">
                <button type="submit" class="btn-navy-pill">Cari Data 🔍</button>
            </div>
        </div>
    </form>

    <div class="panel-tabel">
        <table class="table-minimal">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 35%;">Keterangan Laporan</th>
                    <th style="width: 25%; text-align: center;">Total Realisasi</th>
                    <th style="width: 20%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query data berdasarkan Tipe Tab dan Range Tanggal
                $sql = "SELECT * FROM laporan_realisasi 
                        WHERE tipe_laporan = :tab 
                        AND tanggal_laporan BETWEEN :mulai AND :sampai 
                        ORDER BY tanggal_laporan DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['tab' => $tab_aktif, 'mulai' => $mulai_tanggal, 'sampai' => $sampai_tanggal]);
                $data = $stmt->fetchAll();

                if (!$data): 
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 50px; opacity: 0.5;">
                            Belum ada laporan di tab ini...
                        </td>
                    </tr>
                <?php 
                else: 
                    $no = 1;
                    foreach ($data as $row):
                        // Hitung Total Realisasi buat ditampilin di daftar
                        if ($tab_aktif == 'jenis_belanja') {
                            $total_r = $row['realisasi_pegawai'] + $row['realisasi_barang'] + $row['realisasi_modal'];
                        } else {
                            $total_r = $row['rm_realisasi_pegawai'] + $row['rm_realisasi_barang'] + $row['rm_realisasi_modal'] + 
                                       $row['pnbp_realisasi_pegawai'] + $row['pnbp_realisasi_barang'] + $row['pnbp_realisasi_modal'];
                        }
                ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_laporan'])) ?></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td style="text-align: center; color: #64ffda;">Rp <?= number_format($total_r, 0, ',', '.') ?></td>
                        <td style="text-align: center;">
                            <a href="detail_realisasi.php?id=<?= $row['id'] ?>&tab=<?= $tab_aktif ?>" class="btn-navy-pill" style="padding: 5px 12px; font-size: 11px;">
                                👁️ Lihat Detail
                            </a>
                            <a href="hapus_realisasi.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus laporan ini?')" style="color: #ff4c4c; font-size: 11px; margin-left: 10px; text-decoration: none;">Hapus</a>
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