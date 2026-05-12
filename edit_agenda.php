<?php
@session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

// Cek Role (Cuma Admin yang boleh edit)
$role_saat_ini = strtolower($_SESSION['role'] ?? '');
if (!in_array($role_saat_ini, ['admin_utama', 'tu_keuangan', 'admin'])) {
    echo "<script>alert('Akses Ditolak!'); window.location.href='agenda_kegiatan.php';</script>";
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: agenda_kegiatan.php");
    exit;
}

// Ambil data lama
$stmt = $pdo->prepare("SELECT * FROM agenda_kegiatan WHERE id = ?");
$stmt->execute([$id]);
$agenda = $stmt->fetch();

if (!$agenda) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='agenda_kegiatan.php';</script>";
    exit;
}

// PROSES UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $lokasi = $_POST['lokasi'];
    $keterangan = $_POST['keterangan'];
    $nama_file = $agenda['dokumentasi']; // Default pakai file lama

    // Cek kalau ada upload file baru
    if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] == 0) {
        $target_dir = "uploads/agenda/";
        $ext = pathinfo($_FILES['dokumentasi']['name'], PATHINFO_EXTENSION);
        $nama_file_baru = "agenda_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['dokumentasi']['tmp_name'], $target_dir . $nama_file_baru)) {
            // Hapus file lama kalau ada
            if (!empty($agenda['dokumentasi']) && file_exists($target_dir . $agenda['dokumentasi'])) {
                unlink($target_dir . $agenda['dokumentasi']);
            }
            $nama_file = $nama_file_baru;
        }
    }

    $sql = "UPDATE agenda_kegiatan SET tanggal = ?, nama_kegiatan = ?, lokasi = ?, keterangan = ?, dokumentasi = ? WHERE id = ?";
    $stmt_upd = $pdo->prepare($sql);
    
    if ($stmt_upd->execute([$tanggal, $nama_kegiatan, $lokasi, $keterangan, $nama_file, $id])) {
        echo "<script>alert('Agenda berhasil diperbarui! ✅'); window.location.href='agenda_kegiatan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui agenda.');</script>";
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Edit Agenda Kegiatan ✏️</h2>
    <p style="color: rgba(255,255,255,0.7);">Perbarui rincian kegiatan Kakanim.</p>
</div>

<div class="glass panel-utama" style="padding: 30px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
    <form action="" method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); text-transform: lowercase;">tanggal kegiatan</label>
                <input type="date" name="tanggal" value="<?= $agenda['tanggal'] ?>" class="input-full-width" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px; border-radius: 10px;" required>
            </div>
            <div>
                <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); text-transform: lowercase;">lokasi</label>
                <input type="text" name="lokasi" value="<?= htmlspecialchars($agenda['lokasi']) ?>" class="input-full-width" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px; border-radius: 10px;" required>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); text-transform: lowercase;">nama kegiatan</label>
            <input type="text" name="nama_kegiatan" value="<?= htmlspecialchars($agenda['nama_kegiatan']) ?>" class="input-full-width" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px; border-radius: 10px;" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); text-transform: lowercase;">keterangan</label>
            <textarea name="keterangan" rows="4" class="input-full-width" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 12px; border-radius: 10px;"><?= htmlspecialchars($agenda['keterangan']) ?></textarea>
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display:block; font-size:13px; margin-bottom:8px; color: rgba(255,255,255,0.7); text-transform: lowercase;">ganti dokumentasi (kosongkan jika tidak diganti)</label>
            <input type="file" name="dokumentasi" style="color: #fff; font-size: 13px;">
            <?php if(!empty($agenda['dokumentasi'])): ?>
                <p style="font-size: 11px; margin-top: 5px; color: #64ffda;">File saat ini: <?= $agenda['dokumentasi'] ?></p>
            <?php endif; ?>
        </div>

        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 25px;">
            <a href="agenda_kegiatan.php" style="text-decoration: none; padding: 12px 25px; border-radius: 30px; margin-right: 12px; color: #ffffff; border: 1px solid rgba(255,255,255,0.3); font-size: 13px; font-weight: bold;">batal</a>
            <button type="submit" style="padding: 12px 35px; border-radius: 30px; border: none; cursor: pointer; font-weight: bold; background: #ffffff; color: #0a1128; font-size: 13px; box-shadow: 0 4px 6px rgba(0,0,0,0.2);">💾 simpan perubahan</button>
        </div>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>