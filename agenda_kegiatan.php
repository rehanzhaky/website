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
        <p style="color: var(--text-secondary);">Pantau jadwal kegiatan, rapat, dan dokumentasi Kantor Imigrasi di sini.</p>
    </div>
    
    <?php if ($bisa_edit): ?>
        <a href="pilih_agenda.php" style="padding: 12px 20px; width: auto; font-size: 13px; background: #ffffff; color: #0a1128; text-decoration: none; border-radius: 30px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
            + tambah agenda
        </a>
    <?php endif; ?>
</div>

<div class="glass panel-utama" style="padding: 30px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
    
    <form method="GET" action="" style="margin-bottom: 35px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="flex: 1;">
                <input type="text" name="cari" class="input-full-width" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama kegiatan atau lokasi..." style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary); padding: 13px 20px; border-radius: 30px; box-sizing: border-box;">
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
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 15px; color: var(--text-muted); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 15%;">TANGGAL</th>
                    <th style="padding: 15px; color: var(--text-muted); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 20%;">NAMA KEGIATAN</th>
                    <th style="padding: 15px; color: var(--text-muted); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 10%;">JENIS</th>
                    <th style="padding: 15px; color: var(--text-muted); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 15%;">LOKASI</th>
                    <th style="padding: 15px; color: var(--text-muted); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 20%;">KETERANGAN</th>
                    <th style="text-align: center; padding: 15px; color: var(--text-muted); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 10%;">DOKUMENTASI</th>
                    
                    <?php if ($bisa_edit): ?>
                        <th style="text-align: center; padding: 15px; color: rgba(255,255,255,0.8); font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; width: 10%;">AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(count($data_agenda) > 0): ?>
                    <?php foreach($data_agenda as $row): ?>
                    <tr style="border-bottom: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.background='var(--blue-lighter)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 15px; color: var(--text-primary); white-space: nowrap;">
                            <strong><?= date('d M Y', strtotime($row['tanggal'])) ?></strong>
                        </td>
                        <td style="padding: 15px; color: var(--text-primary); font-weight: 500;">
                            <?= htmlspecialchars($row['nama_kegiatan']) ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php 
                            $jenis = $row['jenis_agenda'] ?? 'umum';
                            if ($jenis == 'kegiatan_kakanim') {
                                echo '<span style="background: rgba(100,255,218,0.1); color: #64ffda; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; border: 1px solid rgba(100,255,218,0.3);">🎯 Kegiatan</span>';
                            } elseif ($jenis == 'rapat_kakanim') {
                                echo '<span style="background: rgba(79,172,254,0.1); color: #4facfe; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; border: 1px solid rgba(79,172,254,0.3);">📋 Rapat</span>';
                            } else {
                                echo '<span style="background: rgba(189,147,249,0.1); color: #bd93f9; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; border: 1px solid rgba(189,147,249,0.3);">📌 Umum</span>';
                            }
                            ?>
                        </td>
                        <td style="padding: 15px; color: var(--text-secondary);">
                            📍 <?= htmlspecialchars($row['lokasi']) ?>
                        </td>
                        <td style="padding: 15px; color: var(--text-secondary); font-size: 13px;">
                            <?= htmlspecialchars($row['keterangan']) ?>
                        </td>
                        <td style="text-align: center; padding: 15px;">
                            <div style="display: flex; flex-direction: column; gap: 5px; align-items: center;">
                                <?php if(!empty($row['dokumentasi_png'])): ?>
                                    <a href="uploads/agenda/<?= $row['dokumentasi_png'] ?>" target="_blank" style="color: #64ffda; text-decoration: none; font-weight: bold; font-size: 11px; border: 1px solid rgba(100,255,218,0.3); padding: 4px 10px; border-radius: 15px; background: rgba(100,255,218,0.05); white-space: nowrap;">📷 PNG</a>
                                <?php endif; ?>
                                <?php if(!empty($row['dokumentasi_pdf'])): ?>
                                    <a href="uploads/agenda/<?= $row['dokumentasi_pdf'] ?>" target="_blank" style="color: #ff6b6b; text-decoration: none; font-weight: bold; font-size: 11px; border: 1px solid rgba(255,107,107,0.3); padding: 4px 10px; border-radius: 15px; background: rgba(255,107,107,0.05); white-space: nowrap;">📄 PDF</a>
                                <?php endif; ?>
                                <?php if(!empty($row['dokumentasi'])): ?>
                                    <a href="uploads/agenda/<?= $row['dokumentasi'] ?>" target="_blank" style="color: #ffd93d; text-decoration: none; font-weight: bold; font-size: 11px; border: 1px solid rgba(255,217,61,0.3); padding: 4px 10px; border-radius: 15px; background: rgba(255,217,61,0.05); white-space: nowrap;">📁 File</a>
                                <?php endif; ?>
                                <?php if(empty($row['dokumentasi']) && empty($row['dokumentasi_png']) && empty($row['dokumentasi_pdf'])): ?>
                                    <span style="color: rgba(255,255,255,0.3); font-size: 11px; font-style: italic;">kosong</span>
                                <?php endif; ?>
                            </div>
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
                        <td colspan="<?= $bisa_edit ? '7' : '6' ?>" style="text-align: center; padding: 50px; opacity: 0.5; color: #ffffff;">
                            Belum ada agenda kegiatan yang dicatat atau ditemukan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>