<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// Cek hak akses
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
$bisa_edit = in_array($role_saat_ini, ['admin_utama', 'tu_keuangan', 'admin']);

$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="pilih_laporan.php" style="padding: 10px 18px; background: var(--bg-secondary); color: var(--text-primary); text-decoration: none; border-radius: 10px; font-size: 13px; font-weight: bold; border: 1px solid var(--border-color); transition: all 0.3s;">
            ← kembali
        </a>
        <div>
            <h2>Daftar Laporan PNBP 💰</h2>
            <p style="color: var(--text-secondary);">Manajemen pemantauan Penerimaan Negara Bukan Pajak.</p>
        </div>
    </div>
    
    <div>
        <?php if ($bisa_edit): ?>
            <a href="tambah_laporan_pnbp.php" class="btn-solid-primary" style="padding: 12px 20px; width: auto; font-size: 13px; background: #ffffff; color: #0a1128; text-decoration: none; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                + tambah laporan pnbp
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <form method="GET" action="" style="margin-bottom: 35px;">
        <div style="display: flex; align-items: flex-end; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:13px; margin-bottom:8px; color: var(--text-secondary);">tanggal mulai</label>
                <input type="date" name="mulai_tanggal" class="input-full-width" value="<?= $mulai_tanggal ?>" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); padding: 12px; border-radius: 10px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; font-size:13px; margin-bottom:8px; color: var(--text-secondary);">tanggal akhir</label>
                <input type="date" name="sampai_tanggal" class="input-full-width" value="<?= $sampai_tanggal ?>" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); padding: 12px; border-radius: 10px;">
            </div>
            <div>
                <button type="submit" style="padding: 13px 25px; background: #ffffff; color: #0a1128; border: none; border-radius: 30px; font-weight: bold; cursor: pointer;">
                    terapkan filter 🔍
                </button>
            </div>
        </div>
    </form>

    <div class="panel-tabel">
        <table class="table-minimal" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color);">
                    <th style="width: 5%; text-align: center; padding: 15px; color: var(--text-muted);">NO.</th>
                    <th style="width: 15%; padding: 15px; color: var(--text-muted);">TGL. LAPORAN</th>
                    <th style="width: 30%; padding: 15px; color: var(--text-muted);">KETERANGAN</th>
                    <th style="width: 15%; text-align: right; padding: 15px; color: var(--text-muted);">TOTAL TARGET</th>
                    <th style="width: 15%; text-align: right; padding: 15px; color: var(--text-muted);">TOTAL REALISASI</th>
                    <th style="width: 20%; text-align: center; padding: 15px; color: var(--text-muted);">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php
                /**
                 * KUNCI: Berdasarkan screenshot DB kamu:
                 * Nama kolom target = 'estimasi'
                 * Nama kolom realisasi = 'realisasi'
                 */
                $sql = "SELECT m.*, 
                               (SELECT SUM(estimasi) FROM laporan_pnbp_detail d WHERE d.id_laporan = m.id) AS grand_target,
                               (SELECT SUM(realisasi) FROM laporan_pnbp_detail d WHERE d.id_laporan = m.id) AS grand_realisasi
                        FROM laporan_pnbp m 
                        WHERE m.tanggal_laporan BETWEEN :mulai AND :sampai 
                        ORDER BY m.tanggal_laporan DESC";
                
                try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['mulai' => $mulai_tanggal, 'sampai' => $sampai_tanggal]);
                    $data = $stmt->fetchAll();
                } catch (PDOException $e) {
                    echo "<tr><td colspan='6' style='color:#ff4c4c; text-align:center; padding:20px;'>Database Error! Hubungi Admin.</td></tr>";
                    $data = [];
                }

                if (!$data): 
                ?>
                    <tr><td colspan="6" style="text-align: center; padding: 50px; color: var(--text-muted);">Belum ada laporan PNBP...</td></tr>
                <?php 
                else: 
                    $no = 1;
                    foreach ($data as $row):
                ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="text-align: center; padding: 15px; color: var(--text-primary);"><?= $no++ ?></td>
                        <td style="padding: 15px; color: var(--text-primary);"><?= date('d/m/Y', strtotime($row['tanggal_laporan'])) ?></td>
                        <td style="padding: 15px; font-weight: bold; color: var(--text-primary);"><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td style="text-align: right; padding: 15px; color: var(--text-secondary);">Rp <?= number_format($row['grand_target'] ?: 0, 0, ',', '.') ?></td>
                        <td style="text-align: right; padding: 15px; color: #10b981; font-weight: bold;">Rp <?= number_format($row['grand_realisasi'] ?: 0, 0, ',', '.') ?></td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="detail_pnbp.php?id=<?= $row['id'] ?>" class="btn-navy-pill" style="padding: 6px 15px; font-size: 11px; text-decoration: none;">👁️ detail</a>
                            <?php if ($bisa_edit): ?>
                                <a href="hapus_pnbp.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus laporan ini?')" style="color: #ff4c4c; font-size: 11px; margin-left: 10px; text-decoration: none; font-weight: bold;">hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>