<nav class="navbar glass">
    <div class="nav-left" style="display:flex; align-items:center; gap:14px;">
        <button id="toggleBtn" style="background:none; border:none; color:#ffffff; cursor:pointer; font-size: 20px;">☰</button>
        <div class="nav-logos" style="display:flex; align-items:center; gap:10px;">
            <img src="assets/image/imigrasi.jpeg" alt="Logo Imigrasi" style="height:42px; width:42px; object-fit:contain; border-radius:8px; background:#ffffff; padding:3px;">
            <img src="assets/image/imigrasi2.jpeg" alt="Logo Imigrasi 2" style="height:42px; width:42px; object-fit:contain; border-radius:8px; background:#ffffff; padding:3px;">
        </div>
    </div>

    <div class="nav-brand">
        SITUAN PADUKA
    </div>
    
    <div class="nav-right header-profile" onclick="toggleDropdown(event)">
        <span style="font-size: 14px; opacity: 0.9; font-weight: bold; color: #ffffff;">
            Halo, <?= $_SESSION['nama_lengkap'] ?? 'Admin' ?> ▼
        </span>

        <div class="profile-dropdown" id="profileDropdown">
            <a href="logout.php" class="dropdown-item">🚪 Keluar Sistem</a>
        </div>
    </div>
</nav>

<script>
function toggleDropdown(e) {
    e.stopPropagation(); 
    document.getElementById('profileDropdown').classList.toggle('show');
}

window.onclick = function() {
    let dropdown = document.getElementById("profileDropdown");
    if (dropdown && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
    }
}
</script>

<main class="content-wrapper">
    <div class="panel-utama glass">