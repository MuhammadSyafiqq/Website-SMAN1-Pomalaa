<?php
require_once('../koneksi.php');
session_start();

// Timeout session 15 menit
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
$result = $connection->query("SELECT * FROM prestasi ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Prestasi</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=13">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f6fc;
            padding: 40px;
            margin: 0;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-back {
            background-color: #888;
            color: white;
            margin-bottom: 15px;
        }

        .btn-back:hover {
            background-color: #666;
        }

        .btn-add {
            float: right;
            background-color: #00589D;
            color: white;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background-color: #00417a;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
        }

        thead {
            background-color: #00589D !important;
        }

        thead th {
            background-color: #00589D !important;
            color: white !important;
            padding: 12px;
            text-align: left;
            border-right: 1px solid #ddd;
        }

        thead th:last-child {
            border-right: none;
        }

        tbody td {
            background-color: white;
            padding: 12px;
            vertical-align: middle;
            color: #000;
            border-top: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }

        tbody tr:last-child td {
            border-bottom: 1px solid #ddd;
        }

        tbody td:last-child {
            border-right: none;
        }

        td img {
            height: 60px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .actions a {
            margin-right: 8px;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }

        .actions .edit {
            background-color: #1d4ed8;
            color: white;
        }

        .actions .edit:hover {
            background-color: #1e40af;
        }

        .actions .delete {
            background-color: #dc2626;
            color: white;
        }

        .actions .delete:hover {
            background-color: #b91c1c;
        }

        .notif {
            margin-bottom: 20px;
            padding: 12px 18px;
            border-radius: 8px;
            color: #0f5132;
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            font-size: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Kelola Prestasi</h1>

    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] === 'add'): ?>
            <div class="notif">✅ Prestasi berhasil ditambahkan.</div>
        <?php elseif ($_GET['success'] === 'edit'): ?>
            <div class="notif">✏️ Prestasi berhasil diperbarui.</div>
        <?php elseif ($_GET['success'] === 'delete'): ?>
            <div class="notif">🗑️ Prestasi berhasil dihapus.</div>
        <?php endif; ?>
    <?php endif; ?>

    <a href="../dashboard_admin.php" class="btn btn-back">← Kembali ke Dashboard</a>
    <a href="tambah_prestasi.php" class="btn btn-add">+ Tambah Prestasi</a>

    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Level</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Gambar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['level']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= date('d M Y', strtotime($row['date'])) ?></td>
                <td>
                    <?php if ($row['image']): ?>
                        <img class="preview-img" src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar">
                    <?php else: ?>
                        <em>Tidak ada</em>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="edit_prestasi.php?id=<?= $row['id_prestasi'] ?>" class="edit">Edit</a>
                    <a href="hapus_prestasi.php?id=<?= $row['id_prestasi'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus prestasi ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $connection->close(); ?>