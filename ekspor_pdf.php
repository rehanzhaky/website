<?php
session_start();
if (!isset($_SESSION['user_id'])) { exit; }
require_once 'config/koneksi.php';

$id_laporan = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM laporan_realisasi WHERE id = ?");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch();

if (!$laporan) { exit('Data tidak ditemukan'); }

function rp($angka) { return number_format($angka ?: 0, 0, ',', '.'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Realisasi</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; color: black; background: white; padding: 20px; }
        .kop-surat { text-align: center; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        hr { border: 0; border-top: 1px solid black; margin: 4px 0; }
        @media print { @page { size: landscape; } }
    </style>
</head>
<body onload="window.print()">

    <div class="kop-surat">
        KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA REPUBLIK INDONESIA<br>
        KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG<br>
        <span style="font-size: 16px; margin-top: 10px; display: block;">
            <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? 'REALISASI BELANJA SATKER PER JENIS BELANJA' : 'REALISASI BELANJA SATKER PER SUMBER DANA' ?>
        </span>
    </div>

    <p><strong>Tanggal Laporan:</strong> <?= date('d F Y', strtotime($laporan['tanggal_laporan'])) ?><br>
    <strong>Keterangan:</strong> <?= htmlspecialchars($laporan['keterangan']) ?></p>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">NO</th>
                <th rowspan="2" style="width: 25%;">Kode | Nama Satker / Sumber Dana</th>
                <th rowspan="2" style="width: 10%;">Keterangan</th>
                <th colspan="3">Jenis Belanja</th>
                <th rowspan="2" style="width: 15%;">Total</th>
            </tr>
            <tr>
                <th style="width: 15%;">Pegawai</th>
                <th style="width: 15%;">Barang</th>
                <th style="width: 15%;">Modal</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($laporan['tipe_laporan'] == 'jenis_belanja'): 
                $tot_pagu = $laporan['pagu_pegawai'] + $laporan['pagu_barang'] + $laporan['pagu_modal'];
                $tot_real = $laporan['realisasi_pegawai'] + $laporan['realisasi_barang'] + $laporan['realisasi_modal'];
                $tot_sisa = $laporan['sisa_pegawai'] + $laporan['sisa_barang'] + $laporan['sisa_modal'];
            ?>
                <tr>
                    <td rowspan="2" class="text-center">1</td>
                    <td rowspan="2" class="bold">692823 | KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG</td>
                    <td class="text-center bold">PAGU <hr> REALISASI</td>
                    <td class="text-right"><?= rp($laporan['pagu_pegawai']) ?> <hr> <?= rp($laporan['realisasi_pegawai']) ?></td>
                    <td class="text-right"><?= rp($laporan['pagu_barang']) ?> <hr> <?= rp($laporan['realisasi_barang']) ?></td>
                    <td class="text-right"><?= rp($laporan['pagu_modal']) ?> <hr> <?= rp($laporan['realisasi_modal']) ?></td>
                    <td class="text-right bold"><?= rp($tot_pagu) ?> <hr> <?= rp($tot_real) ?></td>
                </tr>
                <tr>
                    <td class="text-center bold">SISA</td>
                    <td class="text-right"><?= rp($laporan['sisa_pegawai']) ?></td>
                    <td class="text-right"><?= rp($laporan['sisa_barang']) ?></td>
                    <td class="text-right"><?= rp($laporan['sisa_modal']) ?></td>
                    <td class="text-right bold"><?= rp($tot_sisa) ?></td>
                </tr>
            <?php else: 
                // Kalkulasi Subtotal & Grandtotal Sumber Dana
                $rm_p = $laporan['rm_pagu_pegawai'] + $laporan['rm_pagu_barang'] + $laporan['rm_pagu_modal'];
                $rm_r = $laporan['rm_realisasi_pegawai'] + $laporan['rm_realisasi_barang'] + $laporan['rm_realisasi_modal'];
                $rm_s = $laporan['rm_sisa_pegawai'] + $laporan['rm_sisa_barang'] + $laporan['rm_sisa_modal'];
                $pnbp_p = $laporan['pnbp_pagu_pegawai'] + $laporan['pnbp_pagu_barang'] + $laporan['pnbp_pagu_modal'];
                $pnbp_r = $laporan['pnbp_realisasi_pegawai'] + $laporan['pnbp_realisasi_barang'] + $laporan['pnbp_realisasi_modal'];
                $pnbp_s = $laporan['pnbp_sisa_pegawai'] + $laporan['pnbp_sisa_barang'] + $laporan['pnbp_sisa_modal'];
            ?>
                <tr>
                    <td rowspan="2" class="text-center">1</td>
                    <td rowspan="2" class="bold">(A) RUPIAH MURNI</td>
                    <td class="text-center bold">PAGU <hr> REALISASI</td>
                    <td class="text-right"><?= rp($laporan['rm_pagu_pegawai']) ?> <hr> <?= rp($laporan['rm_realisasi_pegawai']) ?></td>
                    <td class="text-right"><?= rp($laporan['rm_pagu_barang']) ?> <hr> <?= rp($laporan['rm_realisasi_barang']) ?></td>
                    <td class="text-right"><?= rp($laporan['rm_pagu_modal']) ?> <hr> <?= rp($laporan['rm_realisasi_modal']) ?></td>
                    <td class="text-right bold"><?= rp($rm_p) ?> <hr> <?= rp($rm_r) ?></td>
                </tr>
                <tr>
                    <td class="text-center bold">SISA</td>
                    <td class="text-right"><?= rp($laporan['rm_sisa_pegawai']) ?></td>
                    <td class="text-right"><?= rp($laporan['rm_sisa_barang']) ?></td>
                    <td class="text-right"><?= rp($laporan['rm_sisa_modal']) ?></td>
                    <td class="text-right bold"><?= rp($rm_s) ?></td>
                </tr>
                <tr>
                    <td rowspan="2" class="text-center">2</td>
                    <td rowspan="2" class="bold">(D) PENERIMAAN NEGARA BUKAN PAJAK</td>
                    <td class="text-center bold">PAGU <hr> REALISASI</td>
                    <td class="text-right"><?= rp($laporan['pnbp_pagu_pegawai']) ?> <hr> <?= rp($laporan['pnbp_realisasi_pegawai']) ?></td>
                    <td class="text-right"><?= rp($laporan['pnbp_pagu_barang']) ?> <hr> <?= rp($laporan['pnbp_realisasi_barang']) ?></td>
                    <td class="text-right"><?= rp($laporan['pnbp_pagu_modal']) ?> <hr> <?= rp($laporan['pnbp_realisasi_modal']) ?></td>
                    <td class="text-right bold"><?= rp($pnbp_p) ?> <hr> <?= rp($pnbp_r) ?></td>
                </tr>
                <tr>
                    <td class="text-center bold">SISA</td>
                    <td class="text-right"><?= rp($laporan['pnbp_sisa_pegawai']) ?></td>
                    <td class="text-right"><?= rp($laporan['pnbp_sisa_barang']) ?></td>
                    <td class="text-right"><?= rp($laporan['pnbp_sisa_modal']) ?></td>
                    <td class="text-right bold"><?= rp($pnbp_s) ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center bold">GRAND TOTAL</td>
                    <td class="text-center bold">PAGU <hr> REALISASI</td>
                    <td class="text-right bold"><?= rp($rm_p + $pnbp_p) ?> <hr> <?= rp($rm_r + $pnbp_r) ?></td>
                    <td class="text-right bold"><?= rp($laporan['rm_pagu_barang'] + $laporan['pnbp_pagu_barang']) ?> <hr> <?= rp($laporan['rm_realisasi_barang'] + $laporan['pnbp_realisasi_barang']) ?></td>
                    <td class="text-right bold"><?= rp($laporan['rm_pagu_modal'] + $laporan['pnbp_pagu_modal']) ?> <hr> <?= rp($laporan['rm_realisasi_modal'] + $laporan['pnbp_realisasi_modal']) ?></td>
                    <td class="text-right bold"><?= rp($rm_p + $pnbp_p) ?> <hr> <?= rp($rm_r + $pnbp_r) ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-center bold">TOTAL SISA ANGGARAN KESELURUHAN</td>
                    <td class="text-right bold"><?= rp($laporan['rm_sisa_pegawai'] + $laporan['pnbp_sisa_pegawai']) ?></td>
                    <td class="text-right bold"><?= rp($laporan['rm_sisa_barang'] + $laporan['pnbp_sisa_barang']) ?></td>
                    <td class="text-right bold"><?= rp($laporan['rm_sisa_modal'] + $laporan['pnbp_sisa_modal']) ?></td>
                    <td class="text-right bold"><?= rp($rm_s + $pnbp_s) ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>