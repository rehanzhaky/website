<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$search = $_GET['cari'] ?? '';
$sql = "SELECT * FROM agenda_kegiatan WHERE nama_kegiatan LIKE :search OR lokasi LIKE :search ORDER BY tanggal DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$data_agenda = $stmt->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Agenda Kegiatan Kakanim</h2>
    <p>Pantau jadwal kegiatan, rapat, dan dokumentasi Kantor Imigrasi di sini.</p>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
    
    <form method="GET" action="" class="search-invisible">
        <input type="text" name="cari" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama kegiatan atau lokasi...">
        <button type="submit" title="Cari">🔍</button>
    </form>

    <a href="tambah_agenda.php" class="btn-shortcut" style="width: auto; padding: 12px 25px;">+ Tambah Agenda</a>

</div>

<div class="panel-tabel glass">
    <table class="table-minimal">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Kegiatan</th>
                <th>Lokasi</th>
                <th>Keterangan</th>
                <th>Dokumentasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($data_agenda) > 0): ?>
                <?php foreach($data_agenda as $row): ?>
                <tr>
                    <td style="white-space: nowrap;"><strong><?= date('d M Y', strtotime($row['tanggal'])) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                    <td><?= htmlspecialchars($row['lokasi']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td>
                        <?php if(!empty($row['dokumentasi'])): ?>
                            <a href="uploads/agenda/<?= $row['dokumentasi'] ?>" target="_blank" style="color: #64ffda; text-decoration: none; font-weight: bold; font-size: 12px;">Lihat Foto</a>
                        <?php else: ?>
                            <span style="color: rgba(255,255,255,0.3); font-size: 12px;">Tidak ada</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; opacity: 0.7;">
                        Belum ada agenda kegiatan yang dicatat.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layouts/footer.php'; ?>