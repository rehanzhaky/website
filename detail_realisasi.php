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

// 1. Ambil Data Master
$stmt_master = $pdo->prepare("SELECT * FROM laporan_realisasi_bidang WHERE id = ?");
$stmt_master->execute([$id_laporan]);
$laporan = $stmt_master->fetch();

if (!$laporan) {
    echo "<script>alert('Laporan tidak ditemukan!'); window.location.href='realisasi_anggaran.php';</script>";
    exit;
}

// 2. Ambil Data Detail
$stmt_detail = $pdo->prepare("SELECT * FROM laporan_realisasi_bidang_detail WHERE id_laporan = ?");
$stmt_detail->execute([$id_laporan]);
$details = $stmt_detail->fetchAll();

// Bantuan Format Rupiah
function rp($angka) {
    return number_format($angka ?: 0, 0, ',', '.');
}

// 3. Kalkulasi Total
$tot_pagu = 0;
$tot_realisasi = 0;
$tot_sisa = 0;

foreach ($details as $row) {
    $tot_pagu += $row['pagu'];
    $tot_realisasi += $row['realisasi'];
    $tot_sisa += $row['sisa'];
}
$tot_persen = $tot_pagu > 0 ? number_format(($tot_realisasi / $tot_pagu) * 100, 2) : 0;
$tahun_anggaran = date('Y', strtotime($laporan['tanggal_laporan']));

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header cetak-sembunyi" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2>Detail Realisasi Bidang 🏛️</h2>
        <p style="color: var(--text-secondary);"><?= htmlspecialchars($laporan['keterangan']) ?></p>
    </div>
    
    <div style="display: flex; gap: 12px;">
        <a href="realisasi_anggaran.php" style="padding: 10px 20px; background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-decoration: none; border-radius: 30px; font-size: 13px; font-weight: bold; transition: 0.3s;">
            ⬅️ kembali
        </a>
        <a href="ekspor_detail_realisasi_excel.php?id=<?= $id_laporan ?>" style="padding: 10px 20px; background: rgba(40, 167, 69, 0.9); color: #ffffff; text-decoration: none; border-radius: 30px; font-size: 13px; font-weight: bold; transition: 0.3s;">
            📊 ekspor excel
        </a>
        <button onclick="window.print()" style="padding: 10px 20px; background: #ffffff; color: #0a1128; border: none; cursor: pointer; font-weight: bold; border-radius: 30px; font-size: 13px; box-shadow: 0 4px 6px rgba(0,0,0,0.2); transition: 0.3s;">
            🖨️ cetak / pdf
        </button>
    </div>
</div>

