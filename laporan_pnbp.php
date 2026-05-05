<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 
$tab_aktif      = $_GET['tab'] ?? 'jenis_belanja'; 

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Realisasi PNBP 💰</h2>
    <p>Pantau serapan anggaran berdasarkan Penerimaan Negara Bukan Pajak.</p>
</div>

<div class="glass panel-utama">
    <form method="GET" action="" class="filter-pill-bar">
        <input type="hidden" name="tab" value="<?= $tab_aktif ?>">
        <div class="filter-row-full" style="display: flex; align-items: center; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">tanggal mulai</label>
                <input type="date" name="mulai_tanggal" class="input-full-width" value="<?= $mulai_tanggal ?>" required>
            </div>
            
            <div style="flex: 1;">
                <label style="display:block; font-size:12px; margin-bottom:5px; opacity:0.7;">tanggal akhir</label>
                <input type="date" name="sampai_tanggal" class="input-full-width" value="<?= $sampai_tanggal ?>" required>
            </div>

            <div style="margin-top: 22px;">
                <button type="submit" class="btn-navy-pill">
                    terapkan filter 🔍
                </button>
            </div>
        </div>
    </form>

    <h3 style="margin-top: 35px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 18px;">
            <?= $tab_aktif == 'jenis_belanja' ? '📊 Realisasi Belanja Satker Per Jenis' : '🏢 Realisasi Belanja Satker Per Sumber Dana' ?>
        </span>
        <span class="badge badge-pending" style="font-size: 11px; text-transform: none; font-weight: normal;">
            <?= date('d M Y', strtotime($mulai_tanggal)) ?> - <?= date('d M Y', strtotime($sampai_tanggal)) ?>
        </span>
    </h3>

    <div class="panel-tabel">
        <table class="table-minimal">
            <thead>
                <?php if ($tab_aktif == 'jenis_belanja'): ?>
                    <tr>
                        <th>Kode</th>
                        <th>Jenis Belanja</th>
                        <th>Pagu (Rp)</th>
                        <th>Realisasi (Rp)</th>
                        <th>Sisa (Rp)</th>
                        <th>%</th>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th>Sumber Dana</th>
                        <th>Pagu Alokasi</th>
                        <th>Total Realisasi</th>
                        <th>Sisa Saldo</th>
                        <th>Progres</th>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 50px; opacity: 0.5;">
                        <div style="font-size: 40px; margin-bottom: 10px;">📂</div>
                        Menunggu data realisasi dari database...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<?php include 'layouts/footer.php'; ?>