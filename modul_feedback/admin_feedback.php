<?php
require_once('../koneksi.php');
session_start();

$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

// Konfigurasi pagination
$limit = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total data
$total_result = $connection->query("SELECT COUNT(*) AS total FROM feedback");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Ambil data sesuai halaman
$sql = "SELECT * FROM feedback ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $connection->query($sql);

// Notifikasi
$notif = '';
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'reply') {
        $notif = 'Balasan berhasil dikirim.';
    } elseif ($_GET['success'] === 'delete') {
        $notif = 'Feedback berhasil dihapus.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Feedback</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=17">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 60px 20px;
            color: #fff;
        }

        .container {
            max-width: 1000px;
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

        .notif {
            padding: 12px 18px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 6px;
            border-left: 6px solid #3c763d;
            margin-bottom: 20px;
            font-weight: bold;
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

        .back-link:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #00589D;
            color: white;
        }

        td.balasan {
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        .btn {
            padding: 8px 12px;
            background-color: #00589D;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
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
            gap: 6px;
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
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar Feedback</h1>

    <?php if ($notif): ?>
        <div class="notif"><?= htmlspecialchars($notif) ?></div>
    <?php endif; ?>

    <a class="back-link" href="../dashboard_admin.php">← Kembali ke Dashboard</a>

    <table>
        <thead>
            <tr>
                <th style="width: 120px;">Nama</th>
                <th style="width: 200px;">Komentar</th>
                <th style="width: 150px;">Waktu</th>
                <th style="width: 250px;">Balasan</th>
                <th style="width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['komentar']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                    <td class="balasan"><?= $row['balasan'] ? htmlspecialchars($row['balasan']) : '<em>Belum dibalas</em>' ?></td>
                    <td class="actions">
                        <a href="balas_feedback.php?id=<?= $row['id'] ?>" class="btn">Balas</a>
                        <a href="hapus_feedback.php?id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus feedback ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
