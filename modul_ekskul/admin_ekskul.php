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
$result = $connection->query("SELECT * FROM ekstrakurikuler ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Ekstrakurikuler</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 40px; background: #00589D; }
        h1 { color:rgb(255, 255, 255); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #00589D; }
        th, td { padding: 12px; border: 1px solid #ccc; }
        th { background: #00589D; color: #fff; }
        .actions a { margin-right: 10px; color: white; background:rgb(16, 61, 95); padding: 6px 10px; text-decoration: none; border-radius: 5px; }
        .img-preview { height: 60px; object-fit: cover; border-radius: 4px; }
        .top-button { margin-bottom: 20px; display: inline-block; background:rgb(16, 61, 95); color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>

<h1>Data Ekstrakurikuler</h1>
<a href="tambah_ekskul.php" class="top-button">+ Tambah Ekskul</a>
<a href="../dashboard_admin.php" class="top-button" style="float:right;">Kembali</a>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Pembina</th>
            <th>Tanggal</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['constructor']) ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" class="img-preview"></td>
            <td class="actions">
                <a href="edit_ekskul.php?id=<?= $row['id_ekskul'] ?>">Edit</a>
                <a href="hapus_ekskul.php?id=<?= $row['id_ekskul'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
