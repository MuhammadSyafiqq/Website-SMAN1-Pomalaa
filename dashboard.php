<?php
// Start session (jika belum)
session_start();

require_once 'koneksi.php';

// Tangani form feedback sebelum HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $nama = $connection->real_escape_string($_POST['nama']);
    $komentar = $connection->real_escape_string($_POST['komentar']);
    $connection->query("INSERT INTO feedback (nama, komentar) VALUES ('$nama', '$komentar')");

    // Redirect untuk mencegah pengiriman ulang saat refresh
    header("Location: " . $_SERVER['PHP_SELF'] . "#feedback");
    exit(); // Penting untuk menghentikan eksekusi setelah redirect
}

// Query awal untuk berita
$sql = "SELECT * FROM berita ORDER BY date DESC LIMIT 6";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMA Negeri 1 Pomalaa</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style/style.css?v=2">

    <style>
    </style>
</head>
<body>

    <?php include 'partials/navbar.php'; ?>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>SELAMAT DATANG DI<br>SMA NEGERI 1 POMALAA</h1>
            <p>Kabupaten Kolaka, Sulawesi Tenggara</p>
        </div>
    </section>

    <!-- News Section -->
<section id="berita" class="news-section">
    <div class="container">
        <div class="section-title">
            <h2>BERITA</h2>
        </div>
        <div class="news-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $title = $row['title'];
                    $content = isset($row['content']) ? substr($row['content'], 0, 150) . '...' : 'Baca selengkapnya untuk mengetahui detail berita ini.';
                    $background_image = base64_encode($row['image']);
                    ?>
                    <div class="news-card">
                        <img src="data:image/jpeg;base64,<?php echo $background_image; ?>" alt="<?php echo htmlspecialchars($title); ?>">
                        <div class="news-card-content">
                            <h3><?php echo htmlspecialchars($title); ?></h3>
                            <p><?php echo htmlspecialchars($content); ?></p>
                            <a href="detail_berita.php?id=<?php echo $row['id_berita']; ?>" class="read-more">Baca Selengkapnya</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p style="text-align: center; grid-column: 1 / -1;">Tidak ada berita tersedia</p>';
            }
            ?>
        </div>

        <!-- Tombol Selengkapnya -->
        <div style="text-align: center; margin-top: 20px;">
            <a href="berita.php" class="read-more" style="display: inline-block; padding: 10px 20px; background-color: #00589D; color: white; border-radius: 8px; text-decoration: none;">Selengkapnya</a>
        </div>
    </div>
</section>


    <!-- Extracurricular Section from Database -->
<section id="ekstrakurikuler" class="extra-section">
    <div class="container">
        <div class="section-title">
            <h2>EKSTRAKURIKULER</h2>
        </div>
        <div class="extra-grid">
            <?php
            $ekskulQuery = $connection->query("SELECT * FROM ekstrakurikuler ORDER BY name ASC");

            if ($ekskulQuery->num_rows > 0) {
                while ($ekskul = $ekskulQuery->fetch_assoc()) {
                    $id = $ekskul['id_ekskul']; // Ganti dengan kolom yang benar
                    $image = !empty($ekskul['image']) ? 'data:image/jpeg;base64,' . base64_encode($ekskul['image']) : null;

                    echo '<a href="detail_ekstrakurikuler.php?id=' . $id . '" style="text-decoration: none;">';
                    echo '<div class="extra-card" style="';
                    if ($image) {
                        echo "background-image: url('" . $image . "'); background-size: cover; background-position: center;";
                    } else {
                        echo "background-color: #777;";
                    }
                    echo '">';
                    echo '<h3 style="color: white;">' . htmlspecialchars(strtoupper($ekskul['name'])) . '</h3>';
                    echo '</div>';
                    echo '</a>';
                }

            } else {
                echo '<p style="text-align: center; grid-column: 1 / -1;">Tidak ada data ekstrakurikuler tersedia</p>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Feedback Section -->
<section id="feedback" class="feedback-section">
    <div class="feedback-container">
        <h2 class="feedback-title">FEEDBACK</h2>

        <form method="POST" action="#feedback" class="feedback-form">
            <input type="text" name="nama" placeholder="Nama" required>
            <textarea name="komentar" placeholder="Komentar" required></textarea>
            <button type="submit" name="submit_feedback">Kirim</button>
        </form>

        <div class="feedback-list">
            <?php
            $result = $connection->query("SELECT * FROM feedback ORDER BY created_at DESC LIMIT 3");
            while ($row = $result->fetch_assoc()) {
    echo '<div class="feedback-card">';
    echo '<img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" class="avatar" alt="User">';
    echo '<div class="feedback-content">';
    echo '<h4>' . htmlspecialchars($row['nama']) . '</h4>';
    echo '<span class="waktu">' . date("d M Y, H:i", strtotime($row['created_at'])) . '</span>';
    echo '<p>"' . htmlspecialchars($row['komentar']) . '"</p>';

    // Tambahkan balasan jika ada
    if (!empty($row['balasan'])) {
        echo '<div style="margin-top:10px; padding:10px; background-color:#f1f1f1; border-radius:8px;">';
        echo '<strong>Balasan Admin:</strong><br>';
        echo '<p style="margin: 5px 0;">' . nl2br(htmlspecialchars($row['balasan'])) . '</p>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
}
            ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="feedback_lengkap.php" class="read-more" style="display: inline-block; padding: 10px 20px; background-color: #00589D; color: white; border-radius: 8px; text-decoration: none;">Selengkapnya</a>
        </div>
    </div>
</section>


<?php include 'partials/footer.php'; ?>
    
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
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

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(30, 64, 175, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.background = 'var(--primary-blue)';
                navbar.style.backdropFilter = 'none';
            }
        });
    </script>
</body>
</html>

<?php
$connection->close();
?>