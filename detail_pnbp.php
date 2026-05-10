<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proteksi akses
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
if ($role_saat_ini !== 'admin_utama' && $role_saat_ini !== 'tu_keuangan') {
    echo "<script>alert('Akses Ditolak!'); window.location.href='index.php';</script>";
    exit;
}

require_once 'config/koneksi.php';

$id_laporan = $_GET['id'] ?? null;

if (!$id_laporan) {
    header("Location: laporan_pnbp.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM laporan_pnbp WHERE id = ?");
$stmt->execute([$id_laporan]);
$laporan = $stmt->fetch();

if (!$laporan) {
    echo "<script>alert('Laporan tidak ditemukan!'); window.location.href='laporan_pnbp.php';</script>";
    exit;
}

// Bantuan Format Rupiah
function rp($angka) {
    return number_format($angka ?: 0, 0, ',', '.');
}

// Hitung Grand Total
$tot_target = $laporan['target_425131'] + $laporan['target_425151'] + $laporan['target_425211'] + $laporan['target_425212'] + $laporan['target_425213'] + $laporan['target_425214'];
$tot_realisasi = $laporan['realisasi_425131'] + $laporan['realisasi_425151'] + $laporan['realisasi_425211'] + $laporan['realisasi_425212'] + $laporan['realisasi_425213'] + $laporan['realisasi_425214'];
$tot_sisa = $laporan['sisa_425131'] + $laporan['sisa_425151'] + $laporan['sisa_425211'] + $laporan['sisa_425212'] + $laporan['sisa_425213'] + $laporan['sisa_425214'];

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Detail Laporan PNBP 💰</h2>
    <p><?= htmlspecialchars($laporan['keterangan']) ?></p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <a href="laporan_pnbp.php" class="btn-navy-pill" style="background: rgba(255,255,255,0.05); color: white; border-color: rgba(255,255,255,0.2);">
                ⬅️ Kembali
            </a>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <a href="ekspor_pnbp_pdf.php?id=<?= $id_laporan ?>" target="_blank" class="btn-navy-pill" style="text-decoration: none; background: #ff4c4c; color: white; border: none;">
                🖨️ Cetak / PDF
            </a>
            <a href="ekspor_pnbp_excel.php?id=<?= $id_laporan ?>" class="btn-navy-pill" style="border-color: #50fa7b; color: #50fa7b; text-decoration: none; background: rgba(80, 250, 123, 0.1);">
                📊 Excel
            </a>
        </div>
    </div>

    <div style="margin-bottom: 20px; font-size: 14px; color: #fff; background: rgba(255,255,255,0.05); padding: 20px; border-radius: 8px; border: 1px solid rgba(79, 172, 254, 0.3);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">
            <div>
                <strong style="color: #4facfe;">Kementerian/Lembaga:</strong><br>
                13 - KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN
            </div>
            <div>
                <strong style="color: #4facfe;">Unit Eselon I:</strong><br>
                03 - DIREKTORAT JENDERAL IMIGRASI
            </div>
            <div style="text-align: right;">
                <strong style="color: #4facfe;">Periode Laporan:</strong><br>
                <?= date('d M Y', strtotime($laporan['periode_mulai'])) ?> s.d. <?= date('d M Y', strtotime($laporan['periode_sampai'])) ?>
            </div>
        </div>
        <div>
            <strong style="color: #4facfe;">Satuan Kerja:</strong><br>
            692823 - KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG
        </div>
    </div>

    <h3 style="text-align: center; color: #4facfe; letter-spacing: 1px; margin-bottom: 20px;">
        LAPORAN TARGET & REALISASI PNBP PER JENIS AKUN
    </h3>

    <div class="panel-tabel">
        <table class="table-minimal" style="border: 1px solid rgba(255,255,255,0.2); width: 100%;">
            <thead>
                <tr style="background: rgba(79, 172, 254, 0.15);">
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 25%; color: #4facfe;">Satuan Kerja</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 10%; color: #4facfe;">Kode Akun</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 35%; color: #4facfe;">Jenis Akun</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 15%; color: #4facfe;">Target</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 15%; color: #4facfe;">Realisasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="6" style="vertical-align: middle; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; padding: 15px;">
                        692823 - KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG
                    </td>
                    <td style="text-align: center; border: 1px solid rgba(255,255,255,0.1);">425131</td>
                    <td style="border: 1px solid rgba(255,255,255,0.1);">Pendapatan Sewa Tanah, Gedung, dan Bangunan</td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['target_425131']) ?></td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['realisasi_425131']) ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: 1px solid rgba(255,255,255,0.1);">425151</td>
                    <td style="border: 1px solid rgba(255,255,255,0.1);">Pendapatan Penggunaan Sarana dan Prasarana sesuai dengan Tusi</td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['target_425151']) ?></td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['realisasi_425151']) ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: 1px solid rgba(255,255,255,0.1);">425211</td>
                    <td style="border: 1px solid rgba(255,255,255,0.1);">Pendapatan Paspor</td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['target_425211']) ?></td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['realisasi_425211']) ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: 1px solid rgba(255,255,255,0.1);">425212</td>
                    <td style="border: 1px solid rgba(255,255,255,0.1);">Pendapatan Visa</td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['target_425212']) ?></td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['realisasi_425212']) ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: 1px solid rgba(255,255,255,0.1);">425213</td>
                    <td style="border: 1px solid rgba(255,255,255,0.1);">Pendapatan Izin Keimigrasian dan Izin Masuk Kembali (Re-entry permit)</td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['target_425213']) ?></td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['realisasi_425213']) ?></td>
                </tr>
                <tr>
                    <td style="text-align: center; border: 1px solid rgba(255,255,255,0.1);">425214</td>
                    <td style="border: 1px solid rgba(255,255,255,0.1);">Pendapatan Pelayanan Keimigrasian Lainnya</td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['target_425214']) ?></td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);"><?= rp($laporan['realisasi_425214']) ?></td>
                </tr>

                <tr style="background: rgba(79, 172, 254, 0.1);">
                    <td colspan="3" style="text-align: center; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #4facfe; font-size: 16px; padding: 15px;">
                        TOTAL
                    </td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #4facfe; font-size: 16px;">
                        <?= rp($tot_target) ?>
                    </td>
                    <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #4facfe; font-size: 16px;">
                        <?= rp($tot_realisasi) ?>
                    </td>
                </tr>
                <tr style="background: rgba(255, 76, 76, 0.1);">
                    <td colspan="3" style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #ff5555; padding: 10px;">
                        SISA TARGET YANG BELUM TERCAPAI
                    </td>
                    <td colspan="2" style="text-align: center; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #ff5555; font-size: 16px;">
                        <?= rp($tot_sisa) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .panel-utama, .panel-utama * {
        visibility: visible;
    }
    .panel-utama {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        border: none;
        background: white !important;
        color: black !important;
    }
    th, td {
        color: black !important;
        border-color: #ddd !important;
    }
    h3, strong {
        color: black !important;
    }
    button, a {
        display: none !important;
    }
}
</style>

<?php include 'layouts/footer.php'; ?>