<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$role = strtolower($_SESSION['role'] ?? '');
$akses_admin = ['admin_utama', 'tu_kepegawaian', 'tu_keuangan', 'tu_umum'];
$is_admin = in_array($role, $akses_admin);

$search = trim($_GET['cari'] ?? '');
$sql = "SELECT * FROM stok_persediaan_atk WHERE nama_barang LIKE :s ORDER BY nama_barang ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['s' => "%$search%"]);
$data = $stmt->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Stok Persediaan ATK 📦</h2>
    <p style="color: var(--text-secondary);">Pantau stok masuk, keluar, dan jumlah persediaan ATK kantor.</p>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
    <form method="GET" action="" class="search-invisible">
        <input type="text" name="cari" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama barang...">
        <button type="submit" title="Cari">🔍</button>
    </form>

    <?php if ($is_admin): ?>
        <div style="display: flex; gap: 10px;">
            <a href="tambah_stok_masuk.php" class="btn-shortcut" style="width: auto; padding: 12px 20px; background: #16a34a;">+ Tambah Stok Masuk</a>
            <a href="tambah_item_atk.php" class="btn-shortcut" style="width: auto; padding: 12px 20px;">+ Tambah Item Baru</a>
        </div>
    <?php endif; ?>
</div>

<div class="panel-tabel glass" style="overflow-x: auto; padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
    <table class="table-minimal" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">No</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Nama Barang</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Satuan</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; text-align: center;">Stok Masuk</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; text-align: center;">Stok Keluar</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; text-align: center;">Jumlah</th>
                <th style="padding: 15px; color: var(--text-muted); font-size: 12px; text-transform: uppercase; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data) > 0): $no = 1; foreach ($data as $row):
                $jumlah = (int)$row['jumlah'];
                if ($jumlah <= 0) {
                    $warna_badge = 'rgba(255, 85, 85, 0.15)';
                    $warna_teks = '#ff5555';
                    $label_status = 'HABIS';
                } elseif ($jumlah < 10) {
                    $warna_badge = 'rgba(255, 184, 108, 0.15)';
                    $warna_teks = '#ffb86c';
                    $label_status = 'MENIPIS';
                } else {
                    $warna_badge = 'rgba(80, 250, 123, 0.15)';
                    $warna_teks = '#16a34a';
                    $label_status = 'TERSEDIA';
                }
            ?>
                <tr onmouseover="this.style.background='var(--blue-lighter)'" onmouseout="this.style.background='transparent'" style="transition: 0.2s;">
                    <td style="text-align: center; border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-primary);"><?= $no++ ?></td>
                    <td style="border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-primary);"><strong><?= htmlspecialchars($row['nama_barang']) ?></strong></td>
                    <td style="border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-secondary);"><?= htmlspecialchars($row['satuan']) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid var(--border-color); padding: 15px; color: #16a34a; font-weight: bold;"><?= number_format($row['stok_masuk']) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid var(--border-color); padding: 15px; color: #dc2626; font-weight: bold;"><?= number_format($row['stok_keluar']) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid var(--border-color); padding: 15px; color: var(--text-primary); font-weight: 900; font-size: 15px;"><?= number_format($jumlah) ?></td>
                    <td style="text-align: center; border-bottom: 1px solid var(--border-color); padding: 15px;">
                        <span style="background: <?= $warna_badge ?>; color: <?= $warna_teks ?>; border: 1px solid <?= $warna_teks ?>; font-weight: bold; padding: 5px 10px; border-radius: 6px; font-size: 11px;">
                            <?= $label_status ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 60px; color: var(--text-muted);">
                        <div style="font-size: 40px; margin-bottom: 10px;">📭</div>
                        Belum ada item ATK terdaftar. Klik <strong>+ Tambah Item Baru</strong> untuk mulai mencatat persediaan.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layouts/footer.php'; ?>
