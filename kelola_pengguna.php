<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

$search = $_GET['cari'] ?? '';
$sql = "SELECT id, username, nama_lengkap, role, seksi FROM users WHERE nama_lengkap LIKE :search OR username LIKE :search ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$all_users = $stmt->fetchAll();

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header">
    <h2>Daftar Pengguna Sistem</h2>
    <p>Kelola hak akses, tambah akun, dan pantau data pegawai <strong style="color: #072749;">SITUAN PADUKA</strong> di sini.</p>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
    <form method="GET" action="" class="search-invisible">
        <input type="text" name="cari" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama atau username...">
        <button type="submit" title="Cari">🔍</button>
    </form>

    <a href="tambah_user.php" class="btn-shortcut" style="width: auto; padding: 12px 25px;">+ Tambah Pengguna Baru</a>

</div>

<div class="panel-tabel glass">
    <table class="table-minimal">
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Username</th>
                <th>Seksi / Bagian</th>
                <th>Status Akses</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($all_users) > 0): ?>
                <?php foreach ($all_users as $row): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong></td>
                    <td>
                        <code style="background: var(--bg-secondary); padding: 3px 6px; border-radius: 4px; font-family: monospace; color: var(--text-primary);">
                            @<?= htmlspecialchars($row['username']) ?>
                        </code>
                    </td>
                    <td><?= htmlspecialchars($row['seksi']) ?: 'Belum Diatur' ?></td>
                    <td>
                        <?php if(in_array(strtolower($row['role']), ['admin', 'admin_utama'])): ?>
                            <span class="badge badge-admin" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                ADMINISTRATOR
                            </span>
                        <?php else: ?>
                            <span class="badge badge-user" style="background: var(--bg-secondary); color: var(--text-muted); border: 1px solid var(--border-color);">
                                USER BIASA
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_user.php?id=<?= $row['id'] ?>" style="color: var(--blue-primary); text-decoration: none; font-weight: bold; margin-right: 15px;">Edit</a>
                        <a href="hapus_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin mau hapus akun @<?= htmlspecialchars($row['username']) ?>?')" style="color: #dc2626; text-decoration: none; font-weight: bold;">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        Belum ada data pengguna yang ditemukan.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layouts/footer.php'; ?>