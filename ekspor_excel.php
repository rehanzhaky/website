<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit; }
require_once 'config/koneksi.php';

$id_laporan = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM laporan_realisasi WHERE id = ?");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch();

if (!$laporan) { exit('Data tidak ditemukan'); }

// ========================================================
// MAGIC HEADER: Maksa browser unduh sebagai file Excel
// ========================================================
$nama_file = "Laporan_Realisasi_" . date('Ymd', strtotime($laporan['tanggal_laporan'])) . ".xls";
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");
header("Pragma: no-cache");
header("Expires: 0");

function rp($angka) { return number_format($angka ?: 0, 0, ',', '.'); }
?>

<table border="1">
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">
                <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? 'REALISASI BELANJA SATKER PER JENIS BELANJA' : 'REALISASI BELANJA SATKER PER SUMBER DANA' ?>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: left;">Tanggal Laporan: <?= date('d M Y', strtotime($laporan['tanggal_laporan'])) ?></th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: left;">Keterangan: <?= htmlspecialchars($laporan['keterangan']) ?></th>
        </tr>
        <tr>
            <th rowspan="2">NO</th>
            <th rowspan="2">Kode | Nama Satker / Sumber Dana</th>
            <th rowspan="2">Keterangan</th>
            <th colspan="3">Jenis Belanja</th>
            <th rowspan="2">Total</th>
        </tr>
        <tr>
            <th>Pegawai</th>
            <th>Barang</th>
            <th>Modal</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($laporan['tipe_laporan'] == 'jenis_belanja'): 
            $tot_pagu = $laporan['pagu_pegawai'] + $laporan['pagu_barang'] + $laporan['pagu_modal'];
            $tot_real = $laporan['realisasi_pegawai'] + $laporan['realisasi_barang'] + $laporan['realisasi_modal'];
            $tot_sisa = $laporan['sisa_pegawai'] + $laporan['sisa_barang'] + $laporan['sisa_modal'];
        ?>
            <tr>
                <td rowspan="3" style="text-align: center; vertical-align: middle;">1</td>
                <td rowspan="3" style="vertical-align: middle;">692823 | KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG</td>
                <td>PAGU</td>
                <td><?= rp($laporan['pagu_pegawai']) ?></td>
                <td><?= rp($laporan['pagu_barang']) ?></td>
                <td><?= rp($laporan['pagu_modal']) ?></td>
                <td><?= rp($tot_pagu) ?></td>
            </tr>
            <tr>
                <td>REALISASI</td>
                <td><?= rp($laporan['realisasi_pegawai']) ?></td>
                <td><?= rp($laporan['realisasi_barang']) ?></td>
                <td><?= rp($laporan['realisasi_modal']) ?></td>
                <td><?= rp($tot_real) ?></td>
            </tr>
            <tr>
                <td>SISA</td>
                <td><?= rp($laporan['sisa_pegawai']) ?></td>
                <td><?= rp($laporan['sisa_barang']) ?></td>
                <td><?= rp($laporan['sisa_modal']) ?></td>
                <td><?= rp($tot_sisa) ?></td>
            </tr>

        <?php else: 
            $rm_p = $laporan['rm_pagu_pegawai'] + $laporan['rm_pagu_barang'] + $laporan['rm_pagu_modal'];
            $rm_r = $laporan['rm_realisasi_pegawai'] + $laporan['rm_realisasi_barang'] + $laporan['rm_realisasi_modal'];
            $rm_s = $laporan['rm_sisa_pegawai'] + $laporan['rm_sisa_barang'] + $laporan['rm_sisa_modal'];
            $pnbp_p = $laporan['pnbp_pagu_pegawai'] + $laporan['pnbp_pagu_barang'] + $laporan['pnbp_pagu_modal'];
            $pnbp_r = $laporan['pnbp_realisasi_pegawai'] + $laporan['pnbp_realisasi_barang'] + $laporan['pnbp_realisasi_modal'];
            $pnbp_s = $laporan['pnbp_sisa_pegawai'] + $laporan['pnbp_sisa_barang'] + $laporan['pnbp_sisa_modal'];
        ?>
            <tr>
                <td rowspan="3" style="text-align: center; vertical-align: middle;">1</td>
                <td rowspan="3" style="vertical-align: middle;">(A) RUPIAH MURNI</td>
                <td>PAGU</td>
                <td><?= rp($laporan['rm_pagu_pegawai']) ?></td>
                <td><?= rp($laporan['rm_pagu_barang']) ?></td>
                <td><?= rp($laporan['rm_pagu_modal']) ?></td>
                <td><?= rp($rm_p) ?></td>
            </tr>
            <tr>
                <td>REALISASI</td>
                <td><?= rp($laporan['rm_realisasi_pegawai']) ?></td>
                <td><?= rp($laporan['rm_realisasi_barang']) ?></td>
                <td><?= rp($laporan['rm_realisasi_modal']) ?></td>
                <td><?= rp($rm_r) ?></td>
            </tr>
            <tr>
                <td>SISA</td>
                <td><?= rp($laporan['rm_sisa_pegawai']) ?></td>
                <td><?= rp($laporan['rm_sisa_barang']) ?></td>
                <td><?= rp($laporan['rm_sisa_modal']) ?></td>
                <td><?= rp($rm_s) ?></td>
            </tr>

            <tr>
                <td rowspan="3" style="text-align: center; vertical-align: middle;">2</td>
                <td rowspan="3" style="vertical-align: middle;">(D) PENERIMAAN NEGARA BUKAN PAJAK</td>
                <td>PAGU</td>
                <td><?= rp($laporan['pnbp_pagu_pegawai']) ?></td>
                <td><?= rp($laporan['pnbp_pagu_barang']) ?></td>
                <td><?= rp($laporan['pnbp_pagu_modal']) ?></td>
                <td><?= rp($pnbp_p) ?></td>
            </tr>
            <tr>
                <td>REALISASI</td>
                <td><?= rp($laporan['pnbp_realisasi_pegawai']) ?></td>
                <td><?= rp($laporan['pnbp_realisasi_barang']) ?></td>
                <td><?= rp($laporan['pnbp_realisasi_modal']) ?></td>
                <td><?= rp($pnbp_r) ?></td>
            </tr>
            <tr>
                <td>SISA</td>
                <td><?= rp($laporan['pnbp_sisa_pegawai']) ?></td>
                <td><?= rp($laporan['pnbp_sisa_barang']) ?></td>
                <td><?= rp($laporan['pnbp_sisa_modal']) ?></td>
                <td><?= rp($pnbp_s) ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>