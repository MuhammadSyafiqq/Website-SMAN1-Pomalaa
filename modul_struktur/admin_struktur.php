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

require_once '../theme.php';

// Cek jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
$result = $connection->query("SELECT * FROM struktur ORDER BY id_struktur DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Struktur</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=12">
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

        .top-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .btn {
            padding: 8px 14px;
            background-color: #00589D;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #003f70;
        }

        .btn-danger {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #00589D;
            color: white;
        }

        img.photo-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .actions a {
            display: inline-block;
            margin: 3px 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Daftar Struktur Pegawai</h1>
    <div class="top-actions">
        <a href="../dashboard_admin.php" class="btn" style="background: gray;">← Kembali ke Dashboard</a>
        <a href="tambah_struktur.php" class="btn">+ Tambah Struktur</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th>Status</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['nip']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php if (!empty($row['photo'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['photo']) ?>" class="photo-preview" alt="Foto">
                        <?php else: ?>
                            <em>Tidak ada</em>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a href="edit_struktur.php?id=<?= $row['id_struktur'] ?>" class="btn">Edit</a>
                        <a href="hapus_struktur.php?id=<?= $row['id_struktur'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
