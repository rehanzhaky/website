<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

// Cek Kasta: Apakah yang login ini Admin?
$is_admin = in_array(strtolower($_SESSION['role']), ['admin', 'admin_utama']);

// Fitur Pencarian berdasarkan Kode atau Keterangan
$search = $_GET['cari'] ?? '';
$sql = "SELECT * FROM laporan_pnbp WHERE kode LIKE :search OR keterangan LIKE :search ORDER BY tanggal DESC, id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$data_pnbp = $stmt->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Laporan PNBP 💸</h2>
    <p>Pantau target pagu dan realisasi Penerimaan Negara Bukan Pajak.</p>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
    
    <form method="GET" action="" class="search-invisible">
        <input type="text" name="cari" value="<?= htmlspecialchars($search) ?>" placeholder="Cari kode atau keterangan...">
        <button type="submit" title="Cari">🔍</button>
    </form>

    <!-- Tombol Tambah HANYA UNTUK ADMIN -->
    <?php if ($is_admin): ?>
        <a href="tambah_pnbp.php" class="btn-shortcut" style="width: auto; padding: 12px 25px;">+ Tambah Realisasi PNBP</a>
    <?php endif; ?>

</div>

<div class="panel-tabel glass">
    <table class="table-minimal">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No.</th>
                <th style="width: 20%;">Kode / Jenis PNBP</th>
                <th style="width: 25%;">Pagu (Target)</th>
                <th style="width: 25%;">Realisasi</th>
                <th style="width: 15%;">Sisa / Selisih</th>
                <th style="width: 10%; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($data_pnbp) > 0): ?>
                <?php 
                $no = 1;
                foreach($data_pnbp as $row): 
                    // Rumus sakti hitung sisa target otomatis
                    $sisa = $row['pagu'] - $row['realisasi'];
                ?>
                <tr>
                    <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);"><?= $no++ ?></td>
                    
                    <td style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <!-- Badge ungu elegan buat Kode -->
                        <span style="padding: 6px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; display: inline-block; background: rgba(167, 139, 250, 0.15); color: #c4b5fd; border: 1px solid rgba(167, 139, 250, 0.4);">
                            🏷️ <?= htmlspecialchars($row['kode']) ?>
                        </span>
                        <div style="font-size: 11px; opacity: 0.7; margin-top: 5px;"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                    </td>
                    
                    <!-- Pagu pakai warna cyan standar -->
                    <td style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #64ffda; font-weight: bold;">
                        Rp <?= number_format($row['pagu'], 0, ',', '.') ?>
                    </td>
                    
                    <!-- Realisasi pakai warna orange -->
                    <td style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #ffb86c; font-weight: bold;">
                        Rp <?= number_format($row['realisasi'], 0, ',', '.') ?>
                    </td>

                    <!-- Sisa pakai warna hijau cerah -->
                    <td style="border-bottom: 1px solid rgba(255,255,255,0.1); color: #50fa7b; font-weight: bold;">
                        Rp <?= number_format($sisa, 0, ',', '.') ?>
                    </td>
                    
                    <td style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <?php if ($is_admin): ?>
                            <a href="edit_pnbp.php?id=<?= $row['id'] ?>" style="color: #64ffda; text-decoration: none; font-size: 11px; font-weight: bold; margin-right: 10px;">Edit</a>
                            <a href="hapus_pnbp.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin mau hapus data ini?')" style="color: #ff4c4c; text-decoration: none; font-size: 11px; font-weight: bold;">Hapus</a>
                        <?php else: ?>
                            <span style="font-size: 11px; color: rgba(255,255,255,0.4); font-style: italic;">Hanya Lihat</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 50px; opacity: 0.5;">
                        <div style="font-size: 40px; margin-bottom: 10px;">💸</div>
                        Belum ada data Pagu dan Realisasi PNBP yang dicatat...
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layouts/footer.php'; ?>