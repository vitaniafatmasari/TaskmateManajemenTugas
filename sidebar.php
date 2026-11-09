<?php
// =====================================================
// TaskMate - Sidebar
// =====================================================
$halaman_ini = basename($_SERVER['PHP_SELF']);
function nav_aktif(string $halaman, string $sekarang): string
{
    return $halaman === $sekarang ? 'active' : '';
}
?>
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <span class="brand-logo"><i class="bi bi-calendar-check"></i></span>
        <div class="brand-text">
            <span class="brand-name">TaskMate</span>
            <span class="brand-tag">Manage Your Tasks</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= nav_aktif('dashboard.php', $halaman_ini) ?>">
            <i class="bi bi-grid-1x2"></i><span>Dashboard</span>
        </a>
        <a href="tugas.php" class="nav-item <?= nav_aktif('tugas.php', $halaman_ini) ?>">
            <i class="bi bi-list-task"></i><span>Daftar Tugas</span>
        </a>
        <a href="tambah_tugas.php" class="nav-item <?= nav_aktif('tambah_tugas.php', $halaman_ini) ?>">
            <i class="bi bi-plus-square"></i><span>Tambah Tugas</span>
        </a>
        <a href="kalender.php" class="nav-item <?= nav_aktif('kalender.php', $halaman_ini) ?>">
            <i class="bi bi-calendar3"></i><span>Kalender</span>
        </a>
        <a href="profil.php" class="nav-item <?= nav_aktif('profil.php', $halaman_ini) ?>">
            <i class="bi bi-person"></i><span>Profil</span>
        </a>
        <a href="logout.php" class="nav-item nav-logout">
            <i class="bi bi-box-arrow-right"></i><span>Logout</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="quote">"Manage Your Tasks, Achieve Your Goals."</div>
    </div>
</aside>
<div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop"></div>
