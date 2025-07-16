<?php
session_start();
$role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
$nama = $_SESSION['nama'] ?? null;

// Definisikan base URL dari aplikasi Anda
$base_url = '/'; // Atau bisa juga: '/nama_folder_project/'

// Deteksi halaman untuk styling navbar
$current_page = basename($_SERVER['PHP_SELF']);
$is_homepage = ($current_page === 'index.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.6;
        color: #333;
        padding-top: 80px; /* Space for fixed navbar */
    }

    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .navbar.blue-theme {
        background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
    }

    .navbar.white-theme {
        background: white;
        border-bottom: 1px solid #e0e0e0;
    }

    .navbar .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 80px;
    }

    .nav-brand {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: transform 0.3s ease;
    }

    .nav-brand:hover {
        transform: scale(1.02);
    }

    .nav-brand img {
        height: 45px;
        width: 45px;
        margin-right: 15px;
        border-radius: 8px;
        object-fit: cover;
    }

    .nav-brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .blue-theme .nav-brand-text {
        color: white;
    }

    .white-theme .nav-brand-text {
        color: #2c3e50;
    }

    .nav-brand-text .school-name {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .nav-brand-text .school-subtitle {
        font-size: 0.75rem;
        font-weight: 400;
        opacity: 0.8;
    }

    .nav-main {
        display: flex;
        align-items: center;
        gap: 0;
    }

    .nav-links {
        display: flex;
        list-style: none;
        align-items: center;
        gap: 0;
        margin-right: 2rem;
    }

    .nav-links li {
        position: relative;
    }

    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
        border-radius: 0;
        position: relative;
    }

    .blue-theme .nav-item {
        color: white;
    }

    .white-theme .nav-item {
        color: #2c3e50;
    }

    .nav-item:hover {
        background: rgba(255,255,255,0.1);
    }

    .white-theme .nav-item:hover {
        background: #f8f9fa;
    }

    .nav-item .nav-icon {
        font-size: 1.2rem;
        margin-bottom: 0.3rem;
    }

    .nav-item .nav-text {
        font-size: 0.85rem;
        font-weight: 500;
    }

    .nav-item .dropdown-arrow {
        font-size: 0.7rem;
        margin-left: 0.3rem;
        transition: transform 0.3s ease;
    }

    .dropdown:hover .dropdown-arrow {
        transform: rotate(180deg);
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        min-width: 200px;
        border-radius: 8px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateX(-50%) translateY(-10px);
        transition: all 0.3s ease;
        list-style: none;
        padding: 0.5rem 0;
        border: 1px solid #e0e0e0;
    }

    .dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }

    .dropdown-menu li {
        padding: 0;
    }

    .dropdown-menu a {
        color: #333;
        padding: 0.7rem 1rem;
        display: block;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .dropdown-menu a:hover {
        background: #f8f9fa;
        color: #4A90E2;
        transform: translateX(5px);
    }

    .dropdown-menu .dropdown-icon {
        width: 16px;
        margin-right: 0.5rem;
    }

    .profile-section {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .profile-dropdown {
        position: relative;
    }

    .profile-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .blue-theme .profile-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .white-theme .profile-icon {
        background: #f8f9fa;
        color: #2c3e50;
    }

    .profile-icon:hover {
        transform: scale(1.1);
    }

    .profile-menu {
        position: absolute;
        top: 50px;
        right: 0;
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        color: #333;
        width: 250px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
    }

    .profile-menu.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-info {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .profile-info .profile-name {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .profile-info .profile-detail {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 0.3rem;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.7rem;
        background: #e74c3c;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .blue-theme .mobile-menu-toggle {
        color: white;
    }

    .white-theme .mobile-menu-toggle {
        color: #2c3e50;
    }

    .mobile-menu-toggle:hover {
        background: rgba(255,255,255,0.1);
    }

    .white-theme .mobile-menu-toggle:hover {
        background: #f8f9fa;
    }

    .mobile-menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 320px;
        height: 100vh;
        background: white;
        padding: 1rem;
        transition: right 0.3s ease;
        z-index: 1001;
        overflow-y: auto;
        border-left: 1px solid #e0e0e0;
    }

    .mobile-menu.active {
        right: 0;
    }

    .mobile-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 1rem;
    }

    .mobile-menu-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        color: #666;
        transition: all 0.3s ease;
    }

    .mobile-menu-close:hover {
        background: #f8f9fa;
        color: #333;
    }

    .mobile-nav-links {
        list-style: none;
    }

    .mobile-nav-links li {
        margin-bottom: 0.5rem;
    }

    .mobile-nav-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        text-decoration: none;
        color: #333;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .mobile-nav-item:hover {
        background: #f8f9fa;
        color: #4A90E2;
    }

    .mobile-nav-item .nav-icon {
        font-size: 1.2rem;
        width: 20px;
        text-align: center;
    }

    .mobile-dropdown {
        position: relative;
    }

    .mobile-dropdown-toggle {
        background: none;
        border: none;
        color: #666;
        font-size: 1rem;
        cursor: pointer;
        padding: 0.5rem;
        margin-left: auto;
        transition: all 0.3s ease;
    }

    .mobile-dropdown-toggle.active {
        transform: rotate(180deg);
        color: #4A90E2;
    }

    .mobile-dropdown-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 0.5rem;
    }

    .mobile-dropdown-menu.active {
        max-height: 300px;
    }

    .mobile-dropdown-menu a {
        display: block;
        padding: 0.8rem 1rem;
        color: #666;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .mobile-dropdown-menu a:hover {
        background: white;
        color: #4A90E2;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 999;
    }

    .overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .nav-links {
            display: none;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .nav-brand-text .school-name {
            font-size: 1rem;
        }

        .nav-brand-text .school-subtitle {
            font-size: 0.7rem;
        }

        .nav-brand img {
            height: 35px;
            width: 35px;
        }

        .mobile-menu {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .nav-brand-text .school-name {
            font-size: 0.9rem;
        }

        .nav-brand-text .school-subtitle {
            font-size: 0.65rem;
        }

        .nav-brand img {
            height: 30px;
            width: 30px;
            margin-right: 10px;
        }

        .container {
            padding: 0 0.5rem;
        }
    }
</style>

<nav class="navbar <?= $is_homepage ? 'blue-theme' : 'white-theme' ?>" id="navbar">
    <div class="container">
        <a href="<?= $base_url ?>index.php" class="nav-brand">
            <img src="<?= $base_url ?>assets/image/logo_sekolah.png" alt="Logo SMA Negeri 1 Pomalaa">
            <div class="nav-brand-text">
                <div class="school-name">SMA NEGERI 1 POMALAA</div>
                <div class="school-subtitle">Unggul dalam Prestasi, Berkarakter, dan Berwawasan Global</div>
            </div>
        </a>

        <div class="nav-main">
            <!-- Desktop Navigation -->
            <ul class="nav-links">
                <li class="dropdown">
                    <a href="#" class="nav-item">
                        <div class="nav-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="nav-text">
                            Profil
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= $base_url ?>tentang.php"><i class="fas fa-info-circle dropdown-icon"></i> Tentang</a></li>
                        <li><a href="<?= $base_url ?>visi_misi.php"><i class="fas fa-eye dropdown-icon"></i> Visi Dan Misi</a></li>
                        <li><a href="<?= $base_url ?>akreditasi.php"><i class="fas fa-certificate dropdown-icon"></i> Akreditasi</a></li>
                    </ul>
                </li>
                <li>
                    <a href="<?= $base_url ?>prestasi.php" class="nav-item">
                        <div class="nav-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="nav-text">Prestasi</div>
                    </a>
                </li>
                <li>
                    <a href="<?= $base_url ?>struktural.php" class="nav-item">
                        <div class="nav-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="nav-text">Struktural</div>
                    </a>
                </li>
                <li>
                    <a href="<?= $base_url ?>berita.php" class="nav-item">
                        <div class="nav-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="nav-text">Berita</div>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="nav-item">
                        <div class="nav-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <div class="nav-text">
                            Layanan
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= $base_url ?>jadwal/jadwal_ujian.php"><i class="fas fa-calendar-alt dropdown-icon"></i> Jadwal Ujian</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#footer" class="nav-item">
                        <div class="nav-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="nav-text">Kontak</div>
                    </a>
                </li>
                <?php if ($role === 'admin' || $role === 'super-admin'): ?>
                <li>
                    <a href="<?= $base_url ?>dashboard_admin.php" class="nav-item">
                        <div class="nav-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="nav-text">Kelola</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <!-- Profile Section -->
            <div class="profile-section">
                <?php if ($username): ?>
                    <div class="profile-dropdown">
                        <a href="#" class="profile-icon" id="profileIcon">
                            <i class="fas fa-user-circle"></i>
                        </a>
                        <div class="profile-menu" id="profileMenu">
                            <div class="profile-info">
                                <div class="profile-name"><?= htmlspecialchars($nama); ?></div>
                                <div class="profile-detail">Username: <?= htmlspecialchars($username); ?></div>
                                <div class="profile-detail">Role: <?= htmlspecialchars($role); ?></div>
                            </div>
                            <a href="<?= $base_url ?>logout.php" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
        <div class="nav-brand">
            <img src="<?= $base_url ?>assets/image/logo_sekolah.png" alt="Logo SMA Negeri 1 Pomalaa">
            <div class="nav-brand-text">
                <div class="school-name">SMA NEGERI 1 POMALAA</div>
                <div class="school-subtitle">Unggul dalam Prestasi, Berkarakter, dan Berwawasan Global</div>
            </div>
        </div>
        <button class="mobile-menu-close" id="mobileMenuClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <ul class="mobile-nav-links">
        <li class="mobile-dropdown">
            <a href="#" class="mobile-nav-item">
                <i class="fas fa-user nav-icon"></i>
                <span>Profil</span>
                <button class="mobile-dropdown-toggle">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </a>
            <div class="mobile-dropdown-menu">
                <a href="<?= $base_url ?>tentang.php">Tentang</a>
                <a href="<?= $base_url ?>visi_misi.php">Visi Dan Misi</a>
                <a href="<?= $base_url ?>akreditasi.php">Akreditasi</a>
            </div>
        </li>
        <li>
            <a href="<?= $base_url ?>prestasi.php" class="mobile-nav-item">
                <i class="fas fa-trophy nav-icon"></i>
                <span>Prestasi</span>
            </a>
        </li>
        <li>
            <a href="<?= $base_url ?>struktural.php" class="mobile-nav-item">
                <i class="fas fa-users nav-icon"></i>
                <span>Struktural</span>
            </a>
        </li>
        <li>
            <a href="<?= $base_url ?>berita.php" class="mobile-nav-item">
                <i class="fas fa-newspaper nav-icon"></i>
                <span>Berita</span>
            </a>
        </li>
        <li class="mobile-dropdown">
            <a href="#" class="mobile-nav-item">
                <i class="fas fa-concierge-bell nav-icon"></i>
                <span>Layanan</span>
                <button class="mobile-dropdown-toggle">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </a>
            <div class="mobile-dropdown-menu">
                <a href="<?= $base_url ?>jadwal/jadwal_ujian.php">Jadwal Ujian</a>
            </div>
        </li>
        <li>
            <a href="#footer" class="mobile-nav-item">
                <i class="fas fa-phone nav-icon"></i>
                <span>Kontak</span>
            </a>
        </li>

        <?php if ($role === 'admin' || $role === 'super-admin'): ?>
        <li>
            <a href="<?= $base_url ?>dashboard_admin.php" class="mobile-nav-item">
                <i class="fas fa-cog nav-icon"></i>
                <span>Kelola</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($username): ?>
        <li style="border-top: 1px solid #e0e0e0; margin-top: 1rem; padding-top: 1rem;">
            <div style="padding: 1rem; background: #f8f9fa; border-radius: 8px; margin-bottom: 1rem;">
                <div style="font-weight: 700; color: #2c3e50; margin-bottom: 0.5rem;"><?= htmlspecialchars($nama); ?></div>
                <div style="color: #666; font-size: 0.9rem; margin-bottom: 0.3rem;">Username: <?= htmlspecialchars($username); ?></div>
                <div style="color: #666; font-size: 0.9rem;">Role: <?= htmlspecialchars($role); ?></div>
            </div>
            <a href="<?= $base_url ?>logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<script>
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const overlay = document.getElementById('overlay');

    function openMobileMenu() {
        mobileMenu.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    mobileMenuToggle.addEventListener('click', openMobileMenu);
    mobileMenuClose.addEventListener('click', closeMobileMenu);
    overlay.addEventListener('click', closeMobileMenu);

    // Mobile dropdown toggle
    document.querySelectorAll('.mobile-dropdown-toggle').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = this.closest('.mobile-dropdown');
            const menu = dropdown.querySelector('.mobile-dropdown-menu');
            
            // Close other dropdowns
            document.querySelectorAll('.mobile-dropdown-menu').forEach(otherMenu => {
                if (otherMenu !== menu) {
                    otherMenu.classList.remove('active');
                    otherMenu.parentElement.querySelector('.mobile-dropdown-toggle').classList.remove('active');
                }
            });
            
            menu.classList.toggle('active');
            this.classList.toggle('active');
        });
    });

    // Profile dropdown toggle
    document.addEventListener('click', function(e) {
        const profileIcon = document.getElementById('profileIcon');
        const profileMenu = document.getElementById('profileMenu');

        if (profileIcon && profileMenu) {
            if (profileIcon.contains(e.target)) {
                e.preventDefault();
                profileMenu.classList.toggle('active');
            } else if (!profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        }
    });

    // Close mobile menu when clicking on links
    document.querySelectorAll('.mobile-nav-item').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.parentElement.classList.contains('mobile-dropdown')) {
                closeMobileMenu();
            }
        });
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Prevent dropdown links from closing dropdown
    document.querySelectorAll('.dropdown-menu a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>