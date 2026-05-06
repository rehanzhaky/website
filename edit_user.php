<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

$id_user = $_GET['id'] ?? null;
if (!$id_user) {
    header("Location: kelola_pengguna.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id_user]);
$user_edit = $stmt->fetch();

if (!$user_edit) {
    header("Location: kelola_pengguna.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username     = trim($_POST['username']);
    $password     = $_POST['password'];
    $role         = $_POST['role'];
    $seksi        = $_POST['seksi'];

    $cek = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $cek->execute([$username, $id_user]);
    
    if ($cek->rowCount() > 0) {
        $error = "Waduh, username <b>@$username</b> udah dipakai orang lain. Coba yang lain!";
    } else {
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username=?, password=?, nama_lengkap=?, role=?, seksi=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $password_hash, $nama_lengkap, $role, $seksi, $id_user]);
        } else {
            $sql = "UPDATE users SET username=?, nama_lengkap=?, role=?, seksi=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $nama_lengkap, $role, $seksi, $id_user]);
        }
        
        header("Location: kelola_pengguna.php");
        exit;
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Edit Data Pengguna</h2>
        <p>Perbarui informasi akun, ganti kata sandi, atau ubah seksi pegawai.</p>
    </div>
    <a href="kelola_pengguna.php" style="color: var(--text-white); text-decoration: none; opacity: 0.8;">← Kembali ke Daftar</a>
</div>

<div class="glass panel-utama">
    
    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 76, 76, 0.1); border: 1px solid rgba(255, 76, 76, 0.2); color: #ff4c4c; padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600;">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        
        <h3 style="margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Informasi Akun</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Pegawai (Lengkap)</label>
                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user_edit['nama_lengkap']) ?>" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Username (Untuk Login)</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user_edit['username']) ?>" required autocomplete="off">
            </div>

            <div class="form-group form-full">
                <label>Kata Sandi Baru <span style="color: rgba(255,255,255,0.4); font-size: 10px; font-weight: normal;">(Kosongkan jika tidak ingin mengubah password)</span></label>
                <input type="password" name="password" placeholder="••••••••">
            </div>

            <div class="form-group">
                <label>Status Akses (Role)</label>
                <select name="role" required>
                    <option value="user" <?= ($user_edit['role'] == 'user') ? 'selected' : '' ?>>User Biasa</option>
                    <option value="admin" <?= (in_array(strtolower($user_edit['role']), ['admin', 'admin_utama'])) ? 'selected' : '' ?>>Administrator</option>
                </select>
            </div>

            <div class="form-group">
                <label>Seksi / Bagian</label>
                <select name="seksi" required>
                    <option value="Tata Usaha" <?= ($user_edit['seksi'] == 'Tata Usaha') ? 'selected' : '' ?>>Tata Usaha</option>
                    <option value="TIKKIM" <?= ($user_edit['seksi'] == 'TIKKIM') ? 'selected' : '' ?>>TIKKIM</option>
                    <option value="LANTASKIM" <?= ($user_edit['seksi'] == 'LANTASKIM') ? 'selected' : '' ?>>LANTASKIM</option>
                    <option value="INTELDAKIM" <?= ($user_edit['seksi'] == 'INTELDAKIM') ? 'selected' : '' ?>>INTELDAKIM</option>
                </select>
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 200px;">Simpan Perubahan</button>
        </div>

    </form>
</div>

<?php include 'layouts/footer.php'; ?>