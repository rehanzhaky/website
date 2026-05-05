<?php
require_once 'config/koneksi.php';

// Kita pakai username 'admin'
$username = 'admin';
$password_mentah = 'admin123';

// Kita enkripsi passwordnya pakai standar keamanan PHP (Bcrypt)
$password_hash = password_hash($password_mentah, PASSWORD_DEFAULT);

// Timpa password yang lama di database pakai password yang udah dienkripsi
$sql = "UPDATE users SET password = ? WHERE username = ?";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$password_hash, $username])) {
    echo "<h2 style='font-family: sans-serif; color: green;'>✅ BERHASIL!</h2>";
    echo "Password buat <b>$username</b> udah di-reset dan dienkripsi dengan benar.<br>";
    echo "Silakan balik ke halaman login dan coba masuk pakai <b>admin123</b>.<br><br>";
    echo "<i>Note: Kalau udah sukses login, hapus file reset_admin.php ini ya!</i>";
} else {
    echo "Waduh, gagal reset nih. Coba cek lagi tabel users kamu ada username 'admin' nggak?";
}
?>