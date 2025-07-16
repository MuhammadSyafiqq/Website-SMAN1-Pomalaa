<?php
session_start();
require_once '../vendor/fpdf/fpdf.php';
require_once '../config/database.php';

// Initialize variables for selected class and major
$selectedKelas = '';
$selectedJurusan = '';
$scheduleResults = [];
$error_message = '';

$role = $_SESSION['role'] ?? null;
$username = $_SESSION['username'] ?? null;
$nama = $_SESSION['nama'] ?? null;

// Definisikan base URL yang lebih dinamis
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$base_url = $protocol . $_SERVER['HTTP_HOST'] . '/';

// Deteksi apakah sedang di subfolder atau tidak
$current_dir = dirname($_SERVER['SCRIPT_NAME']);
$is_subfolder = (basename($current_dir) !== '' && basename($current_dir) !== '/');

// Sesuaikan path untuk assets berdasarkan lokasi file
if ($is_subfolder) {
    $asset_path = '../assets/';
    $page_path = '../';
} else {
    $asset_path = 'assets/';
    $page_path = '';
};

// Function to convert English day names to Indonesian
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

// Fungsi untuk TCPDF
// Improved PDF generation function
function generatePDF($scheduleResults, $selectedKelas, $selectedJurusan) {
    $pdf = new FPDF('P', 'mm', 'A4'); // Portrait orientation
    $pdf->AddPage();
    $pdf->SetMargins(15, 15, 15);

    $logoPath = '../assets/image/logo_sekolah.png';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 15, 10, 20);
    }

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 15, 'SMA NEGERI 1 POMALAA', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'JADWAL UJIAN', 0, 1, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(35, 8, 'KELAS', 0, 0);
    $pdf->Cell(5, 8, ':', 0, 0);
    $pdf->Cell(0, 8, $selectedKelas, 0, 1);
    $pdf->Cell(35, 8, 'JURUSAN', 0, 0);
    $pdf->Cell(5, 8, ':', 0, 0);
    $pdf->Cell(0, 8, $selectedJurusan, 0, 1);
    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->SetDrawColor(0);

    $w = [10, 25, 25, 70, 30, 30];
    $headers = ['No.', 'Tanggal', 'Hari', 'Mata Pelajaran', 'Jam Mulai', 'Jam Selesai'];

    foreach ($headers as $i => $header) {
        $pdf->Cell($w[$i], 8, $header, 1, 0, 'C', true);
    }
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 10);
    $fill = false;
    $no = 1;

    foreach ($scheduleResults as $schedule) {
        $tanggal = date('d/m/Y', strtotime($schedule['tanggal']));
        $hari = convertHariIndo($schedule['hari']);

        $pdf->Cell($w[0], 8, $no++, 1, 0, 'C', $fill);
        $pdf->Cell($w[1], 8, $tanggal, 1, 0, 'C', $fill);
        $pdf->Cell($w[2], 8, $hari, 1, 0, 'C', $fill);
        $pdf->Cell($w[3], 8, $schedule['mata_pelajaran'], 1, 0, 'L', $fill);
        $pdf->Cell($w[4], 8, $schedule['jam_mulai'], 1, 0, 'C', $fill);
        $pdf->Cell($w[5], 8, $schedule['jam_selesai'], 1, 1, 'C', $fill);

        $fill = !$fill;
    }

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 5, 'Dicetak pada: ' . date('d/m/Y H:i:s'), 0, 1);
    $pdf->Cell(0, 5, 'SMA Negeri 1 Pomalaa', 0, 1);

    if (ob_get_level()) {
        ob_end_clean();
    }

    $filename = 'Jadwal_Ujian_' . $selectedKelas . '_' . $selectedJurusan . '_' . date('Ymd_His') . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    $pdf->Output('I', $filename);
    exit;
}


