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

$id = $_GET['id'];
$result = $connection->query("SELECT * FROM ekstrakurikuler WHERE id_ekskul = $id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $connection->real_escape_string($_POST['name']);
    $desc = $connection->real_escape_string($_POST['description']);
    $constructor = $connection->real_escape_string($_POST['constructor']);

    if ($_FILES['image']['tmp_name']) {
        $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
        $sql = "UPDATE ekstrakurikuler SET name='$name', description='$desc', constructor='$constructor', image='$image' WHERE id_ekskul=$id";
    } else {
        $sql = "UPDATE ekstrakurikuler SET name='$name', description='$desc', constructor='$constructor' WHERE id_ekskul=$id";
    }

    $connection->query($sql);
    header("Location: admin_ekskul.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Ekstrakurikuler</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=6">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            padding: 80px 20px;
            color: white;
        }

        .form-container {
            max-width: 750px;
            margin: auto;
            background: #003366;
            color: rgb(255, 255, 255);
            padding: 35px 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color:rgb(255, 255, 255);
        }

        label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        button {
            margin-top: 20px;
            background-color: #00589D;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        button:hover {
            background-color: #00487f;
        }

        .preview {
            margin-top: 10px;
        }

        img.preview-img {
            margin-top: 10px;
            max-height: 200px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            color:rgb(3, 126, 220);
            font-weight: 600;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Ekstrakurikuler</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Nama:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($row['name']) ?>" required>

        <label for="constructor">Pembina:</label>
        <input type="text" name="constructor" id="constructor" value="<?= htmlspecialchars($row['constructor']) ?>" required>

        <label for="description">Deskripsi:</label>
        <textarea name="description" id="description" required><?= htmlspecialchars($row['description']) ?></textarea>

        <label for="image">Gambar Baru (jika ingin diganti):</label>
        <input type="file" name="image" id="image" accept="image/*">

        <?php if (!empty($row['image'])): ?>
        <div class="preview">
            <label>Gambar Saat Ini:</label><br>
            <img class="preview-img" src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar Saat Ini">
        </div>
        <?php endif; ?>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a class="back-link" href="admin_ekskul.php">← Kembali ke Daftar Ekskul</a>
</div>

</body>
</html>
