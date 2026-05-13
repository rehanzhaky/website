<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// ========================================================
// LOGIKA HAK AKSES (ROLE-BASED ACCESS CONTROL)
// ========================================================
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
$bisa_edit = in_array($role_saat_ini, ['admin_utama', 'tu_keuangan']);

$mulai_tanggal  = $_GET['mulai_tanggal'] ?? date('Y-m-01'); 
$sampai_tanggal = $_GET['sampai_tanggal'] ?? date('Y-m-t'); 
$filter_seksi   = $_GET['seksi'] ?? '';

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h2>Realisasi Anggaran Per Bidang 🏛️</h2>
        <p>Manajemen pemantauan serapan anggaran masing-masing seksi.</p>
    </div>
    
    <?php if ($bisa_edit): ?>
        <a href="tambah_realisasi.php" class="btn-shortcut" style="padding: 12px 20px; width: auto; font-size: 13px; background: #ffffff; color: #0a192f; text-decoration: none; border-radius: 8px; font-weight: bold;">
            + Tambah Laporan Realisasi
        </a>
    <?php endif; ?>
</div>

<div class="glass panel-utama" style="padding: 30px;">
    
    <form method="GET" action="" style="margin-bottom: 35px;">
        <div style="display: flex; align-items: flex-end; gap: 15px;">
            <div style="flex: 1;">
                <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); font-weight: 600; text-transform: lowercase;">tanggal mulai</label>
                <input type="date" name="mulai_tanggal" class="input-full-width" value="<?= $mulai_tanggal ?>" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px 15px; border-radius: 10px;">
            </div>
            <div style="flex: 1;">
                <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); font-weight: 600; text-transform: lowercase;">tanggal akhir</label>
                <input type="date" name="sampai_tanggal" class="input-full-width" value="<?= $sampai_tanggal ?>" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px 15px; border-radius: 10px;">
            </div>
            <div style="flex: 1.5;">
                <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); font-weight: 600; text-transform: lowercase;">seksi / bidang</label>
                <select name="seksi" class="input-full-width" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px 15px; border-radius: 10px; font-size: 14px;">
                    <option value="" style="color: black;">-- semua seksi --</option>
                    <option value="LANTASKIM" style="color: black;" <?= $filter_seksi == 'LANTASKIM' ? 'selected' : '' ?>>LANTASKIM</option>
                    <option value="INTALTUSKIM" style="color: black;" <?= $filter_seksi == 'INTALTUSKIM' ? 'selected' : '' ?>>INTALTUSKIM</option>
                    <option value="INTELDAKIM" style="color: black;" <?= $filter_seksi == 'INTELDAKIM' ? 'selected' : '' ?>>INTELDAKIM</option>
                    <option value="PEMERIKSAAN KEIMIGRASIAN (TPI)" style="color: black;" <?= $filter_seksi == 'PEMERIKSAAN KEIMIGRASIAN (TPI)' ? 'selected' : '' ?>>PEMERIKSAAN KEIMIGRASIAN (TPI)</option>
                    <option value="URUSAN UMUM" style="color: black;" <?= $filter_seksi == 'URUSAN UMUM' ? 'selected' : '' ?>>URUSAN UMUM</option>
                    <option value="TIKIM" style="color: black;" <?= $filter_seksi == 'TIKIM' ? 'selected' : '' ?>>TIKIM</option>
                    <option value="LAYANAN PERKANTORAN" style="color: black;" <?= $filter_seksi == 'LAYANAN PERKANTORAN' ? 'selected' : '' ?>>LAYANAN PERKANTORAN</option>
                    <option value="BELANJA MODAL" style="color: black;" <?= $filter_seksi == 'BELANJA MODAL' ? 'selected' : '' ?>>BELANJA MODAL</option>
                    <option value="URUSAN KEPEGAWAIAN" style="color: black;" <?= $filter_seksi == 'URUSAN KEPEGAWAIAN' ? 'selected' : '' ?>>URUSAN KEPEGAWAIAN</option>
                    <option value="URUSAN KEUANGAN" style="color: black;" <?= $filter_seksi == 'URUSAN KEUANGAN' ? 'selected' : '' ?>>URUSAN KEUANGAN</option>
                    <option value="LAYANAN MANAJEMEN KINERJA INTERNAL" style="color: black;" <?= $filter_seksi == 'LAYANAN MANAJEMEN KINERJA INTERNAL' ? 'selected' : '' ?>>LAYANAN MANAJEMEN KINERJA INTERNAL</option>
                </select>
            </div>
            <div>
                <button type="submit" style="padding: 13px 25px; background: #0a1128; color: #ffffff; border: none; border-radius: 30px; font-weight: bold; cursor: pointer; text-transform: lowercase; font-size: 14px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    terapkan filter 🔍
                </button>
            </div>
        </div>
    </form>

    <div class="panel-tabel">
        <table class="table-minimal" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="width: 5%; text-align: center; padding: 15px; color: #ffffff; opacity: 0.8;">No.</th>
                    <th style="width: 15%; padding: 15px; color: #ffffff; opacity: 0.8;">Tgl. Laporan</th>
                    <th style="width: 25%; padding: 15px; color: #ffffff; opacity: 0.8;">Seksi / Bidang</th>
                    <th style="width: 25%; padding: 15px; color: #ffffff; opacity: 0.8;">Keterangan</th>
                    <th style="width: 15%; text-align: right; padding: 15px; color: #ffffff; opacity: 0.8;">Total Realisasi</th>
                    <th style="width: 15%; text-align: center; padding: 15px; color: #ffffff; opacity: 0.8;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT m.*, 
                               (SELECT SUM(realisasi) FROM laporan_realisasi_bidang_detail d WHERE d.id_laporan = m.id) AS grand_realisasi
                        FROM laporan_realisasi_bidang m 
                        WHERE m.tanggal_laporan BETWEEN :mulai AND :sampai ";
                
                $params = [
                    'mulai' => $mulai_tanggal, 
                    'sampai' => $sampai_tanggal
                ];

                if (!empty($filter_seksi)) {
                    $sql .= " AND m.seksi = :seksi ";
                    $params['seksi'] = $filter_seksi;
                }

                $sql .= " ORDER BY m.tanggal_laporan DESC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $data = $stmt->fetchAll();

                if (!$data): 
                ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 50px; opacity: 0.5; color: #ffffff;">
                            Belum ada laporan di periode atau seksi ini...
                        </td>
                    </tr>
                <?php 
                else: 
                    $no = 1;
                    foreach ($data as $row):
                ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="text-align: center; padding: 15px; color: #ffffff;"><?= $no++ ?></td>
                        <td style="padding: 15px; color: #ffffff;"><?= date('d/m/Y', strtotime($row['tanggal_laporan'])) ?></td>
                        <td style="padding: 15px; color: #ffffff; font-weight: 500; letter-spacing: 0.5px;"><?= htmlspecialchars($row['seksi']) ?></td>
                        <td style="padding: 15px; color: rgba(255,255,255,0.8);"><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td style="text-align: right; color: #ffffff; padding: 15px; font-weight: 500; letter-spacing: 0.5px;">Rp <?= number_format($row['grand_realisasi'] ?: 0, 0, ',', '.') ?></td>
                        <td style="padding: 15px;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <a href="detail_realisasi.php?id=<?= $row['id'] ?>" style="display: flex; align-items: center; justify-content: center; gap: 5px; padding: 6px 15px; font-size: 11px; color: #ffffff; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; text-decoration: none; font-weight: bold; white-space: nowrap;">
                                    <span style="font-size: 13px; line-height: 1;">👁️</span> detail
                                </a>
                                
                                <?php if ($bisa_edit): ?>
                                    <a href="hapus_realisasi.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus laporan ini?')" style="display: flex; align-items: center; justify-content: center; padding: 6px 15px; font-size: 11px; color: #ff4c4c; background: rgba(255,76,76,0.05); border: 1px solid rgba(255,76,76,0.3); border-radius: 20px; text-decoration: none; font-weight: bold; white-space: nowrap;">
                                        hapus
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php 
                    endforeach; 
                endif; 
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>
