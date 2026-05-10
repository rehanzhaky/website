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
    echo "<script>alert('Laporan tidak ditemukan!'); window.location.href='realisasi_anggaran.php';</script>";
    exit;
}

// Bantuan Format Rupiah
function rp($angka) {
    return number_format($angka ?: 0, 0, ',', '.');
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Detail Realisasi Anggaran 🏛️</h2>
    <p><?= htmlspecialchars($laporan['keterangan']) ?></p>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <a href="realisasi_anggaran.php?tab=<?= $laporan['tipe_laporan'] ?>" class="btn-navy-pill" style="background: rgba(255,255,255,0.05); color: white; border-color: rgba(255,255,255,0.2);">
                ⬅️ Kembali
            </a>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <a href="ekspor_pdf.php?id=<?= $id_laporan ?>" target="_blank" class="btn-navy-pill" style="text-decoration: none; background: #ff4c4c; color: white; border: none;">
                🖨️ Cetak / PDF
            </a>
            <a href="ekspor_excel.php?id=<?= $id_laporan ?>" class="btn-navy-pill" style="border-color: #50fa7b; color: #50fa7b; text-decoration: none; background: rgba(80, 250, 123, 0.1);">
                📊 Excel
            </a>
        </div>
    </div>

    <div style="margin-bottom: 20px; font-size: 14px; opacity: 0.9; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; border-left: 4px solid #64ffda;">
        📅 <strong>Tanggal Laporan:</strong> <?= date('d M Y', strtotime($laporan['tanggal_laporan'])) ?> &nbsp; | &nbsp; 
        🏷️ <strong>Kategori:</strong> <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? 'REKAP JENIS BELANJA' : 'REKAP SUMBER DANA' ?>
    </div>

    <div class="panel-tabel">
        <table class="table-minimal" style="border: 1px solid rgba(255,255,255,0.1); width: 100%;">
            <thead>
                <tr style="background: rgba(255,255,255,0.05);">
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2); width: 5%;">NO</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2); width: 25%;">
                        <?= $laporan['tipe_laporan'] == 'jenis_belanja' ? 'Kode | Nama Satker' : '(Kode) Sumber Dana' ?>
                    </th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2); width: 10%;">Keterangan</th>
                    <th colspan="3" style="text-align: center; border: 1px solid rgba(255,255,255,0.2);">Jenis Belanja</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.2); width: 15%;">Total</th>
                </tr>
                <tr style="background: rgba(255,255,255,0.05);">
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 15%;">Pegawai</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 15%;">Barang</th>
                    <th style="text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 15%;">Modal</th>
                </tr>
            </thead>
            
            <tbody>
                <?php if ($laporan['tipe_laporan'] == 'jenis_belanja'): 
                    // KALKULASI JENIS BELANJA
                    $tot_pagu = $laporan['pagu_pegawai'] + $laporan['pagu_barang'] + $laporan['pagu_modal'];
                    $tot_realisasi = $laporan['realisasi_pegawai'] + $laporan['realisasi_barang'] + $laporan['realisasi_modal'];
                    $tot_sisa = $laporan['sisa_pegawai'] + $laporan['sisa_barang'] + $laporan['sisa_modal'];
                ?>
                    <tr>
                        <td rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.1);">1</td>
                        <td rowspan="2" style="vertical-align: middle; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #64ffda;">
                            692823 | KANTOR IMIGRASI KELAS I TPI TANJUNG PINANG
                        </td>
                        <td style="border: 1px solid rgba(255,255,255,0.1); text-align: center; font-weight: bold;">
                            PAGU <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin: 5px 0;"> REALISASI
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['pagu_pegawai']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['realisasi_pegawai']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['pagu_barang']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['realisasi_barang']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['pagu_modal']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['realisasi_modal']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #64ffda;">
                            <?= rp($tot_pagu) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin: 5px 0;"> <?= rp($tot_realisasi) ?>
                        </td>
                    </tr>
                    <tr style="background: rgba(255, 76, 76, 0.05);">
                        <td style="border: 1px solid rgba(255,255,255,0.1); text-align: center; font-weight: bold; color: #ffb86c;">SISA</td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['sisa_pegawai']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['sisa_barang']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['sisa_modal']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #ff4c4c;"><?= rp($tot_sisa) ?></td>
                    </tr>

                <?php else: 
                    // KALKULASI SUMBER DANA (RM & PNBP)
                    $rm_tot_p = $laporan['rm_pagu_pegawai'] + $laporan['rm_pagu_barang'] + $laporan['rm_pagu_modal'];
                    $rm_tot_r = $laporan['rm_realisasi_pegawai'] + $laporan['rm_realisasi_barang'] + $laporan['rm_realisasi_modal'];
                    $rm_tot_s = $laporan['rm_sisa_pegawai'] + $laporan['rm_sisa_barang'] + $laporan['rm_sisa_modal'];

                    $pnbp_tot_p = $laporan['pnbp_pagu_pegawai'] + $laporan['pnbp_pagu_barang'] + $laporan['pnbp_pagu_modal'];
                    $pnbp_tot_r = $laporan['pnbp_realisasi_pegawai'] + $laporan['pnbp_realisasi_barang'] + $laporan['pnbp_realisasi_modal'];
                    $pnbp_tot_s = $laporan['pnbp_sisa_pegawai'] + $laporan['pnbp_sisa_barang'] + $laporan['pnbp_sisa_modal'];
                ?>
                    <tr>
                        <td rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.1);">1</td>
                        <td rowspan="2" style="vertical-align: middle; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #c4b5fd;">
                            (A) RUPIAH MURNI
                        </td>
                        <td style="border: 1px solid rgba(255,255,255,0.1); text-align: center; font-weight: bold;">
                            PAGU <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin: 5px 0;"> REALISASI
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['rm_pagu_pegawai']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['rm_realisasi_pegawai']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['rm_pagu_barang']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['rm_realisasi_barang']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['rm_pagu_modal']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['rm_realisasi_modal']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #c4b5fd;">
                            <?= rp($rm_tot_p) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin: 5px 0;"> <?= rp($rm_tot_r) ?>
                        </td>
                    </tr>
                    <tr style="background: rgba(255, 76, 76, 0.05);">
                        <td style="border: 1px solid rgba(255,255,255,0.1); text-align: center; font-weight: bold; color: #ffb86c;">SISA</td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['rm_sisa_pegawai']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['rm_sisa_barang']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['rm_sisa_modal']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #ff4c4c;"><?= rp($rm_tot_s) ?></td>
                    </tr>

                    <tr>
                        <td rowspan="2" style="text-align: center; vertical-align: middle; border: 1px solid rgba(255,255,255,0.1);">2</td>
                        <td rowspan="2" style="vertical-align: middle; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #67e8f9;">
                            (D) PENERIMAAN NEGARA BUKAN PAJAK
                        </td>
                        <td style="border: 1px solid rgba(255,255,255,0.1); text-align: center; font-weight: bold;">
                            PAGU <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin: 5px 0;"> REALISASI
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['pnbp_pagu_pegawai']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['pnbp_realisasi_pegawai']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['pnbp_pagu_barang']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['pnbp_realisasi_barang']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1);">
                            <?= rp($laporan['pnbp_pagu_modal']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 5px 0;"> <?= rp($laporan['pnbp_realisasi_modal']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #67e8f9;">
                            <?= rp($pnbp_tot_p) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.2); margin: 5px 0;"> <?= rp($pnbp_tot_r) ?>
                        </td>
                    </tr>
                    <tr style="background: rgba(255, 76, 76, 0.05);">
                        <td style="border: 1px solid rgba(255,255,255,0.1); text-align: center; font-weight: bold; color: #ffb86c;">SISA</td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['pnbp_sisa_pegawai']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['pnbp_sisa_barang']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); color: #ffb86c;"><?= rp($laporan['pnbp_sisa_modal']) ?></td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.1); font-weight: bold; color: #ff4c4c;"><?= rp($pnbp_tot_s) ?></td>
                    </tr>

                    <tr style="background: rgba(100, 255, 218, 0.1);">
                        <td colspan="2" style="text-align: center; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #64ffda; font-size: 16px; padding: 15px;">
                            GRAND TOTAL
                        </td>
                        <td style="border: 1px solid rgba(255,255,255,0.2); text-align: center; font-weight: bold; color: #64ffda;">
                            PAGU <hr style="border:0; border-top:1px solid rgba(255,255,255,0.3); margin: 5px 0;"> REALISASI
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: bold; color: #64ffda;">
                            <?= rp($rm_tot_p + $pnbp_tot_p) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.3); margin: 5px 0;"> <?= rp($rm_tot_r + $pnbp_tot_r) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: bold; color: #64ffda;">
                            <?= rp($laporan['rm_pagu_barang'] + $laporan['pnbp_pagu_barang']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.3); margin: 5px 0;"> <?= rp($laporan['rm_realisasi_barang'] + $laporan['pnbp_realisasi_barang']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: bold; color: #64ffda;">
                            <?= rp($laporan['rm_pagu_modal'] + $laporan['pnbp_pagu_modal']) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.3); margin: 5px 0;"> <?= rp($laporan['rm_realisasi_modal'] + $laporan['pnbp_realisasi_modal']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #64ffda; font-size: 16px;">
                            <?= rp($rm_tot_p + $pnbp_tot_p) ?> <hr style="border:0; border-top:1px solid rgba(255,255,255,0.3); margin: 5px 0;"> <?= rp($rm_tot_r + $pnbp_tot_r) ?>
                        </td>
                    </tr>
                    <tr style="background: rgba(255, 76, 76, 0.1);">
                        <td colspan="3" style="border: 1px solid rgba(255,255,255,0.2); text-align: center; font-weight: 900; color: #ff5555; font-size: 16px; padding: 15px;">
                            TOTAL SISA ANGGARAN KESELURUHAN
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: bold; color: #ffb86c; font-size: 14px;">
                            <?= rp($laporan['rm_sisa_pegawai'] + $laporan['pnbp_sisa_pegawai']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: bold; color: #ffb86c; font-size: 14px;">
                            <?= rp($laporan['rm_sisa_barang'] + $laporan['pnbp_sisa_barang']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: bold; color: #ffb86c; font-size: 14px;">
                            <?= rp($laporan['rm_sisa_modal'] + $laporan['pnbp_sisa_modal']) ?>
                        </td>
                        <td style="text-align: right; border: 1px solid rgba(255,255,255,0.2); font-weight: 900; color: #ff5555; font-size: 16px;">
                            <?= rp($rm_tot_s + $pnbp_tot_s) ?>
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>