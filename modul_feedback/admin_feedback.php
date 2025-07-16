<?php
require_once '../config/database.php';
session_start();

// Durasi timeout sesi dalam detik (15 menit)
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?timeout=true"); // Sesuaikan path login.php jika berbeda
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php"); // Sesuaikan path login.php jika berbeda
    exit();
}

require_once '../theme.php'; // Sesuaikan path jika berbeda

// Ambil keyword pencarian jika ada
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Konfigurasi pagination
$limit = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
if (!empty($keyword)) {
    $stmt = $connection->prepare("SELECT COUNT(*) AS total FROM feedback WHERE nama LIKE ? OR komentar LIKE ?");
    $like = '%' . $keyword . '%';
    $stmt->bind_param("ss", $like, $like);
} else {
    $stmt = $connection->prepare("SELECT COUNT(*) AS total FROM feedback");
}
$stmt->execute();
$result_count = $stmt->get_result();
$total_rows = $result_count->fetch_assoc()['total'];
$stmt->close();
$total_pages = ceil($total_rows / $limit);

// Ambil data sesuai halaman dan pencarian
if (!empty($keyword)) {
    $stmt = $connection->prepare("SELECT * FROM feedback WHERE nama LIKE ? OR komentar LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $like, $like, $limit, $offset);
} else {
    $stmt = $connection->prepare("SELECT * FROM feedback ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

// Notifikasi
$notif_message = '';
$notif_type = ''; // 'success' atau 'error'

if (isset($_GET['success'])) {
    $notif_type = 'success';
    if ($_GET['success'] === 'reply') {
        $notif_message = 'Balasan berhasil dikirim.';
    } elseif ($_GET['success'] === 'delete') {
        $notif_message = 'Feedback berhasil dihapus.';
    } elseif ($_GET['success'] === 'toggle') {
        $notif_message = 'Status tampil berhasil diubah.';
    }
} elseif (isset($_GET['error'])) {
    $notif_type = 'error';
    if ($_GET['error'] === 'invalid_request') {
        $notif_message = 'Permintaan tidak valid. Silakan coba lagi.';
    } elseif ($_GET['error'] === 'db_prepare_failed') {
        $notif_message = 'Terjadi kesalahan database saat menyiapkan operasi.';
    } elseif ($_GET['error'] === 'db_execute_failed') {
        $notif_message = 'Terjadi kesalahan database saat mengeksekusi operasi.';
    } elseif ($_GET['error'] === 'toggle_failed') { // Jika Anda masih pakai nama error ini dari versi toggle_tampil.php sebelumnya
        $notif_message = 'Gagal mengubah status tampil feedback.';
    } else {
        $notif_message = 'Terjadi kesalahan yang tidak diketahui.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-store" />
    <title>Kelola Feedback</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=18">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 60px 20px;
            color: #000;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #ffffff;
            color: #000;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }

        /* Gaya notifikasi */
        .notif {
            padding: 12px 18px;
            border-radius: 6px;
            border-left: 6px solid;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .notif.success {
            background-color: #dff0d8;
            color: #3c763d;
            border-color: #3c763d;
        }

        .notif.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #721c24;
        }

        .back-link {
            background: #888;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .search-box {
            margin-bottom: 20px;
            text-align: right;
        }

        .search-box input[type="text"] {
            padding: 8px 12px;
            color: black;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-box button {
            padding: 8px 14px;
            background-color: #00589D;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 6px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            min-width: 800px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 12px 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #00589D;
            color: white;
            font-weight: bold;
        }

        td.balasan {
            word-wrap: break-word;
            white-space: pre-wrap;
            max-width: 200px;
        }

        .btn {
            padding: 6px 10px;
            background-color: #00589D;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 4px;
            display: inline-block;
        }

        .btn:hover {
            background-color: #003f70;
        }

        .btn-danger {
            background-color: #c0392b;
        }

        .btn-danger:hover {
            background-color: #a83226;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 100px;
        }

        .pagination {
            margin-top: 30px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 14px;
            margin: 0 4px;
            background-color: #00589D;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .pagination a.active {
            background-color: #003f70;
            font-weight: bold;
        }

        .pagination a:hover {
            background-color: #00497f;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider-switch {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 24px;
        }
        
        .slider-switch:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        
        .toggle-switch input:checked + .slider-switch {
            background-color: #00589D;
        }
        
        .toggle-switch input:checked + .slider-switch:before {
            transform: translateX(22px);
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        .status-aktif {
            background-color: #d4edda;
            color: #155724;
        }

        .status-nonaktif {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .search-box {
                text-align: left;
            }
            
            .search-box input[type="text"] {
                width: 200px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar Feedback</h1>

    <?php if ($notif_message): ?>
        <div class="notif <?= $notif_type ?>">
            <?= htmlspecialchars($notif_message) ?>
        </div>
    <?php endif; ?>

    <a class="back-link" href="../dashboard_admin.php">← Kembali ke Dashboard</a>

    <div class="search-box">
        <form method="GET">
            <input type="text" name="keyword" placeholder="Cari Nama atau Komentar..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit">Cari</button>
            <?php if (!empty($keyword)): ?>
                <a href="admin_feedback.php" style="margin-left: 10px; color: #00589D;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 120px;">Nama</th>
                    <th style="width: 300px;">Komentar</th>
                    <th style="width: 140px;">Waktu</th>
                    <th style="width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td style="word-wrap: break-word; max-width: 300px;"><?= htmlspecialchars($row['komentar']) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                            <td class="actions">
                                <a href="hapus_feedback.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus feedback ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 20px;">
                            <?= !empty($keyword) ? 'Tidak ada data yang sesuai dengan pencarian.' : 'Tidak ada data feedback.' ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page - 1 ?>">← Prev</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?keyword=<?= urlencode($keyword) ?>&page=<?= $page + 1 ?>">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Tambahkan loading indicator saat toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.toggle-switch input[type="checkbox"]');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const slider = this.nextElementSibling;
            slider.style.opacity = '0.6';
            slider.style.pointerEvents = 'none';
        });
    });
});
</script>

</body>
</html>

<?php 
if (isset($stmt)) {
    $stmt->close(); 
}
$connection->close();
?>