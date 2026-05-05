<nav class="navbar glass">
    <div class="nav-left">
        <button id="toggleBtn" style="background:none; border:none; color:white; cursor:pointer; font-size: 20px;">☰</button>
    </div>
    
    <div class="nav-brand">
        SITAU
    </div>
    
    <!-- BAGIAN INI YANG KITA SULAP JADI DROPDOWN -->
    <div class="nav-right header-profile" onclick="toggleDropdown(event)">
        <span style="font-size: 14px; opacity: 0.9; font-weight: bold;">
            Halo, <?= $_SESSION['nama_lengkap'] ?? 'Admin' ?> ▼
        </span>

        <!-- Isi Dropdown-nya -->
        <div class="profile-dropdown" id="profileDropdown">
            <a href="logout.php" class="dropdown-item">🚪 Keluar Sistem</a>
        </div>
    </div>
</nav>

<!-- JS KECIL BUAT KLIK BUKA/TUTUP -->
<script>
function toggleDropdown(e) {
    e.stopPropagation(); // Biar kliknya ga bocor ke window
    document.getElementById('profileDropdown').classList.toggle('show');
}

// Nutup dropdown kalau user klik sembarangan di luar
window.onclick = function() {
    let dropdown = document.getElementById("profileDropdown");
    if (dropdown && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
    }
}
</script>

<main class="content-wrapper">
    <div class="panel-utama glass">