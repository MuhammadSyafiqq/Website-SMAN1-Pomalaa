<?php
require_once '../config/database.php';
session_start();
date_default_timezone_set('Asia/Makassar');

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
    <title>Kelola Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/style.css?v=<?= time() ?>">
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
            margin: 100px auto 40px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        h1 {
            text-align: center;
            color: #004030;
            margin-bottom: 30px;
        }

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
            background: #777;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 25px;
        }

        .back-link:hover {
            background-color: #555;
        }

        .search-box {
            margin-bottom: 20px;
            text-align: right;
        }

        .search-box input[type="text"] {
            padding: 8px 12px;
            width: 250px;
            color: black;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-box button {
            padding: 8px 14px;
            background-color: #004030;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 6px;
        }

        .search-box button:hover {
            background-color: #003320;
        }

        .search-box a {
            margin-left: 10px;
            color: #004030;
            text-decoration: underline;
            font-weight: bold;
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
        }

        th {
            background-color: #004030;
            color: white;
            font-weight: bold;
        }

        .btn {
            padding: 6px 10px;
            background-color: #004030;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 4px;
        }

        .btn:hover {
            background-color: #003320;
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
            gap: 6px;
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
            background-color: #004030;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .pagination a.active {
            background-color: #003320;
            font-weight: bold;
        }

        .pagination a:hover {
            background-color: #002610;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .search-box {
                text-align: left;
            }

            .search-box input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }

            .search-box button {
                width: 100%;
            }

            .pagination {
                font-size: 14px;
            }

            .actions {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 4px;
            }

            table {
                font-size: 13px;
                min-width: unset;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 20px;
            }

            .btn, .btn-danger {
                font-size: 11px;
                padding: 6px;
            }

            .back-link {
                font-size: 13px;
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>

<?php include '../partials/navbar.php'; ?>

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
                <a href="admin_feedback.php">Clear</a>
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
                            <td style="word-wrap: break-word; max-width: 300px;"><?= nl2br(htmlspecialchars($row['komentar'])) ?></td>
                            <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                            <td class="actions">
                                <a href="hapus_feedback.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus feedback ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 20px;">
                            <?= !empty($keyword) ? 'Tidak ada data yang sesuai pencarian.' : 'Tidak ada data feedback.' ?>
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

</body>
</html>

<?php 
if (isset($stmt)) $stmt->close(); 
$connection->close();
?>