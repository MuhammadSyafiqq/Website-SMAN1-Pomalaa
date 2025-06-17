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

// Initialize messages
$message = "";

// Handle kelas insertion
if (isset($_POST['add_kelas'])) {
    $kelas_nama = strtoupper(trim($_POST['kelas_nama']));
    if ($kelas_nama) {
        // Generate ID otomatis untuk kelas
        $new_id = generateNextId($connection, 'kelas', 'KL-');
        
        $stmt = $connection->prepare("INSERT INTO kelas (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_id, $kelas_nama);
        if ($stmt->execute()) {
            $message = "Kelas berhasil ditambahkan dengan ID: $new_id";
        } else {
            $message = "Gagal menambahkan kelas: " . $connection->error;
        }
        $stmt->close();
    } else {
        $message = "Nama kelas harus diisi.";
    }
    // Redirect to prevent form resubmission
    redirectWithMessage($message);
}

if (isset($_POST['add_jurusan'])) {
    $jurusan_nama = strtoupper(trim($_POST['jurusan_nama']));
    if ($jurusan_nama) {
        // Generate ID otomatis untuk jurusan
        $new_id = generateNextId($connection, 'jurusan', 'JR-');
        
        $stmt = $connection->prepare("INSERT INTO jurusan (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_id, $jurusan_nama);
        if ($stmt->execute()) {
            $message = "Jurusan berhasil ditambahkan dengan ID: $new_id";
        } else {
            $message = "Gagal menambahkan jurusan: " . $connection->error;
        }
        $stmt->close();
    } else {
        $message = "Nama jurusan harus diisi.";
    }
    // Redirect to prevent form resubmission
    redirectWithMessage($message);
}

//Handle mata_pelajaran insertion dengan ID otomatis
if (isset($_POST['add_mata_pelajaran'])) {
    $mp_nama = trim($_POST['mata_pelajaran_nama']);
    $kategori = $_POST['kategori'];
    
    if ($mp_nama && $kategori) {
        // Generate ID otomatis
        $new_id = generateNextId($connection, 'mata_pelajaran', 'MP-');
        
        $stmt = $connection->prepare("INSERT INTO mata_pelajaran (id, nama, kategori) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $new_id, $mp_nama, $kategori);
        
        if ($stmt->execute()) {
            $message = "Mata Pelajaran berhasil ditambahkan dengan ID: $new_id";
        } else {
            $message = "Gagal menambahkan Mata Pelajaran: " . $connection->error;
        }
        $stmt->close();
    } else {
        $message = "Nama mata pelajaran dan kategori harus diisi.";
    }
    // Redirect to prevent form resubmission
    redirectWithMessage($message);
}

// Handle jadwal_ujian insertion
if (isset($_POST['add_jadwal_ujian'])) {
    $kelas_id = $_POST['kelas_id'];
    $jurusan_id = $_POST['jurusan_id'];
    $mp_id = $_POST['mata_pelajaran_id'];
    $date = $_POST['date'];
    $hari = date('l', strtotime($date));
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    if ($kelas_id && $jurusan_id && $mp_id && $date && $jam_mulai && $jam_selesai) {
        // Validasi jam mulai harus lebih kecil dari jam selesai
        if ($jam_mulai >= $jam_selesai) {
            $message = "Jam mulai harus lebih kecil dari jam selesai.";
        } else {
            // Gunakan fungsi validasi yang lebih lengkap
            $validation_errors = validateJadwalUjian($connection, $kelas_id, $jurusan_id, $mp_id, $date, $jam_mulai, $jam_selesai);
            
            if (!empty($validation_errors)) {
                $message = "Gagal menambahkan jadwal ujian: " . implode(" ", $validation_errors);
            } else {
                // Check if mata_pelajaran_id exists
                $stmt_check_mp = $connection->prepare("SELECT id FROM mata_pelajaran WHERE id = ?");
                $stmt_check_mp->bind_param("s", $mp_id);
                $stmt_check_mp->execute();
                $result_check_mp = $stmt_check_mp->get_result();
                
                if ($result_check_mp->num_rows === 0) {
                    $message = "Error: Mata pelajaran yang dipilih tidak ditemukan.";
                } else {
                    // Generate ID otomatis untuk jadwal ujian
                    $new_ju_id = generateNextId($connection, 'jadwal_ujian', 'JU-');
                    
                    // Insert jadwal ujian
                    $stmt = $connection->prepare("INSERT INTO jadwal_ujian (id, kelas_id, jurusan_id, mata_pelajaran_id, date, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $new_ju_id, $kelas_id, $jurusan_id, $mp_id, $date, $hari, $jam_mulai, $jam_selesai);
                    
                    if ($stmt->execute()) {
                        $message = "Jadwal Ujian berhasil ditambahkan dengan ID: $new_ju_id";
                    } else {
                        $message = "Gagal menambahkan Jadwal Ujian: " . $connection->error;
                    }
                    $stmt->close();
                }
                $stmt_check_mp->close();
            }
        }
    } else {
        $message = "Semua field harus diisi.";
    }
    redirectWithMessage($message);
}



if (isset($_GET['delete'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];
    
    if (in_array($table, ['kelas', 'jurusan', 'mata_pelajaran', 'jadwal_ujian'])) {
        $stmt = $connection->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            $message = ucfirst($table) . " berhasil dihapus.";
        } else {
            $message = "Gagal menghapus " . $table . ": " . $connection->error;
        }
        $stmt->close();
    }
     redirectWithMessage($message);
}

// Handle EDIT operations
if (isset($_POST['edit_kelas'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));
    
    $stmt = $connection->prepare("UPDATE kelas SET nama = ? WHERE id = ?");
    $stmt->bind_param("ss", $nama, $id); // Change to "ss" for VARCHAR
    
    if ($stmt->execute()) {
        $message = "Kelas berhasil diupdate.";
    } else {
        $message = "Gagal mengupdate kelas: " . $connection->error;
    }
    $stmt->close();
    redirectWithMessage($message);
}

// EDIT JURUSAN
if (isset($_POST['edit_jurusan'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));
    
    $stmt = $connection->prepare("UPDATE jurusan SET nama = ? WHERE id = ?");
    $stmt->bind_param("ss", $nama, $id); // Change to "ss" for VARCHAR
    
    if ($stmt->execute()) {
        $message = "Jurusan berhasil diupdate.";
    } else {
        $message = "Gagal mengupdate jurusan: " . $connection->error;
    }
    $stmt->close();
    redirectWithMessage($message);
}

//EDIT MATA_PELAJARAN
if (isset($_POST['edit_mata_pelajaran'])) {
    $id = $_POST['edit_id'];
    $nama = trim($_POST['edit_nama']);
    $kategori = $_POST['edit_kategori'];
    
    $stmt = $connection->prepare("UPDATE mata_pelajaran SET nama = ?, kategori = ? WHERE id = ?");
    $stmt->bind_param("sss", $nama, $kategori, $id);
    
    if ($stmt->execute()) {
        $message = "Mata Pelajaran berhasil diupdate.";
    } else {
        $message = "Gagal mengupdate mata pelajaran: " . $connection->error;
    }
    $stmt->close();
}
//EDIT JADWAL_UJIAN 
if (isset($_POST['edit_jadwal_ujian'])) {
    $id = $_POST['edit_id'];
    $kelas_id = $_POST['edit_kelas_id'];
    $jurusan_id = $_POST['edit_jurusan_id'];
    $mata_pelajaran_id = $_POST['edit_mata_pelajaran_id'];
    $date = $_POST['edit_date'];
    $hari = date('l', strtotime($date));
    $jam_mulai = $_POST['edit_jam_mulai'];
    $jam_selesai = $_POST['edit_jam_selesai'];
    // Debugging output
    error_log("Editing Jadwal Ujian: ID: $id, Kelas ID: $kelas_id, Jurusan ID: $jurusan_id, Mata Pelajaran ID: $mata_pelajaran_id, Date: $date, Jam Mulai: $jam_mulai, Jam Selesai: $jam_selesai");
    // Validasi jam mulai harus lebih kecil dari jam selesai
    if ($jam_mulai >= $jam_selesai) {
        $message = "Jam mulai harus lebih kecil dari jam selesai.";
    } else {
        // Use validation function with exclude current id
        $validation_errors = validateJadwalUjian($connection, $kelas_id, $jurusan_id, $mata_pelajaran_id, $date, $jam_mulai, $jam_selesai, $id);
        
        if (!empty($validation_errors)) {
            $message = "Gagal mengupdate jadwal ujian: " . implode(" ", $validation_errors);
        } else {
            $stmt = $connection->prepare("UPDATE jadwal_ujian SET kelas_id = ?, jurusan_id = ?, mata_pelajaran_id = ?, date = ?, hari = ?, jam_mulai = ?, jam_selesai = ? WHERE id = ?");
            $stmt->bind_param("ssssssss", $kelas_id, $jurusan_id, $mata_pelajaran_id, $date, $hari, $jam_mulai, $jam_selesai, $id);
            
            if ($stmt->execute()) {
                $message = "Jadwal Ujian berhasil diupdate.";
            } else {
                $message = "Gagal mengupdate jadwal ujian: " . $connection->error;
            }
            $stmt->close();
        }
    }
    redirectWithMessage($message);
}

function redirectWithMessage($message) {
    session_start();
    $_SESSION['message'] = $message;
    
    // Get current script name without query parameters
    $current_url = strtok($_SERVER['REQUEST_URI'], '?');
    
    // Redirect to clean URL
    header("Location: " . $current_url);
    exit();
}

function getSessionMessage() {
    session_start();
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return "";
}

// Get message from session
$message = getSessionMessage();

function generateNextId($connection, $table, $prefix) {
    $query = "SELECT id FROM $table WHERE id LIKE '$prefix%' ORDER BY CAST(SUBSTRING(id, " . (strlen($prefix) + 1) . ") AS UNSIGNED) DESC LIMIT 1";
    $result = $connection->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['id'];
        // Extract nomor dari ID terakhir (contoh: MP-001 -> 001)
        $number = (int)substr($lastId, strlen($prefix));
        $nextNumber = $number + 1;
        // Format dengan leading zeros (3 digit)
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    } else {
        // Jika belum ada data, mulai dari 001
        return $prefix . '001';
    }
}



// Get existing data for selects and display
function getAll($connection, $table) {
    $result = $connection->query("SELECT * FROM $table ORDER BY nama ASC");
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function getMataPelajaran($connection, $kategori_filter = null) {
    if ($kategori_filter && $kategori_filter !== 'all') {
        $stmt = $connection->prepare("SELECT * FROM mata_pelajaran WHERE kategori = ? ORDER BY nama ASC");
        $stmt->bind_param("s", $kategori_filter);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $result = $connection->query("SELECT * FROM mata_pelajaran ORDER BY kategori, nama ASC");
    }
    
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

$kelas_list = getAll($connection, "kelas");
$jurusan_list = getAll($connection, "jurusan");

// MODIFIED: Get mata pelajaran with filter if provided
$kategori_filter = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : null;
$mata_pelajaran_list = getMataPelajaran($connection, $kategori_filter);

// Ambil data jadwal_ujian dengan join ke tabel terkait
$jadwal_ujian_list = [];
$query = "
    SELECT 
        ju.id,
        ju.kelas_id,
        ju.jurusan_id, 
        ju.mata_pelajaran_id,
        k.nama AS kelas,
        j.nama AS jurusan,
        mp.nama AS mata_pelajaran,
        ju.date,
        ju.hari,
        ju.jam_mulai,
        ju.jam_selesai
    FROM 
        jadwal_ujian ju
    JOIN kelas k ON ju.kelas_id = k.id
    JOIN jurusan j ON ju.jurusan_id = j.id
    JOIN mata_pelajaran mp ON ju.mata_pelajaran_id = mp.id
    ORDER BY ju.date, ju.jam_mulai;
";
$result = $connection->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $jadwal_ujian_list[] = $row;
    }
}

// Function to get mata pelajaran grouped by category
function getMataPelajaranByCategory($connection) {
    $query = "SELECT * FROM mata_pelajaran ORDER BY kategori, nama ASC";
    $result = $connection->query($query);
    
    $mata_pelajaran_by_category = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $mata_pelajaran_by_category[$row['kategori']][] = $row;
        }
    }
    return $mata_pelajaran_by_category;
}

// Fetch mata pelajaran grouped by category
$mata_pelajaran_by_category = getMataPelajaranByCategory($connection);


// Get filtered mata_pelajaran for AJAX request or form display
function getFilteredMataPelajaran($connection, $kelas_id, $jurusan_id) {
    $filtered_mata_pelajaran_list = [];
    
    // Get jurusan name to determine category
    $stmt = $connection->prepare("SELECT nama FROM jurusan WHERE id = ?");
    $stmt->bind_param("s", $jurusan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $jurusan_row = $result->fetch_assoc();
    $stmt->close();
    
    if ($jurusan_row) {
        $jurusan_nama = strtolower($jurusan_row['nama']);
        
        // Determine categories based on jurusan
        $categories = [];
        if ($jurusan_nama === 'ipa') {
            $categories = ['umum', 'ipa'];
        } elseif ($jurusan_nama === 'ips') {
            $categories = ['umum', 'ips'];
        } else {
            // For other majors, show only 'umum' subjects
            $categories = ['umum'];
        }
        
        if (!empty($categories)) {
            // Create placeholders for IN clause
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            
            // PERBAIKAN: Filter mata pelajaran yang belum dijadwalkan HANYA untuk kombinasi kelas_id dan jurusan_id yang dipilih
            $query = "
                SELECT mp.id, mp.nama, mp.kategori
                FROM mata_pelajaran mp 
                WHERE mp.kategori IN ($placeholders) 
                AND mp.id NOT IN (
                    SELECT ju.mata_pelajaran_id 
                    FROM jadwal_ujian ju 
                    WHERE ju.kelas_id = ? AND ju.jurusan_id = ?
                )
                ORDER BY mp.nama ASC
            ";
            
            $stmt = $connection->prepare($query);
            
            // Bind parameters: categories + kelas_id + jurusan_id
            $types = str_repeat('s', count($categories)) . "ss";
            $params = array_merge($categories, [$kelas_id, $jurusan_id]);
            
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $filtered_mata_pelajaran_list[] = $row;
            }
            $stmt->close();
        }
    }
    
    return $filtered_mata_pelajaran_list;
}

// Tambahan: Fungsi untuk validasi jadwal yang lebih ketat
function validateJadwalUjian($connection, $kelas_id, $jurusan_id, $mata_pelajaran_id, $date, $jam_mulai, $jam_selesai, $exclude_id = null) {
    $errors = [];
    
    // 1. Cek apakah mata pelajaran sudah dijadwalkan untuk kelas dan jurusan ini
    $query_mp_check = "
        SELECT COUNT(*) as count 
        FROM jadwal_ujian 
        WHERE kelas_id = ? AND jurusan_id = ? AND mata_pelajaran_id = ?
    ";
    
    if ($exclude_id) {
        $query_mp_check .= " AND id != ?";
    }
    
    $stmt = $connection->prepare($query_mp_check);
    
    if ($exclude_id) {
        $stmt->bind_param("ssss", $kelas_id, $jurusan_id, $mata_pelajaran_id, $exclude_id);
    } else {
        $stmt->bind_param("sss", $kelas_id, $jurusan_id, $mata_pelajaran_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($row['count'] > 0) {
        $errors[] = "Mata pelajaran ini sudah dijadwalkan untuk kelas dan jurusan yang dipilih.";
    }
    
    return $errors;
}



// Handle AJAX request for filtering mata pelajaran
if (isset($_GET['ajax']) && $_GET['ajax'] === 'filter_mata_pelajaran') {
    header('Content-Type: application/json');
    
    // Change the type casting to string
    $kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
    $jurusan_id = isset($_GET['jurusan_id']) ? $_GET['jurusan_id'] : '';
    
    if ($kelas_id && $jurusan_id) {
        $filtered_list = getFilteredMataPelajaran($connection, $kelas_id, $jurusan_id);
        echo json_encode($filtered_list);
    } else {
        echo json_encode([]);
    }
    exit;
}

// Handle AJAX request for checking day availability
if (isset($_GET['ajax']) && $_GET['ajax'] === 'check_day_availability') {
    header('Content-Type: application/json');
    
    $hari = isset($_GET['hari']) ? $_GET['hari'] : '';
    
    if ($hari) {
        $stmt = $connection->prepare("SELECT COUNT(*) as total FROM jadwal_ujian WHERE hari = ?");
        $stmt->bind_param("s", $hari);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = $row['total'];
        $stmt->close();
        
        echo json_encode([
            'available' => $total < 6,
            'current_count' => $total,
            'message' => $total >= 6 ? "Hari $hari sudah memiliki 6 jadwal ujian (maksimal)" : "Tersedia untuk dijadwalkan"
        ]);
    } else {
        echo json_encode(['available' => true, 'current_count' => 0, 'message' => '']);
    }
    exit;
}
$grouped_jadwal = [];
foreach ($jadwal_ujian_list as $item) {
    $jurusan = $item['jurusan'];
    $kelas = $item['kelas'];
    if (!isset($grouped_jadwal[$jurusan])) {
        $grouped_jadwal[$jurusan] = [];
    }
    if (!isset($grouped_jadwal[$jurusan][$kelas])) {
        $grouped_jadwal[$jurusan][$kelas] = [];
    }
    $grouped_jadwal[$jurusan][$kelas][] = $item;
}
// For JS usage encode JSON safely for inclusion
$grouped_jadwal_json = json_encode($grouped_jadwal);

// Function to convert English day names to Indonesian

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Input Data Jadwal Ujian</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 20px;
            color: #111827;
        }
        h1 {
            text-align: center;
            color: #1e40af;
            margin-bottom: 40px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .form-section {
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgb(0 0 0 / 0.1);
            margin-bottom: 40px;
        }
        h2 {
            color: #1e40af;
            margin-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 8px;
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-end;
        }
        label {
            flex-basis: 100%;
            font-weight: 600;
            margin-bottom: 6px;
        }
        input[type="text"],
        select,
        input[type="date"],
        input[type="time"] {
            flex-grow: 1;
            padding: 10px;
            border: 1.5px solid #94a3b8;
            border-radius: 8px;
            font-size: 1rem;
            color: #1f2937;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus,
        select:focus,
        input[type="time"]:focus {
            border-color: #2563eb;
            outline: none;
        }
        button {
            background-color: #1e40af;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        button:hover {
            background-color: #2563eb;
        }
        button.edit {
            background-color: #059669;
            padding: 6px 12px;
            font-size: 0.875rem;
        }
        button.edit:hover {
            background-color: #047857;
        }
        button.delete {
            background-color: #dc2626;
            padding: 6px 12px;
            font-size: 0.875rem;
        }
        button.delete:hover {
            background-color: #b91c1c;
        }
        .message {
            margin: 15px 0 0 0;
            font-weight: 700;
            color: #16a34a;
        }
        .message.error {
            color: #dc2626;
        }
        .day-warning {
            color: #f59e0b;
            font-size: 0.875rem;
            margin-top: 4px;
            display: none;
        }
        .day-warning.show {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        th {
            background-color: #3b82f6;
            color: white;
            user-select: none;
        }
        .actions {
            white-space: nowrap;
            text-align: right;
            width: 150px;
        }
        .actions button {
            margin-right: 5px;
        }
        .loading {
            opacity: 0.5;
            pointer-events: none;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        .modal form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .modal input, .modal select {
            width: 100%;
            box-sizing: border-box;
        }
        
        @media (max-width: 720px) {
            form {
                flex-direction: column;
                align-items: stretch;
            }
            input[type="text"], select, input[type="time"], button {
                flex-grow: 0;
            }
            table {
                font-size: 0.875rem;
            }
            .actions button {
                padding: 4px 8px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <h1>Admin Panel - Input Data Jadwal Ujian</h1>
    <div class="container">
        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Gagal') !== false) ? 'error' : ''; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <!-- KELAS SECTION -->
        <section class="form-section" aria-label="Form Input Kelas">
            <h2>Kelola Kelas</h2>
            <form method="POST" aria-describedby="kelas-desc">
                <label for="kelas_nama">Nama Kelas (misal: X, XI, XII)</label>
                <input type="text" id="kelas_nama" name="kelas_nama" maxlength="10" placeholder="Contoh: X" required />
                <button type="submit" name="add_kelas">Tambahkan Kelas</button>
            </form>
            <table aria-label="Daftar Kelas">
                <thead>
                    <tr><th>ID</th><th>Nama Kelas</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($kelas_list as $row): ?>
                    <tr>
                        <td><?php echo $row['id'] ?></td>
                        <td><?php echo htmlspecialchars($row['nama']) ?></td>
                        <td class="actions">
                            <button class="edit" onclick="editKelas('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Edit</button>
                            <button class="delete" onclick="confirmDelete('kelas', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Hapus</button>

                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="form-section" aria-label="Form Input Jurusan">
            <h2>Input Jurusan</h2>
            <form method="POST" aria-describedby="jurusan-desc">
                <label for="jurusan_nama">Nama Jurusan (misal: IPA, IPS)</label>
                <input type="text" id="jurusan_nama" name="jurusan_nama" maxlength="10" placeholder="Contoh: IPA" required />
                <button type="submit" name="add_jurusan">Tambahkan Jurusan</button>
            </form>
            <table aria-label="Daftar Jurusan">
    <thead>
        <tr><th>ID</th><th>Nama Jurusan</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php foreach ($jurusan_list as $row): ?>
        <tr>
            <td><?php echo $row['id'] ?></td>
            <td><?php echo htmlspecialchars($row['nama']) ?></td>
            <td class="actions">
                <button class="edit" onclick="editJurusan('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Edit</button>
                <button class="delete" onclick="confirmDelete('jurusan', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Hapus</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
        </section>

        <section class="form-section" aria-label="Form Input Mata Pelajaran">
            <h2>Input Mata Pelajaran</h2>
            <form method="POST" aria-describedby="mp-desc">
                <label for="mata_pelajaran_nama">Nama Mata Pelajaran</label>
                <input type="text" id="mata_pelajaran_nama" name="mata_pelajaran_nama" maxlength="100" placeholder="Contoh: Matematika" required />
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="umum">Umum</option>
                    <option value="ipa">Peminatan IPA</option>
                    <option value="ips">Peminatan IPS</option>
                </select>
                <button type="submit" name="add_mata_pelajaran">Tambahkan Mata Pelajaran</button>
            </form>
        </section>

        <section class="form-section" aria-label="Daftar Mata Pelajaran">
    <h2>Daftar Mata Pelajaran</h2>
    
    <?php foreach ($mata_pelajaran_by_category as $kategori => $mata_pelajaran_list): ?>
        <h3>Mata Pelajaran Jurusan <?php echo htmlspecialchars(ucfirst($kategori)); ?> </h3>
        <table aria-label="Daftar Mata Pelajaran <?php echo htmlspecialchars($kategori); ?>">
            <thead>
                <tr><th>ID</th><th>Nama Mata Pelajaran</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($mata_pelajaran_list as $row): ?>
                <tr>
                    <td><?php echo $row['id'] ?></td>
                    <td><?php echo htmlspecialchars($row['nama']) ?></td>
                    <td class="actions">
                        <button class="edit" onclick="editMataPelajaran('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nama']); ?>', '<?php echo htmlspecialchars($row['kategori']); ?>')">Edit</button>
                        <button class="delete" onclick="confirmDelete('mata_pelajaran', '<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Hapus</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</section>


        <section class="form-section" aria-label="Form Input Jadwal Ujian">
            <h2>Input Jadwal Ujian</h2>
            <!-- Existing jadwal form unchanged here: kelas, jurusan, mata pelajaran, hari, waktu inputs -->

            <form method="POST" aria-describedby="jadwal-desc" id="jadwalForm">
                <!-- ... existing form fields ... -->

                <label for="kelas_id">Pilih Kelas</label>
                <select name="kelas_id" id="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="jurusan_id">Pilih Jurusan</label>
                <select name="jurusan_id" id="jurusan_id" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo $j['id']; ?>"><?php echo htmlspecialchars($j['nama']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="mata_pelajaran_id">Pilih Mata Pelajaran</label>
                <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                </select>

                <label for="date">Tanggal</label>
                <input type="date" id="date" name="date" required />

                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" required />

                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" required />

                <button type="submit" name="add_jadwal_ujian">Tambahkan Jadwal Ujian</button>
            </form>

            <!-- Filter jurusan for jadwal display -->
            <div class="filter-section" aria-label="Filter Jadwal Ujian berdasarkan Jurusan">
                <label for="filter_jurusan">Filter Jadwal Ujian berdasarkan Jurusan:</label>
                <select id="filter_jurusan">
                    <option value="all">Semua Jurusan</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo htmlspecialchars($j['nama']) ?>"><?php echo htmlspecialchars($j['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="jadwal-container" aria-live="polite" aria-relevant="additions removals">
                <!-- Jadwal ujian grouped tables will be rendered here -->
                <p>Memuat data jadwal ujian...</p>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelasSelect = document.getElementById('kelas_id');
            const jurusanSelect = document.getElementById('jurusan_id');
            const mataPelajaranSelect = document.getElementById('mata_pelajaran_id');
            const dateInput = document.getElementById('date');
            const hariSelect = document.getElementById('hari');
            const dayWarning = document.getElementById('day-warning');
            const submitButton = document.querySelector('button[name="add_jadwal_ujian"]');
            
            // Data of jadwal grouped by jurusan -> kelas passed from backend
            const groupedJadwal = <?php echo $grouped_jadwal_json; ?>;
            const jadwalContainer = document.getElementById('jadwal-container');
            const filterJurusanSelect = document.getElementById('filter_jurusan');

            const editKelasSelect = document.getElementById('edit_ju_kelas');
            const editJurusanSelect = document.getElementById('edit_ju_jurusan');
            const editMataPelajaranSelect = document.getElementById('edit_ju_mata_pelajaran');

                // ... existing code ...
    // Mapping of English day names to Indonesian
    const dayNamesEnglishToIndonesian = {
        'Sunday': 'Minggu',
        'Monday': 'Senin',
        'Tuesday': 'Selasa',
        'Wednesday': 'Rabu',
        'Thursday': 'Kamis',
        'Friday': 'Jumat',
        'Saturday': 'Sabtu'
    };
    function createTable(jadwals) {
        if (!jadwals || jadwals.length === 0) {
            return '<p>Tidak ada jadwal ujian yang tersedia.</p>';
        }
        let html = '<table aria-label="Daftar Jadwal Ujian"><thead><tr><th>ID</th><th>Mata Pelajaran</th><th>Tanggal</th><th>Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Aksi</th></tr></thead><tbody>';
        jadwals.forEach(item => {
            const hariIndo = dayNamesEnglishToIndonesian[item.hari] || item.hari; // Convert to Indonesian
            html += `<tr>
                <td>${item.id}</td>
                <td>${sanitize(item.mata_pelajaran)}</td>
                <td>${item.date}</td>
                <td>${hariIndo}</td> <!-- Use the Indonesian day name -->
                <td>${item.jam_mulai}</td>
                <td>${item.jam_selesai}</td>
                <td>
                    <button class="edit" onclick="editJadwalUjian('${item.id}', '${item.kelas_id}', '${item.jurusan_id}', '${item.mata_pelajaran_id}', '${item.date}', '${hariIndo}', '${item.jam_mulai}', '${item.jam_selesai}')">Edit</button>

                    <button class="delete" onclick="confirmDelete('jadwal_ujian', '${item.id}', '${sanitize(item.mata_pelajaran)}')">Hapus</button>
                </td>
            </tr>`;
        });
        html += '</tbody></table>';
        return html;
    }

            // Sanitize function to avoid injection or HTML issues
            function sanitize(text) {
                if (!text) return '';
                return text.replace(/&/g, '&amp;')
                           .replace(/</g, '&lt;')
                           .replace(/>/g, '&gt;')
                           .replace(/"/g, '&quot;')
                           .replace(/'/g, '&#39;');
            }

            function renderJadwal(filterJurusan) {
                jadwalContainer.innerHTML = '';

                let jurusansToShow = filterJurusan === 'all' ? Object.keys(groupedJadwal) : [filterJurusan];

                jurusansToShow.forEach(jurusan => {
                    if (!groupedJadwal[jurusan]) return;

                    const jurusanDiv = document.createElement('div');
                    jurusanDiv.className = 'jadwal-group';
                    const jurusanHeading = document.createElement('h2');
                    jurusanHeading.textContent = `Jurusan: ${jurusan}`;
                    jurusanDiv.appendChild(jurusanHeading);

                    // For each kelas in jurusan
                    const kelasList = Object.keys(groupedJadwal[jurusan]).sort();
                    kelasList.forEach(kelas => {
                        const kelasDiv = document.createElement('div');
                        kelasDiv.className = 'jadwal-group';
                        const kelasHeading = document.createElement('h3');
                        kelasHeading.textContent = `Kelas: ${kelas}`;
                        kelasDiv.appendChild(kelasHeading);

                        const jadwals = groupedJadwal[jurusan][kelas];

                        kelasDiv.innerHTML += createTable(jadwals);
                        jurusanDiv.appendChild(kelasDiv);
                    });

                    jadwalContainer.appendChild(jurusanDiv);
                });

                if (jadwalContainer.children.length === 0) {
                    jadwalContainer.innerHTML = '<p>Tidak ada jadwal ujian untuk jurusan ini.</p>';
                }
            }

            // Initial render all
            renderJadwal('all');

    // Filter change event
    filterJurusanSelect.addEventListener('change', function() {
        renderJadwal(this.value);
    });

    function updateMataPelajaran(kelasSelectElement, jurusanSelectElement, mataPelajaranSelectElement) {
        const kelasId = kelasSelectElement.value;
        const jurusanId = jurusanSelectElement.value;
        
        // Reset mata pelajaran select
        mataPelajaranSelectElement.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
        mataPelajaranSelectElement.disabled = true;
        
        if (kelasId && jurusanId) {
            // Show loading state
            mataPelajaranSelectElement.classList.add('loading');
            mataPelajaranSelectElement.innerHTML = '<option value="">Loading...</option>';
            
            // Fetch filtered mata pelajaran
            fetch(`?ajax=filter_mata_pelajaran&kelas_id=${encodeURIComponent(kelasId)}&jurusan_id=${encodeURIComponent(jurusanId)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    mataPelajaranSelectElement.classList.remove('loading');
                    mataPelajaranSelectElement.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
                    
                    if (data.length > 0) {
                        data.forEach(mp => {
                            const option = document.createElement('option');
                            option.value = mp.id;
                            option.textContent = `${mp.nama} (${mp.kategori})`;
                            mataPelajaranSelectElement.appendChild(option);
                        });
                        mataPelajaranSelectElement.disabled = false;
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Semua mata pelajaran sudah dijadwalkan untuk kelas dan jurusan ini';
                        option.style.fontStyle = 'italic';
                        option.style.color = '#6b7280';
                        mataPelajaranSelectElement.appendChild(option);
                        mataPelajaranSelectElement.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mataPelajaranSelectElement.classList.remove('loading');
                    mataPelajaranSelectElement.innerHTML = '<option value="">Error loading data</option>';
                    mataPelajaranSelectElement.disabled = true;
                });
        }
    }

    // Event listeners for main form
    if (kelasSelect && jurusanSelect && mataPelajaranSelect) {
        kelasSelect.addEventListener('change', function() {
            updateMataPelajaran(kelasSelect, jurusanSelect, mataPelajaranSelect);
        });
        
        jurusanSelect.addEventListener('change', function() {
            updateMataPelajaran(kelasSelect, jurusanSelect, mataPelajaranSelect);
        });
    }

    // Event listeners for edit modal
    if (editKelasSelect && editJurusanSelect && editMataPelajaranSelect) {
        editKelasSelect.addEventListener('change', function() {
            updateMataPelajaran(editKelasSelect, editJurusanSelect, editMataPelajaranSelect);
        });
        
        editJurusanSelect.addEventListener('change', function() {
            updateMataPelajaran(editKelasSelect, editJurusanSelect, editMataPelajaranSelect);
        });
    }
            
    function checkDayAvailability() {
                const selectedDate = dateInput.value;
                const selectedKelasId = kelasSelect.value;

                if (selectedDate && selectedKelasId) {
                    // Get day name in Bahasa (assumed system in English, convert manually)
                    const dayNamesEnglishToIndonesian = {
                        'Monday': 'Senin',
                        'Tuesday': 'Selasa',
                        'Wednesday': 'Rabu',
                        'Thursday': 'Kamis',
                        'Friday': 'Jumat',
                        'Saturday': 'Sabtu',
                        'Sunday': 'Minggu'
                    };
                    const dayObj = new Date(selectedDate);
                    const dayEnglish = dayObj.toLocaleDateString('en-US', { weekday: 'long' });
                    const hariIndo = dayNamesEnglishToIndonesian[dayEnglish] || dayEnglish;

                    fetch(`?ajax=check_day_availability&hari=${encodeURIComponent(hariIndo)}&kelas_id=${selectedKelasId}`)
                    .then(response => response.json())
                    .then(data => {
                        dayWarning.textContent = data.message;
                        dayWarning.className = 'day-warning show';

                        if (!data.available) {
                            dayWarning.style.color = '#dc2626';
                            submitButton.disabled = true;
                            submitButton.style.opacity = '0.5';
                            submitButton.style.cursor = 'not-allowed';
                        } else {
                            dayWarning.style.color = data.current_count > 0 ? '#f59e0b' : '#16a34a';
                            submitButton.disabled = false;
                            submitButton.style.opacity = '1';
                            submitButton.style.cursor = 'pointer';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        dayWarning.textContent = 'Error checking availability';
                        dayWarning.className = 'day-warning show';
                        dayWarning.style.color = '#dc2626';
                    });
                }
                else {
                    dayWarning.className = 'day-warning';
                    submitButton.disabled = false;
                    submitButton.style.opacity = '1';
                    submitButton.style.cursor = 'pointer';
                }
            }

            kelasSelect.addEventListener('change', updateMataPelajaran);
            jurusanSelect.addEventListener('change', updateMataPelajaran);
            dateInput.addEventListener('change', checkDayAvailability);

            // Before submit validation
            document.getElementById('jadwalForm').addEventListener('submit', function(e) {
                if (submitButton.disabled) {
                    e.preventDefault();
                    alert('Tidak dapat menambah jadwal ujian. Hari pada tanggal sudah mencapai batas maksimal (6 jadwal).');
                }
            });
        });

function editKelas(id, nama) {
    document.getElementById('edit_kelas_id').value = id;
    document.getElementById('edit_kelas_nama').value = nama;
    document.getElementById('editKelasModal').style.display = 'block';
}

// Function untuk membuka modal edit jurusan
function editJurusan(id, nama) {
    document.getElementById('edit_jurusan_id').value = id;
    document.getElementById('edit_jurusan_nama').value = nama;
    document.getElementById('editJurusanModal').style.display = 'block';
}

// Function untuk membuka modal edit mata pelajaran
function editMataPelajaran(id, nama, kategori) {
    document.getElementById('edit_mp_id').value = id;
    document.getElementById('edit_mp_nama').value = nama;
    document.getElementById('edit_mp_kategori').value = kategori;
    document.getElementById('editMataPelajaranModal').style.display = 'block';
}

// Function untuk membuka modal edit jadwal ujian
function editJadwalUjian(id, kelasId, jurusanId, mataPelajaranId, date, hari, jamMulai, jamSelesai) {
    document.getElementById('edit_ju_id').value = id;
    document.getElementById('edit_ju_kelas').value = kelasId;
    document.getElementById('edit_ju_jurusan').value = jurusanId;
    document.getElementById('edit_ju_mata_pelajaran').value = mataPelajaranId;
    document.getElementById('edit_ju_date').value = date;
    document.getElementById('edit_ju_jam_mulai').value = jamMulai;
    document.getElementById('edit_ju_jam_selesai').value = jamSelesai;
    document.getElementById('editJadwalUjianModal').style.display = 'block';
}

// Function untuk menutup modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Function untuk konfirmasi hapus
function confirmDelete(table, id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus ${table} "${nama}"?`)) {
        window.location.href = `?delete=true&table=${table}&id=${id}`;
    }
}

// Close modal ketika klik di luar modal
window.onclick = function(event) {
    const modals = ['editKelasModal', 'editJurusanModal', 'editMataPelajaranModal', 'editJadwalUjianModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}
    </script>

    <!-- Modal Edit Kelas -->
<div id="editKelasModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editKelasModal')">&times;</span>
        <h3>Edit Kelas</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_kelas_id">
            <label for="edit_kelas_nama">Nama Kelas:</label>
            <input type="text" name="edit_nama" id="edit_kelas_nama" required maxlength="10">
            <button type="submit" name="edit_kelas">Update Kelas</button>
        </form>
    </div>
</div>

<!-- Modal Edit Jurusan -->
<div id="editJurusanModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editJurusanModal')">&times;</span>
        <h3>Edit Jurusan</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_jurusan_id">
            <label for="edit_jurusan_nama">Nama Jurusan:</label>
            <input type="text" name="edit_nama" id="edit_jurusan_nama" required maxlength="10">
            <button type="submit" name="edit_jurusan">Update Jurusan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Mata Pelajaran -->
<div id="editMataPelajaranModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editMataPelajaranModal')">&times;</span>
        <h3>Edit Mata Pelajaran</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_mp_id">
            <label for="edit_mp_nama">Nama Mata Pelajaran:</label>
            <input type="text" name="edit_nama" id="edit_mp_nama" required maxlength="100">
            <label for="edit_mp_kategori">Kategori:</label>
            <select name="edit_kategori" id="edit_mp_kategori" required>
                <option value="umum">Umum</option>
                <option value="ipa">Peminatan IPA</option>
                <option value="ips">Peminatan IPS</option>
            </select>
            <button type="submit" name="edit_mata_pelajaran">Update Mata Pelajaran</button>
        </form>
    </div>
</div>

<!-- Modal Edit Jadwal Ujian -->
<!-- Modal Edit Jadwal Ujian -->
    <div id="editJadwalUjianModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="editJadwalUjianTitle">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editJadwalUjianModal')" aria-label="Close">&times;</span>
            <h3 id="editJadwalUjianTitle">Edit Jadwal Ujian</h3>
            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_ju_id">
                
                <label for="edit_ju_kelas">Kelas:</label>
                <select name="edit_kelas_id" id="edit_ju_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?php echo $k['id'] ?>"><?php echo htmlspecialchars($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="edit_ju_jurusan">Jurusan:</label>
                <select name="edit_jurusan_id" id="edit_ju_jurusan" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo $j['id'] ?>"><?php echo htmlspecialchars($j['nama']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="edit_ju_mata_pelajaran">Mata Pelajaran:</label>
                <select name="edit_mata_pelajaran_id" id="edit_ju_mata_pelajaran" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach ($mata_pelajaran_list as $mp): ?>
                        <option value="<?php echo $mp['id'] ?>"><?php echo htmlspecialchars($mp['nama']) ?> (<?php echo htmlspecialchars($mp['kategori']) ?>)</option>
                    <?php endforeach; ?>
                </select>

                <label for="edit_ju_date">Tanggal:</label>
                <input type="date" name="edit_date" id="edit_ju_date" required />

                <!-- Removed manual hari select -->

                <label for="edit_ju_jam_mulai">Jam Mulai:</label>
                <input type="time" name="edit_jam_mulai" id="edit_ju_jam_mulai" required>

                <label for="edit_ju_jam_selesai">Jam Selesai:</label>
                <input type="time" name="edit_jam_selesai" id="edit_ju_jam_selesai" required>

                <button type="submit" name="edit_jadwal_ujian">Update Jadwal Ujian</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$connection->close();
?>