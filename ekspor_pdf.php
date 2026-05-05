<?php
require_once 'config/koneksi.php';

$id_laporan = $_GET['id'] ?? null;
if (!$id_laporan) {
    exit("ID Laporan tidak ditemukan!");
}

// Tarik Data Master
$stmt = $pdo->prepare("SELECT * FROM laporan_realisasi WHERE id = ?");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch();

if (!$laporan) {
    exit("Data laporan tidak ada!");
}

// Tarik Data Detail
$stmt_detail = $pdo->prepare("SELECT * FROM laporan_realisasi_detail WHERE id_laporan = ?");
$stmt_detail->execute([$id_laporan]);
$details = $stmt_detail->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak PDF - Realisasi Anggaran</title>
    <style>
        /* CSS Khusus Kertas Polos */
        body {
            font-family: 'Times New Roman', Times, serif; /* Font resmi laporan */
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        h2, h4 {
            text-align: center;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .info-tanggal {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px; /* Ukuran font tabel dikecilin dikit biar muat */
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            vertical-align: middle;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        
        /* Hilangkan margin kertas bawaan browser saat diprint */
        @page { margin: 15mm; }
    </style>
</head>
<body>

    <h2>REALISASI BELANJA <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? 'PER JENIS BELANJA' : 'PER SUMBER DANA' ?></h2>
    <h4>KANTOR IMIGRASI KELAS I TANJUNGPINANG</h4>
    <div class="info-tanggal">
        Periode: <?= date('d M Y', strtotime($laporan['periode_mulai'])) ?> s/d <?= date('d M Y', strtotime($laporan['periode_sampai'])) ?>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2"><?= $laporan['tipe_laporan'] == 'jenis_belanja' ? '(Kode) Jenis Belanja' : '(Kode) Sumber Dana' ?></th>
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
                    <td rowspan="2" class="text-center" style="vertical-align: middle;"><?= $no++ ?></td>
                    <td rowspan="2" style="vertical-align: middle;"><?= $row['kode_anggaran'] ?><br><small><?= $row['nama_anggaran'] ?></small></td>
                    <td>Pagu Realisasi</td>
                    <td class="text-right"><?= number_format($row['realisasi_pegawai'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['realisasi_barang'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['realisasi_modal'], 0, ',', '.') ?></td>
                    <td class="text-right bold"><?= number_format($total_realisasi, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Sisa</td>
                    <td class="text-right"><?= number_format($sisa_pegawai, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($sisa_barang, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($sisa_modal, 0, ',', '.') ?></td>
                    <td class="text-right bold"><?= number_format($total_sisa, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>