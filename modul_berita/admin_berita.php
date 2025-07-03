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

$sql = "SELECT b.id_berita, b.title, b.date, u.nama FROM berita b LEFT JOIN user u ON b.id_user = u.id_user ORDER BY date DESC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Berita</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=5">
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 40px; background:rgb(249, 249, 249); }
        h1 { color: #003366; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: #003366; box-shadow: 0 0 5px rgba(0,0,0,0.2); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ccc; }
        th { background: #00589D; color: white; }
        a.button { background: #00589D; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px; }
        .actions a {
            display: inline-block;
            margin: 6px 5px;
        }
        .tambah { margin-bottom: 20px; display: inline-block; }
        td img {
            max-width: 100px;
            height: auto;
            border: 1px solid #ccc;
        }
        .button.kembali {
            background: #888;
            color: white;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .button.kembali:hover {
            background: #666;
        }
    </style>
</head>
<body>

<h1>Kelola Berita</h1>
<a href="../../dashboard_admin.php" class="button kembali">← Kembali ke Dashboard</a>
<a href="tambah_berita.php" class="button tambah">+ Tambah Berita</a>

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
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <?php
                $imageResult = $connection->query("SELECT image FROM berita WHERE id_berita = " . $row['id_berita']);
                $imgData = $imageResult->fetch_assoc();
                if ($imgData && $imgData['image']) {
                    $base64 = base64_encode($imgData['image']);
                    echo "<img src='data:image/jpeg;base64,$base64' style='height: 80px; border-radius: 6px;'>";
                } else {
                    echo "Tidak ada gambar";
                }
                ?>
            </td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= date('d M Y', strtotime($row['date'])) ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td class="actions">
                <a href="edit_berita.php?id=<?= $row['id_berita'] ?>" class="button">Edit</a>
                <a href="hapus_berita.php?id=<?= $row['id_berita'] ?>" class="button" onclick="return confirm('Hapus berita ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
<?php $connection->close(); ?>