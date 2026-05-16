<?php
require_once 'config/koneksi.php';

// Kredensial user baru
$username = 'admin';
$password = 'admin123';
$nama = 'Administrator';
$role = 'admin_utama';
$seksi = 'Semua Seksi';

try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password, nama_lengkap, role, seksi, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$username, $password_hash, $nama, $role, $seksi])) {
        echo "<div style='background: #1e1e1e; color: #4ade80; padding: 30px; font-family: monospace; font-size: 18px;'>
                ✅ <b>USER BERHASIL DIBUAT!</b><br><br>
                Username: <b style='color: #60a5fa;'>$username</b><br>
                Password: <b style='color: #60a5fa;'>$password</b><br>
                Role: <b>$role</b><br><br>
                <a href='login.php' style='color: #fbbf24; font-size: 20px;'>→ Login Sekarang</a>
              </div>";
    }
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "<div style='background: #991b1b; color: #fff; padding: 20px;'>
                ⚠️ Username '$username' sudah ada!<br><br>
                Coba login dengan:<br>
                Username: <b>$username</b><br>
                Password: <b>$password</b>
              </div>";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
