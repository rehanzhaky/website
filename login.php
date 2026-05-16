<?php
session_start();

if (isset($_SESSION['user_id']) || isset($_SESSION['role'])) {
    header("Location: index.php"); 
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SITUAN PADUKA - Sistem Informasi Penatausahaan Terpadu Kanim Tanjungpinang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>

<body class="login-container">
    <ul class="bg-slideshow">
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="bg-overlay"></div>
    
    <div class="login-box">
        <div class="login-header-wrapper">
            <img src="assets/image/logo.png" alt="Logo Imigrasi" class="login-logo-img">
            <div class="login-logo-text">SITUAN PADUKA</div>
        </div>

        <div class="login-subtitle">Sistem Informasi Penatausahaan Terpadu Kanim Tanjungpinang</div>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-error">
                <span style="font-size: 16px;">⚠️</span>
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <div class="form-login-group">
                <label>Username</label>
                <input type="text" name="username" class="input-login" placeholder="Masukkan username admin" required autocomplete="off">
            </div>

            <div class="form-login-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" class="input-login" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">
                Masuk
            </button>
        </form>

        <div style="margin-top: 35px; font-size: 11px; color: rgba(255,255,255,0.2); letter-spacing: 0.5px;">
            &copy; 2026 KANTOR IMIGRASI KELAS I TANJUNGPINANG
        </div>
    </div>

</body>
</html>