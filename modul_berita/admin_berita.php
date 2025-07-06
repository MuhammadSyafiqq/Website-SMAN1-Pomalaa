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

$sql = "SELECT b.id_berita, b.title, b.date, u.nama, b.image 
        FROM berita b 
        LEFT JOIN user u ON b.id_user = u.id_user 
        ORDER BY b.date DESC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Berita</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=11">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: white;
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

        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 600;
        }

        .alert.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert.info {
            background-color: #dbeafe;
            color: #1e3a8a;
        }

        .alert.error {
            background-color: #fee2e2;
            color: #991b1b;
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

        thead th {
            background-color: #00589D;
            color: white;
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
            background-color: #00589D;
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
    </style>
</head>
<body>
<div class="container">
    <h1>Kelola Berita</h1>

    <!-- ✅ Notifikasi Sukses -->
    <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
        <div class="alert success">Berita berhasil ditambahkan!</div>
    <?php elseif (isset($_GET['edited']) && $_GET['edited'] == 1): ?>
        <div class="alert info">Berita berhasil diperbarui.</div>
    <?php elseif (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
        <div class="alert error">Berita berhasil dihapus.</div>
    <?php endif; ?>

    <a href="../../dashboard_admin.php" class="btn btn-back">← Kembali ke Dashboard</a>
    <a href="tambah_berita.php" class="btn btn-add">+ Tambah Berita</a>

    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Ditulis oleh</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php
                        if (!empty($row['image'])) {
                            $base64 = base64_encode($row['image']);
                            echo "<img src='data:image/jpeg;base64,$base64'>";
                        } else {
                            echo "<i>Tidak ada</i>";
                        }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= date('d M Y', strtotime($row['date'])) ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="actions">
                        <a href="edit_berita.php?id=<?= $row['id_berita'] ?>" class="edit">Edit</a>
                        <a href="hapus_berita.php?id=<?= $row['id_berita'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus berita ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $connection->close(); ?>
