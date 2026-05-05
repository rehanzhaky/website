<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITAU - Sistem Tata Usaha</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>

    <aside class="sidebar glass" id="sidebar">
        <div style="text-align:center; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
            <h2 style="letter-spacing: 2px;">MENU</h2>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="index.php">Dashboard</a></li>
            <?php if ($_SESSION['seksi'] == 'tata_usaha'): ?>
                <li><a href="agenda_kegiatan.php">Kelola Agenda Kegiatan</a></li>
            <?php endif; ?>
            <li><a href="daftar_pengajuan.php">Inventaris</a></li>
            <li><a href="pilih_laporan.php">Laporan Keuangan</a></li>
            <li><a href="laporan_umum.php">Laporan Umum</a></li>
            <li><a href="e-performance.php">E-performance</a></li>
            <?php if ($_SESSION['role'] == 'admin_utama'): ?>
                <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
            <?php endif; ?>
        </ul>
    </aside>

    <div class="main-container">