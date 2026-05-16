<?php 
$role_saat_ini = strtolower($_SESSION['role'] ?? 'user'); 

$current_page = basename($_SERVER['PHP_SELF']);

function is_active($page_array, $current) {
    return in_array($current, $page_array) ? 'active-menu' : '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITUAN PADUKA - Sistem Informasi Penatausahaan Terpadu Kanim Tanjungpinang</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>

    <aside class="sidebar glass" id="sidebar">
        <div style="text-align:center; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.2);">
            <h2 style="letter-spacing: 2px; color: #ffffff;">MENU</h2>
        </div>
        
        <ul class="sidebar-menu">
            <a href="index.php" class="<?= is_active(['index.php'], $current_page) ?>">
                Dashboard
            </a>
            
            <a href="daftar_pengajuan.php" class="<?= is_active(['daftar_pengajuan.php', 'detail_pengajuan.php', 'buat_pengajuan.php', 'cetak.php'], $current_page) ?>">
                Inventaris
            </a>
            
            <a href="daftar_laporan_umum.php" class="<?= is_active(['daftar_laporan_umum.php', 'laporan_umum.php', 'upload_laporan_umum.php'], $current_page) ?>">
                Laporan Umum
            </a>
            
            <a href="daftar_eperformance.php" class="<?= is_active(['daftar_eperformance.php', 'e_performance.php', 'upload_eperformance.php', 'detail_eperformance.php'], $current_page) ?>">
                E-Performance
            </a>

            <a href="pilih_laporan.php" class="<?= is_active(['pilih_laporan.php', 'realisasi_anggaran.php', 'laporan_pnbp.php', 'detail_realisasi.php', 'tambah_realisasi.php', 'tambah_laporan_pnbp.php', 'detail_pnbp.php', 'akses_keuangan.php'], $current_page) ?>">
                Laporan Keuangan
            </a>

            <?php if ($role_saat_ini === 'admin_utama' || $role_saat_ini === 'tu_kepegawaian'): ?>
                <a href="pilih_agenda.php" class="<?= is_active(['pilih_agenda.php', 'agenda_kegiatan.php', 'tambah_agenda.php', 'tambah_kegiatan_kakanim.php', 'tambah_rapat_kakanim.php', 'edit_agenda.php'], $current_page) ?>">
                    Agenda Kakanim
                </a>
            <?php endif; ?>

            <a href="arsip.php" class="<?= is_active(['arsip.php'], $current_page) ?>">
                Arsip
            </a>

            <?php if ($role_saat_ini === 'admin_utama' || $role_saat_ini === 'tu_kepegawaian'): ?>
                <a href="kelola_pengguna.php" class="<?= is_active(['kelola_pengguna.php', 'edit_user.php', 'tambah_user.php'], $current_page) ?>">
                    Kelola Pengguna
                </a>
            <?php endif; ?>
        </ul>
    </aside>

    <div class="main-container">