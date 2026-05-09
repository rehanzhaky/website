<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$id_laporan = $_GET['id'] ?? null;

if (!$id_laporan) {
    header("Location: realisasi_anggaran.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM laporan_realisasi WHERE id = ?");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch();

if (!$laporan) {
    echo "Laporan tidak ditemukan!";
    exit;
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Detail Realisasi Anggaran 🏛️</h2>
    <p><?= $laporan['keterangan'] ?></p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <a href="realisasi_anggaran.php" class="btn-navy-pill" style="background: rgba(255,255,255,0.05); color: white; border-color: rgba(255,255,255,0.2);">
                Kembali
            </a>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <a href="ekspor_pdf.php?id=<?= $id_laporan ?>" target="_blank" class="btn-navy-pill" style="text-decoration: none;">
                🖨️ Ekspor PDF / Cetak
            </a>
            <a href="ekspor_excel.php?id=<?= $id_laporan ?>" class="btn-navy-pill" style="border-color: #ffd700; color: #ffd700; text-decoration: none;">
                Excel
            </a>
        </div>
    </div>

    <div style="margin-bottom: 20px; font-size: 14px; opacity: 0.8;">
        <strong>Periode:</strong> <?= date('d M Y', strtotime($laporan['periode_mulai'])) ?> s/d <?= date('d M Y', strtotime($laporan['periode_sampai'])) ?> | 
        <strong>Kategori:</strong> <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? 'Per Jenis Belanja' : 'Per Sumber Dana' ?>
    </div>

    <div class="panel-tabel">
        <table class="table-minimal" style="border: 1px solid rgba(255,255,255,0.1);">
            <thead>
                <tr>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2);">No.</th>
                    // ganti kode belanja ke "kode | nama satker"
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2);">
                        <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? '(Kode) jenis belanja' : '(Kode) sumber dana' ?>
                    </th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2);">keterangan</th>
                    <th colspan="3" style="text-align: center; border: 1px solid rgba(255,255,255,0.2);">jenis belanja</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2);">total</th>
                </tr>
                <tr>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2);">pegawai</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2);">barang</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2);">modal</th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                $stmt_detail = $pdo->prepare("SELECT * FROM laporan_realisasi_detail WHERE id_laporan = ?");
                $stmt_detail->execute([$id_laporan]);
                $details = $stmt_detail->fetchAll();

                $no = 1;
                foreach ($details as $row):
                    $total_realisasi = $row['realisasi_pegawai'] + $row['realisasi_barang'] + $row['realisasi_modal'];
                    $sisa_pegawai = $row['pagu'] - $row['realisasi_pegawai']; // Contoh logika sisa (sesuaikan kebutuhan)
                    $sisa_barang = $row['pagu'] - $row['realisasi_barang']; 
                    $sisa_modal = $row['pagu'] - $row['realisasi_modal'];
                    $total_sisa = ($row['pagu'] * 3) - $total_realisasi; // Contoh total sisa
                ?>
                    <tr>
                        <td rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.1);"><?= $no++ ?></td>
                        <td rowspan="2" style="vertical-align: middle; border: 1px solid rgba(255,255,255,0.1);">
                            <?= $row['kode_anggaran'] ?><br><small><?= $row['nama_anggaran'] ?></small>
                        </td>
                        <td style="border: 1px solid rgba(255,255,255,0.1);">pagu realisasi</td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= number_format($row['realisasi_pegawai']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= number_format($row['realisasi_barang']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= number_format($row['realisasi_modal']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #64ffda;"><?= number_format($total_realisasi) ?></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid rgba(255,255,255,0.1);">sisa</td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= number_format($sisa_pegawai) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= number_format($sisa_barang) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= number_format($sisa_modal) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #ff4c4c;"><?= number_format($total_sisa) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include 'layouts/footer.php'; ?>