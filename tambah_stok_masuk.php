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
    $id_stok    = (int)($_POST['id_stok'] ?? 0);
    $jumlah     = (int)($_POST['jumlah'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');

    if ($id_stok <= 0 || $jumlah <= 0) {
        $error = "Pilih item dan isi jumlah stok yang valid (lebih dari 0)!";
    } else {
        try {
            $pdo->beginTransaction();

            $upd = $pdo->prepare("UPDATE stok_persediaan_atk SET stok_masuk = stok_masuk + ?, jumlah = jumlah + ? WHERE id = ?");
            $upd->execute([$jumlah, $jumlah, $id_stok]);

            $log_ket = $keterangan !== '' ? $keterangan : 'Pengadaan / penambahan stok manual';
            $pdo->prepare("INSERT INTO mutasi_stok_atk (id_stok, jenis, jumlah, keterangan, id_user) VALUES (?, 'masuk', ?, ?, ?)")
                ->execute([$id_stok, $jumlah, $log_ket, $_SESSION['user_id']]);

            $pdo->commit();
            echo "<script>alert('Stok masuk berhasil dicatat! ✅'); window.location.href='stok_persediaan.php';</script>";
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}

$daftar_item = $pdo->query("SELECT id, nama_barang, satuan, jumlah FROM stok_persediaan_atk ORDER BY nama_barang ASC")->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Tambah Stok Masuk 📥</h2>
        <p style="color: var(--text-secondary);">Catat pengadaan atau penambahan stok ATK secara manual.</p>
    </div>
    <a href="stok_persediaan.php" style="color: var(--text-primary); text-decoration: none; opacity: 0.8; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 30px; font-size: 13px; font-weight: bold; background: var(--bg-secondary);">← Kembali</a>
</div>

<div class="glass panel-utama">
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.3); color: #dc2626; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if (count($daftar_item) === 0): ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 50px; margin-bottom: 15px;">📭</div>
            <p style="color: var(--text-secondary); margin-bottom: 20px;">Belum ada item ATK terdaftar di sistem. Daftarkan item terlebih dahulu sebelum menambah stok masuk.</p>
            <a href="tambah_item_atk.php" class="btn-shortcut" style="width: auto; padding: 12px 25px; display: inline-block;">+ Tambah Item Baru</a>
        </div>
    <?php else: ?>
        <form action="" method="POST">
            <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Informasi Stok Masuk</h3>

            <div class="form-grid">
                <div class="form-group form-full">
                    <label>Pilih Item ATK</label>
                    <select name="id_stok" required>
                        <option value="" disabled selected>-- Pilih barang yang stoknya ditambah --</option>
                        <?php foreach ($daftar_item as $item): ?>
                            <option value="<?= $item['id'] ?>">
                                <?= htmlspecialchars($item['nama_barang']) ?> (stok saat ini: <?= (int)$item['jumlah'] ?> <?= htmlspecialchars($item['satuan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah Masuk</label>
                    <input type="number" name="jumlah" min="1" placeholder="Contoh: 50" required>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="text" value="<?= date('d M Y') ?>" disabled>
                </div>

                <div class="form-group form-full">
                    <label>Keterangan (opsional)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Pengadaan bulan Mei 2026 / Sumbangan dari kantor pusat" autocomplete="off">
                </div>
            </div>

            <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 20px;">
                <button type="submit" class="btn-shortcut" style="width: 220px;">💾 Simpan Stok Masuk</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'layouts/footer.php'; ?>
