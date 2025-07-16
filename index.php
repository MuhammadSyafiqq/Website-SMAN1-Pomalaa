<?php
// Start session (jika belum)
session_start();

// Database connection
$servername = "localhost";
$username = "u567236312_manuss";
$password = "0F~tuBCg$+v";
$dbname = "u567236312_manuss";

$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

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
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">




    <style>
    </style>
</head>
<body>

    <?php include 'partials/navbar.php'; ?>

 <div class="hero-slider">
        <div class="hero-slide active" style="background-image: url('assets/image/background.png');">
            <div class="hero-content">
                <h1>Selamat Datang di Website Kami</h1>
                <p>Temukan solusi terbaik untuk kebutuhan bisnis Anda dengan layanan profesional dan terpercaya</p>
            </div>
        </div>
        
        <div class="hero-slide" style="background-image: url('assets/image/background2.png');">
            <div class="hero-content">
                <h1>Inovasi Teknologi Terdepan</h1>
                <p>Bergabunglah dengan ribuan klien yang telah merasakan manfaat teknologi canggih kami</p>
            </div>
        </div>
        
        <div class="hero-slide" style="background-image: url('assets/image/background3.png');">
            <div class="hero-content">
                <h1>Tim Profesional Berpengalaman</h1>
                <p>Didukung oleh tim ahli yang siap membantu mewujudkan visi dan misi perusahaan Anda</p>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <button class="hero-prev" onclick="changeSlide(-1)">&#10094;</button>
        <button class="hero-next" onclick="changeSlide(1)">&#10095;</button>

        <!-- Slide Indicators -->
        <div class="hero-indicators">
            <span class="hero-indicator active" onclick="currentSlide(1)"></span>
            <span class="hero-indicator" onclick="currentSlide(2)"></span>
            <span class="hero-indicator" onclick="currentSlide(3)"></span>
        </div>
    </div>
    
    <script>
        // Hero Slider JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            let slideIndex = 1;
            let slideInterval;
            let isAnimating = false;

            // Inisialisasi slider
            showSlides(slideIndex);
            startAutoSlide();

            // Fungsi untuk mengubah slide
            function changeSlide(n) {
                if (isAnimating) return;
                showSlides(slideIndex += n);
                resetAutoSlide();
            }

            // Fungsi untuk menuju slide tertentu
            function currentSlide(n) {
                if (isAnimating) return;
                showSlides(slideIndex = n);
                resetAutoSlide();
            }

            // Fungsi untuk menampilkan slide dengan animasi
            function showSlides(n) {
                const slides = document.getElementsByClassName("hero-slide");
                const indicators = document.getElementsByClassName("hero-indicator");
                
                if (slides.length === 0) return;
                
                if (n > slides.length) { slideIndex = 1; }
                if (n < 1) { slideIndex = slides.length; }
                
                isAnimating = true;
                
                // Nonaktifkan semua indicator
                for (let i = 0; i < indicators.length; i++) {
                    indicators[i].classList.remove('active');
                }
                
                // Aktifkan indicator yang sesuai
                if (indicators[slideIndex - 1]) {
                    indicators[slideIndex - 1].classList.add('active');
                }
                
                // Sembunyikan semua slide dengan animasi keluar
                for (let i = 0; i < slides.length; i++) {
                    slides[i].classList.remove('active');
                    
                    // Tentukan arah animasi keluar
                    if (i < slideIndex - 1) {
                        slides[i].classList.add('prev');
                        slides[i].classList.remove('next');
                    } else if (i > slideIndex - 1) {
                        slides[i].classList.add('next');
                        slides[i].classList.remove('prev');
                    }
                }
                
                // Tampilkan slide yang aktif
                setTimeout(() => {
                    if (slides[slideIndex - 1]) {
                        slides[slideIndex - 1].classList.add('active');
                        slides[slideIndex - 1].classList.remove('prev', 'next');
                    }
                    
                    // Reset animating flag setelah transisi selesai
                    setTimeout(() => {
                        isAnimating = false;
                    }, 800);
                }, 50);
            }

            // Fungsi untuk memulai auto slide
            function startAutoSlide() {
                slideInterval = setInterval(() => {
                    if (!isAnimating) {
                        slideIndex++;
                        showSlides(slideIndex);
                    }
                }, 5000);
            }

            // Fungsi untuk mereset auto slide
            function resetAutoSlide() {
                clearInterval(slideInterval);
                startAutoSlide();
            }

            // Event listener untuk tombol navigasi
            document.querySelector('.hero-prev').addEventListener('click', function(e) {
                e.preventDefault();
                changeSlide(-1);
            });

            document.querySelector('.hero-next').addEventListener('click', function(e) {
                e.preventDefault();
                changeSlide(1);
            });

            // Event listener untuk indicator
            const indicators = document.querySelectorAll('.hero-indicator');
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentSlide(index + 1);
                });
            });

            // Pause auto slide saat hover
            const heroSlider = document.querySelector('.hero-slider');
            heroSlider.addEventListener('mouseenter', function() {
                clearInterval(slideInterval);
            });

            heroSlider.addEventListener('mouseleave', function() {
                startAutoSlide();
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    changeSlide(-1);
                } else if (e.key === 'ArrowRight') {
                    changeSlide(1);
                }
            });

            // Touch/swipe support
            let startX = 0;
            let endX = 0;
            
            heroSlider.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
            });
            
            heroSlider.addEventListener('touchmove', function(e) {
                e.preventDefault();
            });
            
            heroSlider.addEventListener('touchend', function(e) {
                endX = e.changedTouches[0].clientX;
                const deltaX = endX - startX;
                
                if (Math.abs(deltaX) > 50) {
                    if (deltaX > 0) {
                        changeSlide(-1);
                    } else {
                        changeSlide(1);
                    }
                }
            });

            // Expose functions globally for onclick handlers
            window.changeSlide = changeSlide;
            window.currentSlide = currentSlide;
        });
    </script>


    <!-- News Section -->
