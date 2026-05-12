<?php
@session_start(); // Jurus senyap anti-notice PHP!
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// ========================================================
// LOGIKA HAK AKSES (Cek siapa yang boleh Edit/Hapus)
// ========================================================
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
// Tambahkan role yang berhak ngelola agenda di array ini
$bisa_edit = in_array($role_saat_ini, ['admin_utama', 'tu_keuangan', 'admin']); 

$search = $_GET['cari'] ?? '';
$sql = "SELECT * FROM agenda_kegiatan WHERE nama_kegiatan LIKE :search OR lokasi LIKE :search ORDER BY tanggal DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$data_agenda = $stmt->fetchAll();

// Panggil header di bawah biar aman
include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px;">
    <div>
        <h2>Agenda Kegiatan Kakanim 📅</h2>
        <p style="color: rgba(255,255,255,0.7);">Pantau jadwal kegiatan, rapat, dan dokumentasi Kantor Imigrasi di sini.</p>
    </div>
    
    <?php if ($bisa_edit): ?>
        <a href="tambah_agenda.php" style="padding: 12px 20px; width: auto; font-size: 13px; background: #ffffff; color: #0a1128; text-decoration: none; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
            + tambah agenda
        </a>
    <?php endif; ?>
</div>

<div class="glass panel-utama" style="padding: 30px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
    
    <form method="GET" action="" style="margin-bottom: 35px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="flex: 1;">
                <input type="text" name="cari" class="input-full-width" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama kegiatan atau lokasi..." style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 13px 20px; border-radius: 30px; box-sizing: border-box;">
            </div>
            <div>
                <button type="submit" style="padding: 13px 30px; background: #ffffff; color: #0a1128; border: none; border-radius: 30px; font-weight: bold; cursor: pointer; text-transform: lowercase; font-size: 13px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                    cari 🔍
                </button>
            </div>
        </div>
    </form>

    <div class="panel-tabel" style="overflow-x: auto;">
        <table class="table-minimal" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.2);">
                    <th style="padding: 15px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 15%;">TANGGAL</th>
                    <th style="padding: 15px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 25%;">NAMA KEGIATAN</th>
                    <th style="padding: 15px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 20%;">LOKASI</th>
                    <th style="padding: 15px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 20%;">KETERANGAN</th>
                    <th style="text-align: center; padding: 15px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 10%;">DOK.</th>
                    
                    <?php if ($bisa_edit): ?>
                        <th style="text-align: center; padding: 15px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 10%;">AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(count($data_agenda) > 0): ?>
                    <?php foreach($data_agenda as $row): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 15px; color: #ffffff; white-space: nowrap;">
                            <strong><?= date('d M Y', strtotime($row['tanggal'])) ?></strong>
                        </td>
                        <td style="padding: 15px; color: #ffffff; font-weight: 500;">
                            <?= htmlspecialchars($row['nama_kegiatan']) ?>
                        </td>
                        <td style="padding: 15px; color: rgba(255,255,255,0.8);">
                            📍 <?= htmlspecialchars($row['lokasi']) ?>
                        </td>
                        <td style="padding: 15px; color: rgba(255,255,255,0.7); font-size: 13px;">
                            <?= htmlspecialchars($row['keterangan']) ?>
                        </td>
                        <td style="text-align: center; padding: 15px;">
                            <?php if(!empty($row['dokumentasi'])): ?>
                                <a href="uploads/agenda/<?= $row['dokumentasi'] ?>" target="_blank" style="color: #64ffda; text-decoration: none; font-weight: bold; font-size: 12px; border: 1px solid rgba(100,255,218,0.3); padding: 5px 10px; border-radius: 20px; background: rgba(100,255,218,0.05);">📷 lihat</a>
                            <?php else: ?>
                                <span style="color: rgba(255,255,255,0.3); font-size: 11px; font-style: italic;">kosong</span>
                            <?php endif; ?>
                        </td>

                        <?php if ($bisa_edit): ?>
                            <td style="padding: 15px;">
                                <div style="display: flex; justify-content: center; gap: 8px;">
                                    <a href="edit_agenda.php?id=<?= $row['id'] ?>" style="display: flex; align-items: center; justify-content: center; padding: 6px 15px; font-size: 11px; color: #ffffff; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; text-decoration: none; font-weight: bold; white-space: nowrap;">
                                        ✏️ edit
                                    </a>
                                    <a href="hapus_agenda.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus agenda kegiatan ini?')" style="display: flex; align-items: center; justify-content: center; padding: 6px 15px; font-size: 11px; color: #ff4c4c; background: rgba(255,76,76,0.05); border: 1px solid rgba(255,76,76,0.3); border-radius: 20px; text-decoration: none; font-weight: bold; white-space: nowrap;">
                                        hapus
                                    </a>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $bisa_edit ? '6' : '5' ?>" style="text-align: center; padding: 50px; opacity: 0.5; color: #ffffff;">
                            Belum ada agenda kegiatan yang dicatat atau ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>