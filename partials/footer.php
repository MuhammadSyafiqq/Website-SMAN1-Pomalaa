<?php
// Ambil informasi user jika tersedia
$role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
$nama = $_SESSION['nama'] ?? null;

// Definisikan base URL sesuai struktur di navbar
// Sesuaikan jika aplikasi Anda di dalam folder (contoh: '/sman1pomalaa/')
$base_url = 'https://' . $_SERVER['HTTP_HOST'] . '/'; 
?>
<link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">

<!-- partials/footer.php -->
<footer id="footer" class="footer">
    <div class="container">
        <div class="footer-content">
            <!-- Kontak -->
            <div class="footer-section">
                <h3>HUBUNGI KAMI</h3>
                <div class="contact-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Jl. Salak No.2, Kumoro, Kec. Pomalaa<br>Kabupaten Kolaka, Sulawesi Tenggara 93562</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-phone"></i>
                    <p>(0405) 2310587</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-envelope"></i>
                    <p>smanegeripomalaa1@gmail.com</p>
                </div>
                <div class="contact-info">
                    <a href="https://www.instagram.com/smanpoltv/" target="_blank" style="color: inherit;">
                        <i class="fab fa-instagram"></i>smanpoltv
                    </a>
                </div>
                <div class="contact-info">
                    <a href="https://web.facebook.com/profile.php?id=100069534373516" target="_blank" style="color: inherit;">
                        <i class="fab fa-facebook-f"></i>SMAN 1 Pomalaa
                    </a>
                </div>
            </div>

            <!-- Info Sekolah -->
            <div class="footer-section">
                <h3>INFO SEKOLAH</h3>
                <ul>
                    <li><a href="<?= $base_url ?>tentang.php">Profil Sekolah</a></li>
                    <li><a href="<?= $base_url ?>visi_misi.php">Visi & Misi</a></li>
                    <li><a href="<?= $base_url ?>struktural.php">Struktural</a></li>
                    <li><a href="<?= $base_url ?>prestasi.php">Prestasi</a></li>
                </ul>
            </div>

            <!-- Lokasi Sekolah -->
            <div class="footer-section">
                <h3>LOKASI SEKOLAH</h3>
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps?q=-4.195127844991689,121.60381329283022&hl=id&z=17&output=embed"
                        width="100%"
                        height="200"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Logo dan Hak Cipta -->
        <div class="footer-bottom">
            <div class="footer-logo">
                <img src="<?= $base_url ?>assets/image/logo_sekolah.png" alt="Logo SMA Negeri 1 Pomalaa">
                <div>
                    <h3>SMA NEGERI 1 POMALAA</h3>
                    <p>Unggul dalam prestasi, berkarakter, dan berwawasan global</p>
                </div>
            </div>
            <div>
                <p>&copy; 2025 SMA Negeri 1 Pomalaa. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
