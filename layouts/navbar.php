<nav class="navbar glass">
    <div class="nav-left">
        <button id="toggleBtn" style="background:none; border:none; color:#ffffff; cursor:pointer; font-size: 20px;">☰</button>
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