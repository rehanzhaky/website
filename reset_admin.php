<?php
require_once 'config/koneksi.php';

// 1. Tentukan Username yang mau di-upgrade
$target_username = 'praboWOKE'; // Ganti dengan username kamu di database

// 2. (OPSIONAL) Tentukan password baru kalau mau sekalian diganti
$password_baru = '11111111'; 
$password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

try {
    // 3. Update Role, Seksi, dan Password secara aman
    $sql = "UPDATE users SET role = 'admin_utama', seksi = 'Semua Seksi', password = ? WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$password_hashed, $target_username])) {
        echo "<div style='background: #111; color: #50fa7b; padding: 20px; font-family: monospace; font-size: 16px;'>
                ✅ MANTAP BOS!<br><br>
                Akun <b>$target_username</b> berhasil di-upgrade menjadi Admin Utama (Semua Seksi).<br>
                Password telah di-reset menjadi: <b>$password_baru</b> (Sudah di-hash dengan aman!).<br><br>
                <a href='login.php' style='color: #4facfe;'>Coba Login Sekarang →</a>
              </div>";
    } else {
        echo "Gagal mengupdate akun.";
    }
} catch (Exception $e) {
    die("Waduh error: " . $e->getMessage());
}
?>