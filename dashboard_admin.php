<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

$role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
$nama = $_SESSION['nama'] ?? null;

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/';

$current_dir = dirname($_SERVER['SCRIPT_NAME']);
$is_subfolder = (basename($current_dir) !== '' && basename($current_dir) !== '/');

if ($is_subfolder) {
    $asset_path = '../assets/';
    $page_path = '../';
} else {
    $asset_path = 'assets/';
    $page_path = '';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SMAN 1 Pomalaa</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
        }

        .hero {
            position: relative;
            background: url('<?php echo $asset_path; ?>image/background.png') no-repeat center center;
            background-size: cover;
            background-position: center;
            height: 400px;
            display: flex;
            align-items: center;
            color: white;
            padding: 0 40px;
        }

        .hero::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            margin-left: 75px;
        }

        .hero-content h1 {
            font-size: clamp(24px, 4vw, 36px);
            margin-bottom: 10px;
        }

        .hero-content p {
            font-size: clamp(14px, 2vw, 18px);
            margin: 0;
        }

        .admin-section {
            padding: 50px 20px;
            background-color: #fff;
            max-width: 1200px;
            margin: auto;
        }

        .admin-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .admin-button {
            background-color: #004030;
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
            width: 100%;
            box-sizing: border-box;
            min-height: 80px;
        }

        .admin-button:hover {
            background-color: #007255;
        }

        .admin-button i {
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .admin-button {
                flex-direction: column;
                justify-content: center;
                gap: 8px;
                font-size: 16px;
                padding: 15px;
            }

            .hero-content {
                margin-left: 20px;
            }

            .hero-content h1,
            .hero-content p {
                text-align: center;
            }
        }
    </style>
</head>

<body>

<?php include 'partials/navbar.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h1>
            <?php echo ($_SESSION['role'] === 'super-admin') ? 'SUPER-ADMIN DASHBOARD' : 'ADMIN DASHBOARD'; ?>
            <br>SMA NEGERI 1 POMALAA
        </h1>
        <p>Kabupaten Kolaka, Sulawesi Tenggara</p>
    </div>
</section>

<section class="admin-section">
    <div class="admin-buttons" id="adminButtons">
        <a href="modul_berita/admin_berita.php" class="admin-button">KELOLA BERITA <i class="fas fa-newspaper"></i></a>
        <a href="modul_prestasi/admin_prestasi.php" class="admin-button">KELOLA PRESTASI <i class="fas fa-award"></i></a>
        <a href="modul_ekskul/admin_ekskul.php" class="admin-button">KELOLA EKSKUL <i class="fas fa-users"></i></a>
        <a href="modul_struktur/admin_struktur.php" class="admin-button">KELOLA STRUKTUR <i class="fas fa-sitemap"></i></a>
        <a href="modul_feedback/admin_feedback.php" class="admin-button">KELOLA FEEDBACK <i class="fas fa-comment-dots"></i></a>
        <a href="jadwal/admin-panel.php" class="admin-button">KELOLA JADWAL UJIAN <i class="fas fa-calendar-alt"></i></a>
        <a href="modul_slider/admin_slider" class="admin-button">KELOLA SLIDER <i class="fas fa-images"></i></a>
        <?php if ($_SESSION['role'] === 'super-admin'): ?>
            <a href="hash.php" class="admin-button">KELOLA ADMIN <i class="fas fa-user-shield"></i></a>
        <?php endif; ?>
    </div>
</section>

<?php include 'partials/footer.php'; ?>

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

    document.addEventListener('DOMContentLoaded', function () {
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            const bgImage = new Image();
            bgImage.onload = function () {
                console.log('Background image loaded successfully');
            };
            bgImage.onerror = function () {
                console.error('Failed to load background image');
                heroSection.style.background = '#1e3a8a';
            };
            bgImage.src = '<?php echo $asset_path; ?>image/background.png';
        }
    });
</script>

</body>
</html>
