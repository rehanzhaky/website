<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || (strtolower($_SESSION['role']) !== 'admin' && strtolower($_SESSION['role']) !== 'admin_utama')) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username     = trim($_POST['username']);
    $password     = $_POST['password'];
    $role         = $_POST['role'];
    $seksi        = $_POST['seksi'];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $cek->execute([$username]);
    
    if ($cek->rowCount() > 0) {
        $error = "Waduh, username <b>@$username</b> udah dipakai orang lain. Coba yang lain!";
    } else {
        $sql = "INSERT INTO users (username, password, nama_lengkap, role, seksi) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $password_hash, $nama_lengkap, $role, $seksi])) {
            header("Location: kelola_pengguna.php");
            exit;
        } else {
            $error = "Gagal nyimpen data ke database nih. Coba lagi.";
        }
    }
}

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2>Tambah Pengguna Baru</h2>
        <p>Daftarkan akses akun pegawai baru untuk sistem SITAU.</p>
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
                <input type="text" name="nama_lengkap" placeholder="Contoh: Budi Santoso" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Username (Untuk Login)</label>
                <input type="text" name="username" placeholder="Contoh: budi123" required autocomplete="off">
            </div>

            <div class="form-group form-full">
                <label>Kata Sandi</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>

            <div class="form-group">
                <label>Status Akses (Role)</label>
                <div class="form-group">
                    <label>Hak Akses (Role)</label>
                    <select name="role" required class="input-full-width">
                        <option value="user">User Biasa (Seksi Lain)</option>
                        <option value="tu_keuangan">Admin TU - Bagian Keuangan</option>
                        <option value="tu_kepegawaian">Admin TU - Bagian Kepegawaian</option>
                        <option value="tu_umum">Admin TU - Bagian Umum</option>
                        <option value="admin_utama">Admin Utama (Akses Penuh)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Seksi / Bagian</label>
                <select name="seksi" required>
                    <option value="" disabled selected>-- Pilih Seksi / Bagian --</option>
                    <option value="Tata Usaha">Tata Usaha</option>
                    <option value="TIKKIM">TIKKIM</option>
                    <option value="LANTASKIM">LANTASKIM</option>
                    <option value="INTELDAKIM">INTELDAKIM</option>
                </select>
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 20px;">
            <button type="submit" class="btn-shortcut" style="width: 200px;">Simpan Pengguna</button>
        </div>

    </form>
</div>

<?php include 'layouts/footer.php'; ?>