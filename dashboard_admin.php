<?php
session_start();
require_once 'theme.php';

// Cek jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SMAN 1 Pomalaa</title>
    <link rel="stylesheet" href="assets/style/style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
        }

        /* Hero Section */
        .hero-admin {
            position: relative;
            background: url('image/sekolah_dashboard.png') no-repeat center center;
            background-size: cover;
            height: 400px;
            display: flex;
            align-items: center;
            color: white;
            padding: 0 40px;
        }

        .hero-admin::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-admin-content {
            position: relative;
            z-index: 1;
        }

        .hero-admin-content h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .hero-admin-content p {
            font-size: 18px;
            margin: 0;
        }

        .admin-section {
            padding: 50px 20px;
            background-color: #fff;
        }

        .admin-buttons {
            max-width: 1000px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .admin-button {
            background-color: #00589D;
            color: white;
            border: none;
            padding: 20px;
            border-radius: 12px;
            font-size: 18px;
            text-align: center;
            text-decoration: none;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-button:hover {
            background-color: #003f70;
        }

        .admin-button i {
            font-size: 20px;
        }

        /* Tambah Admin (tengah bawah) */
        .add-admin-wrapper {
            text-align: center;
            margin-top: 40px;
        }

        .add-admin-wrapper .admin-button {
            display: inline-flex;
            justify-content: center;
            gap: 10px;
            width: 300px;
            padding: 15px 30px;
        }

        /* Profil */
        .profile-wrapper {
            position: absolute;
            top: 20px;
            right: 30px;
            z-index: 1000;
        }

        .profile-icon {
            background-color: rgba(255, 255, 255, 0.9);
            color: #003366;
            border: none;
            border-radius: 50%;
            padding: 10px;
            font-size: 18px;
            cursor: pointer;
            position: relative;
        }

        .profile-dropdown {
            position: absolute;
            right: 0;
            top: 45px;
            background-color: white;
            color: #003366;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 220px;
            display: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            padding: 10px;
            z-index: 1000;
        }

        .profile-dropdown.active {
            display: block;
        }

        .profile-dropdown p {
            margin: 5px 0;
            font-size: 14px;
        }

        .profile-dropdown .logout-button {
            margin-top: 10px;
            display: block;
            width: 100%;
            padding: 8px;
            text-align: center;
            background-color: #d9534f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .profile-dropdown .logout-button:hover {
            background-color: #c9302c;
        }

        @media (max-width: 600px) {
            .admin-buttons {
                grid-template-columns: 1fr;
            }

            .admin-button {
                flex-direction: column;
                justify-content: center;
                gap: 8px;
            }
        }
    </style>
</head>
<body>

<!-- Profil -->
<div class="profile-wrapper">
    <button class="profile-icon" onclick="toggleProfileDropdown()">
        <i class="fas fa-user-circle"></i>
    </button>
    <div class="profile-dropdown" id="profileDropdown">
        <p><strong><?= $_SESSION['nama']; ?></strong></p>
        <p>Username: <?= $_SESSION['username']; ?></p>
        <p>Role: <?= $_SESSION['role']; ?></p>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</div>

<!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>
                <?php 
                    echo ($_SESSION['role'] === 'super-admin') 
                        ? 'SUPER-ADMIN DASHBOARD' 
                        : 'ADMIN DASHBOARD'; 
                ?>
                <br>SMA NEGERI 1 POMALAA
            </h1>
            <p>Kabupaten Kolaka, Sulawesi Tenggara</p>
        </div>
    </section>

<!-- Tombol Dashboard -->
<section class="admin-section">
    <div class="admin-buttons">
        <a href="admin_berita.php" class="admin-button">
            TAMBAH BERITA <i class="fas fa-plus"></i>
        </a>
        <a href="tambah_prestasi.php" class="admin-button">
            TAMBAH PRESTASI <i class="fas fa-plus"></i>
        </a>
        <a href="admin_ekskul.php" class="admin-button">
            TAMBAH EKSKUL <i class="fas fa-plus"></i>
        </a>
        <a href="tambah_struktur.php" class="admin-button">
            TAMBAH STRUKTUR <i class="fas fa-plus"></i>
        </a>
        <a href="tambah_feedback.php" class="admin-button">
            TAMBAH FEEDBACK <i class="fas fa-plus"></i>
        </a>
        <a href="tambah_jadwal.php" class="admin-button">
            TAMBAH JADWAL UJIAN <i class="fas fa-plus"></i>
        </a>
    </div>

    <!-- Tombol Tambah Admin (Khusus Super-Admin) -->
    <?php if ($_SESSION['role'] === 'super-admin'): ?>
    <div class="add-admin-wrapper">
        <a href="hash.php" class="admin-button">
            TAMBAH ADMIN <i class="fas fa-user-plus"></i>
        </a>
    </div>
    <?php endif; ?>
</section>

<?php include 'partials/footer.php'; ?>

<!-- JS Profil Dropdown -->
<script>
    function toggleProfileDropdown() {
        document.getElementById('profileDropdown').classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('profileDropdown');
        const icon = document.querySelector('.profile-icon');
        if (!dropdown.contains(event.target) && !icon.contains(event.target)) {
            dropdown.classList.remove('active');
        }
    });
</script>

</body>
</html>
