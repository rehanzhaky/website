<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$role = strtolower($_SESSION['role'] ?? '');
$akses_admin = ['admin_utama', 'tu_kepegawaian', 'tu_keuangan', 'tu_umum'];
if (!in_array($role, $akses_admin)) {
    echo "<script>alert('Akses Ditolak!'); window.location.href='stok_persediaan.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $satuan      = trim($_POST['satuan']);
    $stok_awal   = (int)($_POST['stok_awal'] ?? 0);
    $keterangan  = trim($_POST['keterangan'] ?? '');

    if ($nama_barang === '' || $satuan === '') {
        $error = "Nama barang dan satuan wajib diisi!";
    } else {
        try {
            $cek = $pdo->prepare("SELECT id FROM stok_persediaan_atk WHERE LOWER(nama_barang) = LOWER(?)");
            $cek->execute([$nama_barang]);
            if ($cek->fetch()) {
                $error = "Item <b>" . htmlspecialchars($nama_barang) . "</b> sudah terdaftar di stok!";
            } else {
                $pdo->beginTransaction();

                $sql = "INSERT INTO stok_persediaan_atk (nama_barang, satuan, stok_masuk, stok_keluar, jumlah) VALUES (?, ?, ?, 0, ?)";
                $pdo->prepare($sql)->execute([$nama_barang, $satuan, $stok_awal, $stok_awal]);
                $id_stok = $pdo->lastInsertId();

                if ($stok_awal > 0) {
                    $log_ket = $keterangan !== '' ? $keterangan : 'Stok awal pendaftaran item';
                    $pdo->prepare("INSERT INTO mutasi_stok_atk (id_stok, jenis, jumlah, keterangan, id_user) VALUES (?, 'masuk', ?, ?, ?)")
                        ->execute([$id_stok, $stok_awal, $log_ket, $_SESSION['user_id']]);
                }

                $pdo->commit();
                echo "<script>alert('Item ATK berhasil didaftarkan! ✅'); window.location.href='stok_persediaan.php';</script>";
                exit;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Tambah Item ATK 🆕</h2>
        <p style="color: var(--text-secondary);">Daftarkan item ATK baru ke dalam sistem persediaan.</p>
    </div>
    <a href="stok_persediaan.php" style="color: var(--text-primary); text-decoration: none; opacity: 0.8; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 30px; font-size: 13px; font-weight: bold; background: var(--bg-secondary);">← Kembali</a>
</div>

<div class="glass panel-utama">
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.3); color: #dc2626; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Informasi Item</h3>

        <div class="form-grid">
            <div class="form-group form-full">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" placeholder="Contoh: Tisu, Pulpen, Kertas A4..." required autocomplete="off"
                       value="<?= isset($_POST['nama_barang']) ? htmlspecialchars($_POST['nama_barang']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Satuan</label>
                <select name="satuan" required class="input-full-width">
                    <option value="pcs">Pcs</option>
                    <option value="pak">Pak</option>
                    <option value="box">Box</option>
                    <option value="rim">Rim</option>
                    <option value="lusin">Lusin</option>
                    <option value="botol">Botol</option>
                    <option value="buah">Buah</option>
                    <option value="set">Set</option>
                </select>
            </div>

            <div class="form-group">
                <label>Stok Awal</label>
                <input type="number" name="stok_awal" min="0" value="<?= isset($_POST['stok_awal']) ? (int)$_POST['stok_awal'] : 0 ?>" required>
            </div>

            <div class="form-group form-full">
                <label>Keterangan (opsional)</label>
                <input type="text" name="keterangan" placeholder="Contoh: Pengadaan awal periode 2026" autocomplete="off"
                       value="<?= isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : '' ?>">
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 200px;">💾 Simpan Item</button>
        </div>
    </form>
</div>

<?php include 'layouts/footer.php'; ?>