<div class="glass panel-utama panel-cetak" style="padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
    
    <div class="kop-laporan" style="margin-bottom: 25px; font-size: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; color: var(--text-primary);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <div style="line-height: 1.6;">
                <strong class="text-label">Kementerian/Lembaga:</strong> 137 - KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN<br>
                <strong class="text-label">Unit Eselon I:</strong> 03 - DIREKTORAT JENDERAL IMIGRASI<br>
                <strong class="text-label">Satuan Kerja:</strong> 692823 - KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG
            </div>
            <div style="text-align: right; line-height: 1.6;">
                <strong class="text-label">Tanggal Laporan:</strong> <?= date('d F Y', strtotime($laporan['tanggal_laporan'])) ?><br>
                <strong class="text-label">Seksi / Bidang:</strong> <?= htmlspecialchars($laporan['seksi']) ?>
            </div>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="tabel-cetak" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; color: var(--text-primary);">
            <thead>
                <tr class="baris-header">
                    <th class="sel-tabel" rowspan="2" style="text-align: center; width: 35%;">Realisasi Per Bidang</th>
                    <th class="sel-tabel" rowspan="2" style="text-align: center; width: 20%;">Pagu</th>
                    <th class="sel-tabel" colspan="2" style="text-align: center; width: 25%;">Realisasi TA <?= $tahun_anggaran ?></th>
                    <th class="sel-tabel" rowspan="2" style="text-align: center; width: 20%;">Sisa Anggaran</th>
                </tr>
                <tr class="baris-header">
                    <th class="sel-tabel" style="text-align: center;">Periode</th>
                    <th class="sel-tabel" style="text-align: center;">%</th>
                </tr>
            </thead>
            <tbody>
                <tr class="baris-total">
                    <td class="sel-tabel" style="text-align: center; font-weight: bold; letter-spacing: 0.5px;">JUMLAH SELURUHNYA</td>
                    <td class="sel-tabel angka-total" style="text-align: right;"><?= rp($tot_pagu) ?></td>
                    <td class="sel-tabel angka-total" style="text-align: right;"><?= rp($tot_realisasi) ?></td>
                    <td class="sel-tabel angka-total" style="text-align: right;"><?= $tot_persen ?>%</td>
                    <td class="sel-tabel angka-total text-sisa" style="text-align: right;"><?= rp($tot_sisa) ?></td>
                </tr>

                <?php foreach ($details as $row): 
                    $persen = $row['pagu'] > 0 ? number_format(($row['realisasi'] / $row['pagu']) * 100, 2) : 0;
                ?>
                    <tr class="baris-data" style="transition: all 0.2s;" onmouseover="this.style.background='var(--blue-lighter)'" onmouseout="this.style.background='transparent'">
                        <td class="sel-tabel" style="padding: 15px 12px;">
                            <strong class="kode-komponen"><?= htmlspecialchars($row['kode_komponen']) ?></strong> 
                            <span style="opacity: 0.9;"><?= htmlspecialchars($row['judul_komponen']) ?></span>
                        </td>
                        <td class="sel-tabel" style="text-align: right; opacity: 0.9;"><?= rp($row['pagu']) ?></td>
                        <td class="sel-tabel" style="text-align: right; opacity: 0.9;"><?= rp($row['realisasi']) ?></td>
                        <td class="sel-tabel" style="text-align: right; opacity: 0.8;"><?= $persen ?>%</td>
                        <td class="sel-tabel" style="text-align: right; opacity: 0.9;"><?= rp($row['sisa']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* STYLE DEFAULT (TAMPILAN BROWSER - GLASSMORPHISM KALEM) */
.sel-tabel {
    border: 1px solid var(--border-color);
    padding: 12px;
}
.baris-header {
    background: var(--bg-secondary); /* Putih transparan */
    color: var(--text-primary);
}
.baris-total {
    background: var(--blue-lighter); /* Putih transparan agak tebal */
    color: var(--text-primary);
}
.angka-total {
    font-weight: bold;
    font-size: 14px;
}
.text-label {
    color: var(--text-secondary); /* Abu-abu kalem buat label */
    font-weight: normal;
}
.kode-komponen {
    color: var(--text-primary);
    font-weight: bold;
}
.text-sisa {
    color: var(--text-primary); 
}

/* STYLE KETIKA DI-PRINT (TAMPILAN KERTAS PUTIH) */
@media print {
    /* Set orientasi kertas ke landscape otomatis */
    @page {
        size: landscape;
        margin: 1cm;
    }
    
    body, html {
        background: #ffffff !important;
        color: #000000 !important;
    }
    
    .cetak-sembunyi, .sidebar, .navbar {
        display: none !important;
    }
    
    /* 🔥 MANTRA PEMBUNUH BAYANGAN & LENGKUNGAN KACA 🔥 */
    .glass, .panel-utama, .panel-cetak {
        background: #ffffff !important;
        box-shadow: none !important;
        -webkit-box-shadow: none !important;
        filter: none !important;
        backdrop-filter: none !important;
        border: none !important;
        border-radius: 0 !important; /* Hilangin sudut melengkung */
        padding: 0 !important;
        margin: 0 !important;
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
    }
    
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color: #000000 !important;
    }
    
    .kop-laporan {
        border-bottom: 2px solid #000 !important;
        color: #000 !important;
    }
    .text-label {
        color: #000000 !important;
        font-weight: bold !important;
    }
    .sel-tabel {
        border: 1px solid #000000 !important;
        padding: 8px !important;
    }
    .baris-header th {
        background-color: #6D9EEB !important; /* Biru terang Kemenkeu */
        color: #000000 !important;
        font-weight: bold !important;
    }
    .baris-total td {
        background-color: #A4C2F4 !important; /* Biru muda */
        color: #000000 !important;
        font-weight: normal !important;
    }
    .baris-data td {
        background-color: #ffffff !important;
    }
    .kode-komponen {
        color: #000000 !important;
        font-weight: normal !important;
    }
    .text-sisa {
        color: #000000 !important;
    }
}
</style>

<?php include 'layouts/footer.php'; ?>