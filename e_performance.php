<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

// Menangkap parameter seksi dari URL
$slug_seksi = $_GET['seksi'] ?? '';
$nama_seksi = str_replace('_', ' ', $slug_seksi);

// Kalau URL-nya kosong (nggak ada ?seksi=...), lempar ke halaman daftar seksi
if (empty($slug_seksi)) {
    header("Location: daftar_eperformance.php");
    exit;
}

include 'layouts/header.php';
include 'layouts/navbar.php';

$list_bulan = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 
    5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 
    9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];

$bulan_sekarang = date('n');
$tahun_sekarang = date('Y');

// Label tampilan: "Sub Bag Tata Usaha" tanpa prefix "Seksi", lainnya pakai prefix
$label_seksi = ($nama_seksi === 'Sub Bag Tata Usaha') ? $nama_seksi : 'Seksi ' . $nama_seksi;

// Cek apakah bulan ini seksi tersebut sudah upload e-performance
$check_sql = "SELECT id FROM e_performance WHERE seksi = ? AND bulan = ? AND tahun = ?";
$stmt_check = $pdo->prepare($check_sql);
$stmt_check->execute([$nama_seksi, $bulan_sekarang, $tahun_sekarang]);
$laporan_bulan_ini = $stmt_check->fetch();
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Daftar E-Performance 📈</h2>
        <p>Arsip Laporan Kinerja <strong><?= htmlspecialchars($label_seksi) ?></strong></p>
    </div>
    <a href="daftar_eperformance.php" style="color: var(--text-primary); text-decoration: none; opacity: 0.8; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 30px; font-size: 13px; font-weight: bold; background: var(--bg-secondary);">← Kembali</a>
</div>

<?php if (!$laporan_bulan_ini): ?>
    <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid #ff4c4c; padding: 15px; border-radius: 10px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px;">
        <span style="font-size: 24px;">⚠️</span>
        <div>
            <strong style="color: #ff4c4c; display: block; font-size: 15px; margin-bottom: 3px;">Perhatian!</strong>
            <span style="font-size: 13px; color: var(--text-secondary);"><?= htmlspecialchars($label_seksi) ?> <strong>belum mengunggah</strong> E-Performance untuk periode <?= $list_bulan[$bulan_sekarang] ?> <?= $tahun_sekarang ?>.</span>
        </div>
    </div>
<?php else: ?>
    <div style="background: rgba(100, 255, 218, 0.1); border: 1px solid #64ffda; padding: 15px; border-radius: 10px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px;">
        <span style="font-size: 24px;">✅</span>
        <div>
            <strong style="color: #64ffda; display: block; font-size: 15px; margin-bottom: 3px;">Aman!</strong>
            <span style="font-size: 13px; color: var(--text-secondary);">E-Performance periode <?= $list_bulan[$bulan_sekarang] ?> <?= $tahun_sekarang ?> sudah diunggah. Tetap pertahankan kedisiplinannya, cuy!</span>
        </div>
    </div>
<?php endif; ?>

<div class="glass panel-utama" style="padding: 30px;">
    <div class="panel-tabel">
        <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; color: var(--text-primary);">Riwayat Laporan Kinerja</h4>
            <a href="upload_eperformance.php?seksi=<?= htmlspecialchars($slug_seksi) ?>" class="btn-navy-pill" style="margin: 0; background: #072749; border-color: #072749; color: #ffffff; padding: 6px 15px; font-size: 12px;">
                + Upload Baru
            </a>
        </div>

        <table class="table-minimal">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 15%;">Periode</th>
                    <th style="width: 25%;">Judul Laporan</th>
                    <th style="width: 25%;">Keterangan</th>
                    <th style="width: 15%;">Tgl Upload</th>
                    <th style="width: 15%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Tarik data laporan dari tabel e_performance sesuai seksi
                $sql = "SELECT * FROM e_performance WHERE seksi = ? ORDER BY tahun DESC, bulan DESC, id DESC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_seksi]);
                $data_laporan = $stmt->fetchAll();

                if (count($data_laporan) == 0): 
                ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 50px; opacity: 0.5;">
                            <div style="font-size: 40px; margin-bottom: 10px;">📉</div>
                            Belum ada riwayat E-Performance yang diunggah...
                        </td>
                    </tr>
                
                <?php 
                else: 
                    $no = 1;
                    foreach ($data_laporan as $row):
                ?>
                    <tr>
                        <td style="text-align: center; border-bottom: 1px solid var(--border-color);"><?= $no++ ?></td>
                        
                        <td style="border-bottom: 1px solid var(--border-color); font-weight: bold; color: #072749;">
                            <?= $list_bulan[$row['bulan']] ?> <?= $row['tahun'] ?>
                        </td>
                        
                        <td style="border-bottom: 1px solid var(--border-color);">
                            <?= htmlspecialchars($row['judul_laporan']) ?>
                        </td>
                        
                        <td style="border-bottom: 1px solid var(--border-color); color: var(--text-secondary); font-size: 12px;">
                            <?= empty($row['keterangan']) ? '-' : htmlspecialchars($row['keterangan']) ?>
                        </td>

                        <td style="border-bottom: 1px solid var(--border-color); font-size: 12px;">
                            <?= date('d/m/Y', strtotime($row['tanggal_upload'])) ?>
                        </td>
                        
                        <td style="text-align: center; border-bottom: 1px solid var(--border-color);">
                            <a href="uploads/eperformance/<?= htmlspecialchars($row['nama_file']) ?>" target="_blank" class="btn-navy-pill" style="padding: 6px 15px; font-size: 11px; margin: 0;">
                                📥 Buka File
                            </a>
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