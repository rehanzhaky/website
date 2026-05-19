<?php
session_start();

// Kosongkan semua data session
$_SESSION = [];

// Hapus cookie session di sisi browser supaya login berikutnya benar-benar bersih
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_unset();
session_destroy();

header("Location: login.php");
exit;
?>