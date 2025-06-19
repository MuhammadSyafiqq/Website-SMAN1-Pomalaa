<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_sman1pomalaa";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Initialize variables for selected class and major
$selectedKelas = '';
$selectedJurusan = '';
$scheduleResults = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedKelas = $_POST['kelas'];
    $selectedJurusan = $_POST['jurusan'];

    // Query to get the exam schedule based on selected class and major
    $query = "
        SELECT 
            k.nama AS kelas,
            j.nama AS jurusan,
            mp.nama AS mata_pelajaran,
            ju.date AS tanggal,
            ju.hari,
            ju.jam_mulai,
            ju.jam_selesai
        FROM 
            jadwal_ujian ju
        JOIN 
            kelas k ON ju.kelas_id = k.id
        JOIN 
            jurusan j ON ju.jurusan_id = j.id
        JOIN 
            mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
        WHERE 
            k.nama = ? AND j.nama = ?
        ORDER BY 
            ju.hari, ju.jam_mulai;
    ";

    // Prepare and bind
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ss", $selectedKelas, $selectedJurusan);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results
    while ($row = $result->fetch_assoc()) {
        $scheduleResults[] = $row;
    }

    $stmt->close();

    function convertHariIndo($hariEn) {
    $map = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    return $map[$hariEn] ?? $hariEn;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMA Negeri 1 Pomalaa</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css?v=2">
    <style>
        :root {
            --primary-blue: #1e40af;
            --dark-blue: #1e3a8a;
            --light-blue: #3b82f6;
            --white: #ffffff;
            --gray-100: #f3f4f6;
            --gray-800: #1f2937;
            --text-dark: #111827;
            --purple-gradient: linear-gradient(135deg, #8B5CF6, #A855F7);
            --table-header: #5B4B8A;
            --table-row-even: #F8F7FF;
            --table-row-odd: #FFFFFF;
            --text-light: #6B7280;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            background-color: var(--white);
        }

        .jadwal-ujian-section .container {
            max-width: 1000px;
            margin: 50px auto;
            background-color : #ffff;
            padding: 40px 30px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        /* Modern Table Styling */
        .table-container {
            background: var(--primary-blue);
            border-radius: 16px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .modern-table thead {
            background: var(--table-header);
        }

        .modern-table th {
            background: var(--table-header);
            color: var(--white);
            padding: 16px 20px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            border: none;
        }

        .modern-table th:first-child {
            border-top-left-radius: 12px;
        }

        .modern-table th:last-child {
            border-top-right-radius: 12px;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:nth-child(even) {
            background-color: var(--table-row-even);
        }

        .modern-table tbody tr:nth-child(odd) {
            background-color: var(--table-row-odd);
        }

        .modern-table tbody tr:hover {
            background-color: #E0E7FF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
        }

        .modern-table td {
            padding: 16px 20px;
            border: none;
            border-bottom: 1px solid #E5E7EB;
            font-size: 14px;
            color: var(--text-dark);
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .modern-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }

        .modern-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }

        /* Date column styling */
        .modern-table td:first-child {
            font-weight: 500;
            color: var(--text-light);
            font-size: 13px;
        }

        /* Subject name styling */
        .modern-table td:nth-child(2) {
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Time styling */
        .modern-table td:nth-child(3),
        .modern-table td:nth-child(4) {
            font-family: 'Courier New', monospace;
            font-weight: 500;
            color: var(--primary-blue);
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: var(--primary-blue);
            text-transform: uppercase;
            font-weight: bold;
            font-size: 24px;
        }

        h3 {
            color: var(--white);
            font-size: 18px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .form-jadwal {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 20px 0;
            border: 1px solid var(--gray-800);
            padding: 20px;
            border-radius: 8px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .form-group select {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid var(--gray-800);
            font-size: 16px;
            background-color: var(--white);
            color: var(--text-dark);
        }

        button[type="submit"] {
            background-color: var(--primary-blue);
            color: var(--white);
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        button[type="submit"]:hover {
            background-color: var(--dark-blue);
        }

        /* Empty state styling */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-light);
        }

        .empty-state p {
            color: #ffff;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5
            color: #ffff;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .jadwal-ujian-section .container {
                margin: 20px;
                padding: 20px 15px;
            }

            .table-container {
                padding: 15px;
                margin-top: 20px;
            }

            .modern-table th,
            .modern-table td {
                padding: 12px 10px;
                font-size: 12px;
            }

            .modern-table th {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <!-- Main Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="../dashboard.php" class="nav-brand">
                <img src="../assets/image/logo_sekolah.png" alt="Logo SMA Negeri 1 Pomalaa">
                <div class="nav-brand-text">
                    <h1>SMA NEGERI 1 POMALAA</h1>
                </div>
            </a>
            <ul class="nav-links">
                <li class="dropdown">
                    <a href="#">Profil</a>
                    <ul class="dropdown-menu">
                        <li><a href="tentang.php">Tentang</a></li>
                        <li><a href="../visi_misi.php">Visi Dan Misi</a></li>
                        <li><a href="../akreditasi.php">Akreditasi</a></li>
                    </ul>
                </li>
                <li><a href="../prestasi.php">Prestasi</a></li>
                <li><a href="../struktural.php">Struktural</a></li>
                <li class="dropdown">
                    <a href="#">Layanan</a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Jadwal Ujian</a></li>
                    </ul>
                </li>
                <li><a href="#footer">Kontak</a></li>
            </ul>
        </div>
    </nav>

    <!-- Jadwal Ujian Section -->
    <section class="jadwal-ujian-section">
        <div class="container">
            <h2>Jadwal Ujian</h2>
            <form class="form-jadwal" method="POST" action="">
                <div class="form-group">
                    <label for="kelas">Pilih Kelas</label>
                    <select id="kelas" name="kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        <option value="X" <?php echo ($selectedKelas == 'X') ? 'selected' : ''; ?>>X</option>
                        <option value="XI" <?php echo ($selectedKelas == 'XI') ? 'selected' : ''; ?>>XI</option>
                        <option value="XII" <?php echo ($selectedKelas == 'XII') ? 'selected' : ''; ?>>XII</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jurusan">Pilih Jurusan</label>
                    <select id="jurusan" name="jurusan" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <option value="-" <?php echo ($selectedJurusan == '-') ? 'selected' : ''; ?>>-</option>
                        <option value="IPA" <?php echo ($selectedJurusan == 'IPA') ? 'selected' : ''; ?>>IPA</option>
                        <option value="IPS" <?php echo ($selectedJurusan == 'IPS') ? 'selected' : ''; ?>>IPS</option>
                    </select>
                </div>

                <button type="submit">Lihat Jadwal</button>
            </form>

            <?php if (!empty($scheduleResults)): ?>
                <div class="table-container">
                    <h3>Jadwal Ujian untuk Kelas <?php echo $selectedKelas; ?> Jurusan <?php echo $selectedJurusan; ?></h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Mata Pelajaran</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scheduleResults as $schedule): ?>
                                <tr>
                                    <td><?php echo $schedule['tanggal']; ?></td>
                                    <td><?php echo convertHariIndo($schedule['hari']); ?></td> 
                                    <td><?php echo $schedule['mata_pelajaran']; ?></td>
                                    <td><?php echo $schedule['jam_mulai']; ?></td>
                                    <td><?php echo $schedule['jam_selesai']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <div class="table-container">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>Tidak ada jadwal ujian</h3>
                        <p>Belum ada jadwal ujian untuk kelas <?php echo $selectedKelas; ?> jurusan <?php echo $selectedJurusan; ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>HUBUNGI KAMI</h3>
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Jl. Pendidikan No. 123, Pomalaa<br>Kabupaten Kolaka, Sulawesi Tenggara</p>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <p>(0405) 123456</p>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <p>info@sman1pomalaa.sch.id</p>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-fax"></i>
                        <p>(0405) 123457</p>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>INFO SEKOLAH</h3>
                    <ul>
                        <li><a href="#">Profil Sekolah</a></li>
                        <li><a href="#">Visi & Misi</a></li>
                        <li><a href="#">Struktur Organisasi</a></li>
                        <li><a href="#">Fasilitas</a></li>
                        <li><a href="#">Prestasi</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>AGENDA SEKOLAH</h3>
                    <ul>
                        <li><a href="#">Kalender Akademik</a></li>
                        <li><a href="#">Kegiatan Sekolah</a></li>
                        <li><a href="#">Pengumuman</a></li>
                        <li><a href="#">Event Terbaru</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>DENAH LOKAL</h3>
                    <p>Temukan lokasi ruang kelas, laboratorium, dan fasilitas sekolah lainnya dengan mudah melalui denah interaktif kami.</p>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-logo">
                    <img src="../assets/image/logo_sekolah.png" alt="Logo SMA Negeri 1 Pomalaa">
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

        // Add table row animation
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('.modern-table tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>

<?php
$connection->close();
?>