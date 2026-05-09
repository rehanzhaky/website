<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$slug_seksi = $_GET['seksi'] ?? '';
$nama_seksi = str_replace('_', ' ', $slug_seksi);

if (empty($slug_seksi)) {
    header("Location: e_performance.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bulan         = $_POST['bulan']; // Berupa angka
    $tahun         = $_POST['tahun'];
    $judul_laporan = trim($_POST['judul_laporan']);
    $keterangan    = trim($_POST['keterangan']);

    // Cek duplikat biar nggak double upload di bulan yang sama
    $cek_duplikat = $pdo->prepare("SELECT id FROM e_performance WHERE seksi = ? AND bulan = ? AND tahun = ?");
    $cek_duplikat->execute([$nama_seksi, $bulan, $tahun]);
    
    if ($cek_duplikat->fetch()) {
        $error = "Waduh! Laporan untuk bulan ini sudah pernah diunggah sebelumnya.";
    } else {
        if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
            $allowed_ext = ['pdf'];
            $file_name   = $_FILES['file_laporan']['name'];
            $file_tmp    = $_FILES['file_laporan']['tmp_name'];
            $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = "EPerf_" . $slug_seksi . "_" . $bulan . "_" . $tahun . "_" . time() . "." . $file_ext;
                $upload_dir = 'uploads/eperformance/';
                
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    $sql = "INSERT INTO e_performance (seksi, bulan, tahun, judul_laporan, keterangan, nama_file) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$nama_seksi, $bulan, $tahun, $judul_laporan, $keterangan, $new_file_name])) {
                        echo "<script>alert('Laporan berhasil diunggah! ✅'); window.location.href='e_performance.php?seksi=$slug_seksi';</script>";
                        exit;
                    } else {
                        $error = "Gagal menyimpan ke database.";
                    }
                } else {
                    $error = "Gagal memindahkan file ke server.";
                }
            } else {
                $error = "Format file ditolak! Hanya boleh file PDF.";
            }
        } else {
            $error = "Pilih file PDF terlebih dahulu!";
        }
    }
}

$list_bulan = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 
    5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 
    9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Unggah E-Performance</h2>
        <p>Seksi: <strong><?= htmlspecialchars($nama_seksi) ?></strong></p>
    </div>
    <a href="detail_eperformance.php?seksi=<?= $slug_seksi ?>" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali</a>
</div>

<div class="glass panel-utama" style="margin: 0 auto;">
    
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.2); color: #ff4c4c; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: bold; text-align: center;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group form-full" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Bulan Laporan</label>
                    <select name="bulan" required>
                        <option value="" disabled selected>-- Pilih Bulan --</option>
                        <?php foreach ($list_bulan as $angka => $nama): ?>
                            <option value="<?= $angka ?>" <?= ($angka == date('n')) ? 'selected' : '' ?>><?= $nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Tahun Laporan</label>
                    <input type="number" name="tahun" value="<?= date('Y') ?>" required>
                </div>
            </div>

            <div class="form-group form-full">
                <label>Judul Laporan</label>
                <input type="text" name="judul_laporan" placeholder="Contoh: Laporan Tarja Bulanan..." required autocomplete="off">
            </div>

            <div class="form-group form-full">
                <label>Keterangan Tambahan (Opsional)</label>
                <input type="text" name="keterangan" placeholder="..." autocomplete="off">
            </div>

            <div class="form-group form-full">
                <label>File Dokumen (PDF)</label>
                <input type="file" name="file_laporan" accept=".pdf" required style="width: 100%; color: white;">
            </div>
        </div>

        <div style="text-align: right; margin-top: 25px;">
            <button type="submit" class="btn-shortcut" style="width: 100%;">🚀 Unggah Laporan Kinerja</button>
        </div>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>