<?php
session_start();
// Hancurkan semua data session
session_unset();
session_destroy();

// Tendang balik ke halaman login
header("Location: login.php");
exit;
?>