<section id="berita" class="news-section" style="background-color: #f9f9f9; padding: 60px 20px;">
    <div class="container">
        <div class="section-title">
            <h2>BERITA</h2>
        </div>

        <div class="news-grid" style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
        ">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $title = $row['title'];
                    $deskripsi = isset($row['deskripsi']) ? substr(strip_tags($row['deskripsi']), 0, 100) . '...' : 'Klik selengkapnya untuk detail berita.';
                    $background_image = base64_encode($row['image']);
                    $tanggal = date("d M Y", strtotime($row['date']));
                    $id_berita = $row['id_berita'];

                    // Ambil nama publisher
                    $id_user = $row['id_user'] ?? null;
                    $publisher = "-";
                    if ($id_user) {
                        $user_result = $connection->query("SELECT username FROM user WHERE id_user = $id_user LIMIT 1");
                        if ($user_result && $user_result->num_rows > 0) {
                            $publisher = $user_result->fetch_assoc()['username'];
                        }
                    }
            ?>
            <div class="news-card" style="
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                display: flex;
                flex-direction: column;
            ">
                <img src="data:image/jpeg;base64,<?= $background_image ?>" alt="<?= htmlspecialchars($title) ?>" style="width: 100%; height: 180px; object-fit: cover;">
                <div style="padding: 20px; display: flex; flex-direction: column; flex: 1;">
                    <div style="color: #777; font-size: 0.9em; margin-bottom: 6px;"><?= $tanggal ?></div>
                    <h3 style="font-size: 1.1em; color: #111; margin-bottom: 10px;"><?= htmlspecialchars($title) ?></h3>
                    <p style="color: #444; font-size: 0.95em; flex-grow: 1;"><?= htmlspecialchars($deskripsi) ?></p>
                    
                    <div style="margin-top: auto;">
                        <a href="detail_berita.php?id=<?= $id_berita ?>" class="read-more" style="padding: 8px 16px; background-color: #00589D; color: white; border-radius: 6px; text-decoration: none; display: inline-block;">Baca Selengkapnya</a>
                    </div>
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
        <div style="text-align: center; margin-top: 40px;">
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

        <div class="extra-grid" style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-top: 30px;
        ">
            <?php
            $ekskulQuery = $connection->query("SELECT * FROM ekstrakurikuler ORDER BY name ASC");

            if ($ekskulQuery->num_rows > 0) {
                while ($ekskul = $ekskulQuery->fetch_assoc()) {
                    $id = $ekskul['id_ekskul'];
                    $image = !empty($ekskul['image']) ? 'data:image/jpeg;base64,' . base64_encode($ekskul['image']) : null;

                    echo '<a href="detail_ekstrakurikuler.php?id=' . $id . '" style="text-decoration: none;">';
                    echo '<div class="extra-card" style="';
                    echo 'height: 140px;';
                    echo 'border-radius: 12px;';
                    echo 'display: flex;';
                    echo 'align-items: center;';
                    echo 'justify-content: center;';
                    echo 'padding: 10px;';
                    echo 'text-align: center;';
                    echo 'position: relative;';
                    echo 'overflow: hidden;';
                    echo 'box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);';
                    echo 'transition: transform 0.3s;';
                    if ($image) {
                        echo "background-image: url('" . $image . "'); background-size: cover; background-position: center;";
                    } else {
                        echo "background-color: #777;";
                    }
                    echo '">';

                    echo '<h3 style="color: white; font-size: 16px; font-weight: bold; margin: 0; padding: 0 10px; text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5); word-break: break-word;">' . htmlspecialchars(strtoupper($ekskul['name'])) . '</h3>';
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