<?php
require_once('../koneksi.php');
session_start();
// Waktu timeout (dalam detik) — misal 15 menit = 900 detik
$timeout_duration = 900; 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();     // hapus semua session
    session_destroy();   // hancurkan session
    header("Location: login.php?timeout=true"); // redirect ke login (ganti dengan nama file login jika perlu)
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // perbarui waktu aktivitas terakhir

// Cek jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
require_once '../theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

$sql = "SELECT * FROM prestasi ORDER BY date DESC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Prestasi</title>
    <link rel="stylesheet" href="assets/style/style.css?v=10">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            padding: 50px 20px;
            color: white;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            color: black;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        h1 {
            text-align: center;
            color: #003366;
            margin-bottom: 25px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-link {
            background: #888;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-add {
            background-color: #00589D;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #00589D;
            color: white;
        }

        img.preview-img {
            height: 60px;
            border-radius: 6px;
        }

        .btn {
            display: inline-block;
            min-width: 60px;
            padding: 6px 10px;
            text-align: center;
            background-color: #00589D;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
        }

        .btn-danger {
            background-color: #c0392b;
        }

        .actions {
            white-space: nowrap;
            width: 130px;
        }

        .action-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar Prestasi</h1>

    <div class="top-bar">
        <a class="back-link" href="../dashboard_admin.php">← Kembali ke Dashboard</a>
        <a class="btn-add" href="tambah_prestasi.php">+ Tambah Prestasi</a>
    </div>

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
                    <div class="action-group">
                        <a href="edit_prestasi.php?id=<?= $row['id_prestasi'] ?>" class="btn">Edit</a>
                        <a href="hapus_prestasi.php?id=<?= $row['id_prestasi'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus prestasi ini?')">Hapus</a>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

</body>
</html>
