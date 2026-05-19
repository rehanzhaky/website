<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config/koneksi.php';

$jam = (int)date('H');
if ($jam < 12)      $sapaan = "Selamat Pagi";
elseif ($jam < 15)  $sapaan = "Selamat Siang";
elseif ($jam < 18)  $sapaan = "Selamat Sore";
else                $sapaan = "Selamat Malam";

$nama_user = $_SESSION['nama_lengkap'] ?? $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pegawai SITUAN PADUKA';
$role_user = strtolower($_SESSION['role'] ?? 'user');

include 'layouts/header.php';
include 'layouts/navbar.php';
?>

<style>
    .dash-hero{position:relative;overflow:hidden;border-radius:18px;background:linear-gradient(105deg,#0a1f3d 0%,#0f2a52 45%,#16386b 100%);color:#fff;padding:40px 45px;min-height:280px;display:flex;align-items:center;box-shadow:0 10px 30px -10px rgba(10,31,61,.4)}
    .dash-hero::after{content:"";position:absolute;inset:0;background:url('assets/image/kantor-imigrasi.jpg') center right/cover no-repeat;-webkit-mask-image:linear-gradient(90deg,transparent 0%,transparent 40%,#000 75%);mask-image:linear-gradient(90deg,transparent 0%,transparent 40%,#000 75%);opacity:.85;pointer-events:none}
    .dash-hero-inner{position:relative;z-index:2;max-width:560px}
    .dash-hero h1{margin:0 0 14px;font-size:30px;font-weight:700;letter-spacing:.3px;color:#fff}
    .dash-hero p{margin:0 0 22px;font-size:14px;line-height:1.7;color:#cfd8e8;max-width:520px}
    .dash-hero-badge{display:inline-flex;align-items:center;gap:14px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);border-radius:14px;padding:14px 18px;backdrop-filter:blur(6px)}
    .dash-hero-badge-icon{width:42px;height:42px;border-radius:10px;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
    .dash-hero-badge-text{font-size:12px;line-height:1.55;color:#dde6f3}

    .dash-section-title{margin:36px 0 6px;font-size:20px;font-weight:700;color:#0a1f3d}
    .dash-section-sub{margin:0 0 22px;font-size:13px;color:#64748b}

    .dash-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
    .dash-card{background:#fff;border:1px solid #e6ebf2;border-radius:14px;padding:22px;display:flex;gap:16px;align-items:flex-start;text-decoration:none;color:inherit;transition:.25s ease;cursor:pointer}
    .dash-card:hover{transform:translateY(-3px);box-shadow:0 12px 24px -12px rgba(10,31,61,.18);border-color:#cdd6e3}
    .dash-card-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;flex-shrink:0}
    .dash-card h4{margin:0 0 4px;font-size:15px;font-weight:700;color:#0a1f3d}
    .dash-card p{margin:0;font-size:12.5px;line-height:1.55;color:#64748b}

    .ic-biru{background:linear-gradient(135deg,#3b82f6,#2563eb)}
    .ic-hijau{background:linear-gradient(135deg,#22c55e,#16a34a)}
    .ic-ungu{background:linear-gradient(135deg,#a855f7,#7c3aed)}
    .ic-oranye{background:linear-gradient(135deg,#f59e0b,#ea580c)}
    .ic-pink{background:linear-gradient(135deg,#ec4899,#db2777)}
    .ic-cyan{background:linear-gradient(135deg,#06b6d4,#0891b2)}

    .dash-tentang{margin-top:28px;background:#fff;border:1px solid #e6ebf2;border-radius:14px;padding:26px 30px;display:grid;grid-template-columns:1fr 1.4fr;gap:32px;align-items:center}
    .dash-tentang h4{margin:0 0 8px;font-size:16px;font-weight:700;color:#0a1f3d}
    .dash-tentang-desc{font-size:12.5px;line-height:1.65;color:#64748b}
    .dash-fitur{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
    .dash-fitur-item{display:flex;flex-direction:column;align-items:flex-start;gap:6px}
    .dash-fitur-ic{font-size:20px;color:#0a1f3d}
    .dash-fitur-title{font-size:13px;font-weight:700;color:#0a1f3d}
    .dash-fitur-desc{font-size:11.5px;color:#64748b;line-height:1.5}

    .dash-footer{text-align:center;color:#94a3b8;font-size:12px;padding:20px 0 4px}

    @media (max-width: 1100px){
        .dash-grid{grid-template-columns:repeat(2,1fr)}
        .dash-tentang{grid-template-columns:1fr}
        .dash-fitur{grid-template-columns:repeat(2,1fr)}
        .dash-hero::after{opacity:.35}
    }
    @media (max-width: 640px){
        .dash-grid{grid-template-columns:1fr}
        .dash-fitur{grid-template-columns:1fr 1fr}
        .dash-hero{padding:28px 25px}
        .dash-hero h1{font-size:22px}
    }
</style>

<section class="dash-hero">
    <div class="dash-hero-inner">
        <h1><?= htmlspecialchars($sapaan) ?>, <?= htmlspecialchars($nama_user) ?>! 👋</h1>
        <p>SITUAN PADUKA adalah Sistem Informasi Penatausahaan Terpadu Kantor Imigrasi Kelas I TPI Tanjungpinang yang menyediakan layanan internal kantor untuk mendukung pengelolaan data dan informasi secara digital, terintegrasi, dan efisien.</p>
        <div class="dash-hero-badge">
            <div class="dash-hero-badge-icon">👥</div>
            <div class="dash-hero-badge-text">
                Dikembangkan oleh Subbagian Tata Usaha bersama<br>
                Tim Maganghub Kemenaker Batch 2 Tahun 2025
            </div>
        </div>
    </div>
</section>

<h3 class="dash-section-title">Layanan Internal</h3>
<p class="dash-section-sub">Akses cepat ke layanan dan informasi yang tersedia di SITUAN PADUKA</p>

<div class="dash-grid">
    <a href="daftar_pengajuan.php" class="dash-card">
        <div class="dash-card-icon ic-biru">📦</div>
        <div><h4>Inventaris</h4><p>Kelola data inventaris kantor secara terstruktur dan akurat.</p></div>
    </a>
    <a href="daftar_laporan_umum.php" class="dash-card">
        <div class="dash-card-icon ic-hijau">📄</div>
        <div><h4>Laporan Umum</h4><p>Akses dan kelola berbagai laporan umum kantor.</p></div>
    </a>
    <a href="daftar_eperformance.php" class="dash-card">
        <div class="dash-card-icon ic-ungu">📈</div>
        <div><h4>E-Performance</h4><p>Pantau dan kelola kinerja pegawai secara digital.</p></div>
    </a>
    <a href="pilih_laporan.php" class="dash-card">
        <div class="dash-card-icon ic-oranye">💼</div>
        <div><h4>Keuangan</h4><p>Kelola data keuangan dan anggaran kantor.</p></div>
    </a>
    <a href="<?= ($role_user === 'admin_utama' || $role_user === 'tu_kepegawaian') ? 'pilih_agenda.php' : 'javascript:alert(\'Akses Agenda Kakanim terbatas untuk Admin & TU Kepegawaian.\')' ?>" class="dash-card">
        <div class="dash-card-icon ic-pink">📅</div>
        <div><h4>Agenda Kakanim</h4><p>Lihat dan kelola agenda kegiatan Kepala Kantor.</p></div>
    </a>
    <a href="arsip.php" class="dash-card">
        <div class="dash-card-icon ic-cyan">📁</div>
        <div><h4>Arsip</h4><p>Simpan dan kelola arsip dokumen kantor.</p></div>
    </a>
</div>

<div class="dash-tentang">
    <div>
        <h4>Tentang SITUAN PADUKA</h4>
        <p class="dash-tentang-desc">Sistem Informasi Penatausahaan Terpadu Kantor Imigrasi Kelas I TPI Tanjungpinang merupakan solusi digital untuk memudahkan pengelolaan data, informasi, dan layanan internal kantor secara terintegrasi.</p>
    </div>
    <div class="dash-fitur">
        <div class="dash-fitur-item">
            <div class="dash-fitur-ic">🔗</div>
            <div class="dash-fitur-title">Terintegrasi</div>
            <div class="dash-fitur-desc">Data dan informasi terhubung dalam satu sistem.</div>
        </div>
        <div class="dash-fitur-item">
            <div class="dash-fitur-ic">⚡</div>
            <div class="dash-fitur-title">Efisien</div>
            <div class="dash-fitur-desc">Proses kerja lebih cepat dan terstruktur.</div>
        </div>
        <div class="dash-fitur-item">
            <div class="dash-fitur-ic">🔒</div>
            <div class="dash-fitur-title">Aman</div>
            <div class="dash-fitur-desc">Keamanan data terjamin dengan standar terbaik.</div>
        </div>
        <div class="dash-fitur-item">
            <div class="dash-fitur-ic">👥</div>
            <div class="dash-fitur-title">Mendukung Kinerja</div>
            <div class="dash-fitur-desc">Mendukung produktivitas dan pengambilan keputusan.</div>
        </div>
    </div>
</div>

<div class="dash-footer">© <?= date('Y') ?> Kantor Imigrasi Kelas I TPI Tanjungpinang. All rights reserved.</div>

<?php include 'layouts/footer.php'; ?>
