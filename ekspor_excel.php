<?php
require_once 'config/koneksi.php';

$id_laporan = $_GET['id'] ?? null;
if (!$id_laporan) {
    exit("ID Laporan tidak ditemukan!");
}

$stmt = $pdo->prepare("SELECT * FROM laporan_realisasi WHERE id = ?");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch();

if (!$laporan) {
    exit("Data laporan tidak ada!");
}

$stmt_detail = $pdo->prepare("SELECT * FROM laporan_realisasi_detail WHERE id_laporan = ?");
$stmt_detail->execute([$id_laporan]);
$details = $stmt_detail->fetchAll();

$nama_file = "Laporan_Realisasi_" . date('Ymd_His') . ".xls";
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th colspan="7" style="font-size: 16px; text-align: center; font-weight: bold; padding: 10px;">
                REALISASI BELANJA <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? 'PER JENIS BELANJA' : 'PER SUMBER DANA' ?>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; padding-bottom: 10px;">
                Periode: <?= date('d M Y', strtotime($laporan['periode_mulai'])) ?> s/d <?= date('d M Y', strtotime($laporan['periode_sampai'])) ?>
            </th>
        </tr>
        
        <tr>
            <th rowspan="2" style="background-color: #f2f2f2; text-align: center; vertical-align: middle;">No.</th>
            <th rowspan="2" style="background-color: #f2f2f2; text-align: center; vertical-align: middle;">
                <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? '(Kode) Jenis Belanja' : '(Kode) Sumber Dana' ?>
            </th>
            <th rowspan="2" style="background-color: #f2f2f2; text-align: center; vertical-align: middle;">Keterangan</th>
            <th colspan="3" style="background-color: #f2f2f2; text-align: center;">Jenis Belanja</th>
            <th rowspan="2" style="background-color: #f2f2f2; text-align: center; vertical-align: middle;">Total</th>
        </tr>
        <tr>
            <th style="background-color: #f2f2f2; text-align: center;">Pegawai</th>
            <th style="background-color: #f2f2f2; text-align: center;">Barang</th>
            <th style="background-color: #f2f2f2; text-align: center;">Modal</th>
        </tr>
    </thead>
    
    <tbody>
        <?php
        $no = 1;
        foreach ($details as $row):
            $total_realisasi = $row['realisasi_pegawai'] + $row['realisasi_barang'] + $row['realisasi_modal'];
            $sisa_pegawai = $row['pagu'] - $row['realisasi_pegawai']; 
            $sisa_barang = $row['pagu'] - $row['realisasi_barang']; 
            $sisa_modal = $row['pagu'] - $row['realisasi_modal'];
            $total_sisa = ($row['pagu'] * 3) - $total_realisasi; 
        ?>
            <tr>
                <td rowspan="2" style="text-align: center; vertical-align: middle;"><?= $no++ ?></td>
                <td rowspan="2" style="vertical-align: middle;"><?= $row['kode_anggaran'] ?> - <?= $row['nama_anggaran'] ?></td>
                <td>Pagu Realisasi</td>
                <td style="text-align: right;"><?= $row['realisasi_pegawai'] ?></td>
                <td style="text-align: right;"><?= $row['realisasi_barang'] ?></td>
                <td style="text-align: right;"><?= $row['realisasi_modal'] ?></td>
                <td style="text-align: right; font-weight: bold;"><?= $total_realisasi ?></td>
            </tr>
            <tr>
                <td>Sisa</td>
                <td style="text-align: right;"><?= $sisa_pegawai ?></td>
                <td style="text-align: right;"><?= $sisa_barang ?></td>
                <td style="text-align: right;"><?= $sisa_modal ?></td>
                <td style="text-align: right; font-weight: bold;"><?= $total_sisa ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>