// Handle PDF download - Move this BEFORE any HTML output
if (isset($_POST['download_pdf']) && $_POST['download_pdf'] == '1') {
    // Get the data again for PDF generation
    $selectedKelas = $_POST['kelas'] ?? '';
    $selectedJurusan = $_POST['jurusan'] ?? '';
    
    if (!empty($selectedKelas) && !empty($selectedJurusan)) {
        // Query to get the exam schedule
        $query = "
            SELECT 
                k.nama AS kelas,
                j.nama AS jurusan,
                mp.nama AS mata_pelajaran,
                ju.tanggal AS tanggal,
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
                DATE(ju.tanggal), TIME(ju.jam_mulai)
        ";
        
        $stmt = $connection->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $selectedKelas, $selectedJurusan);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $pdfScheduleResults = [];
                while ($row = $result->fetch_assoc()) {
                    $pdfScheduleResults[] = $row;
                }
                
                if (!empty($pdfScheduleResults)) {
                    generatePDF($pdfScheduleResults, $selectedKelas, $selectedJurusan);
                }
            }
            $stmt->close();
        }
    }
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedKelas = $_POST['kelas'] ?? '';
    $selectedJurusan = $_POST['jurusan'] ?? '';

    if (!empty($selectedKelas) && !empty($selectedJurusan)) {
        // Query to get the exam schedule based on selected class and major
        $query = "
            SELECT 
                k.nama AS kelas,
                j.nama AS jurusan,
                mp.nama AS mata_pelajaran,
                ju.tanggal AS tanggal,
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
                DATE(ju.tanggal), TIME(ju.jam_mulai)
        ";

        // Prepare statement with error handling
        $stmt = $connection->prepare($query);
        
        if ($stmt === false) {
            $error_message = "Error preparing statement: " . $connection->error;
        } else {
            // Bind parameters
            $stmt->bind_param("ss", $selectedKelas, $selectedJurusan);
            
            // Execute query
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                // Fetch results
                while ($row = $result->fetch_assoc()) {
                    $scheduleResults[] = $row;
                }
            } else {
                $error_message = "Error executing query: " . $stmt->error;
            }
            
            $stmt->close();
        }
    } else {
        $error_message = "Please select both class and major.";
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
    <link rel="stylesheet" href="../assets/style/style.css?v=<?php echo time(); ?>">

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

        /* Back Button Styling */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--gray-800);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background-color: #374151;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .back-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-button i {
            font-size: 14px;
        }

        /* Error message styling */
        .error-message {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
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
        .modern-table td:nth-child(3) {
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Time styling */
        .modern-table td:nth-child(4),
        .modern-table td:nth-child(5) {
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
            margin-bottom: 0px;
            text-align: left;
            font-weight: 600;
        }
        
        h4 {
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
            opacity: 0.5;
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

            .back-button {
                font-size: 12px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
   
   <?php include '../partials/navbar.php'; ?>

    <!-- Jadwal Ujian Section -->
    <section class="jadwal-ujian-section">
        <div class="container">
            <!-- Back Button -->
            

            <h2>Jadwal Ujian</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
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
                        <option value="UMUM" <?php echo ($selectedJurusan == 'UMUM') ? 'selected' : ''; ?>>UMUM</option>
                        <option value="IPA" <?php echo ($selectedJurusan == 'IPA') ? 'selected' : ''; ?>>IPA</option>
                        <option value="IPS" <?php echo ($selectedJurusan == 'IPS') ? 'selected' : ''; ?>>IPS</option>
                    </select>
                </div>

                <button type="submit">Lihat Jadwal</button>
                <a href="../" class="" >
                <i class="fas fa-arrow-left"></i>
                Kembali ke Beranda
                </a>
                
        </button>
            </form>

            <?php if (!empty($scheduleResults)): ?>
                <div class="table-container">
                    <h4>Jadwal Ujian untuk Kelas <?php echo htmlspecialchars($selectedKelas); ?> Jurusan <?php echo htmlspecialchars($selectedJurusan); ?></h4>
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
                                    <td><?php echo htmlspecialchars($schedule['tanggal']); ?></td>
                                    <td><?php echo htmlspecialchars(convertHariIndo($schedule['hari'])); ?></td> 
                                    <td><?php echo htmlspecialchars($schedule['mata_pelajaran']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['jam_mulai']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['jam_selesai']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                   <div style="text-align: right; margin-top: 10px;">
                        <form method="POST" action="">
                            <input type="hidden" name="kelas" value="<?php echo htmlspecialchars($selectedKelas); ?>">
                            <input type="hidden" name="jurusan" value="<?php echo htmlspecialchars($selectedJurusan); ?>">
                            <button type="submit" name="download_pdf" value="1" style="background-color: #16d630ff; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </form>
                    </div>
                                </div>
            <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error_message)): ?>
                <div class="table-container">
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h4>Tidak ada jadwal ujian</h4>
                        <p>Belum ada jadwal ujian untuk kelas <?php echo htmlspecialchars($selectedKelas); ?> jurusan <?php echo htmlspecialchars($selectedJurusan); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

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
    <?php include '../partials/footer.php'; ?>
</body>

</html>

<?php
$connection->close();
?>