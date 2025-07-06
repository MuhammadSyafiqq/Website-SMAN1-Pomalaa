<?php
require_once('../koneksi.php');
session_start();

// Timeout 15 menit
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

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $level = trim($_POST['level']);
    $date = trim($_POST['date']);
    $category = trim($_POST['category']);
    $image_uploaded = isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;

    // Validasi semua input wajib diisi
    if (empty($title) || empty($description) || empty($level) || empty($date) || empty($category) || !$image_uploaded) {
        $error = 'Semua form wajib diisi, termasuk gambar.';
    } else {
        $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

        $sql = "INSERT INTO prestasi (title, description, level, date, category, image)
                VALUES ('$title', '$description', '$level', '$date', '$category', '$image')";

        if ($connection->query($sql) === TRUE) {
            header("Location: admin_prestasi.php?success=add");
            exit();
        } else {
            $error = "Gagal menyimpan data: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Prestasi</title>
    <link rel="stylesheet" href="assets/style/style.css?v=16">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 60px 20px;
            color: #fff;
        }

        .form-container {
            max-width: 750px;
            margin: auto;
            background: #ffffff;
            color: #000;
            padding: 35px 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #003366;
        }

        label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
            color: #003366;
        }

        input[type="text"],
        textarea,
        select,
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #00589D;
            border-radius: 8px;
            color: black;
            margin-bottom: 15px;
            font-size: 16px;
            outline: none;
        }

        select option {
            background-color: #fff;
            color: #000;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        input[type="file"] {
            border: 1px dashed #00589D;
        }

        button {
            background-color: #00589D;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #00487f;
        }

        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: #00589D;
            font-weight: bold;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error-msg {
            background-color: #ffe6e6;
            color: #cc0000;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Prestasi</h2>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Deskripsi</label>
        <textarea name="description" id="description" required></textarea>

        <label for="level">Level</label>
        <select name="level" id="level" required>
            <option value="">-- Pilih Level --</option>
            <option value="SEKOLAH">SEKOLAH</option>
            <option value="KABUPATEN">KABUPATEN</option>
            <option value="PROVINSI">PROVINSI</option>
            <option value="NASIONAL">NASIONAL</option>
            <option value="INTERNASIONAL">INTERNASIONAL</option>
        </select>

        <label for="date">Tanggal</label>
        <input type="date" name="date" id="date" required>

        <label for="category">Kategori</label>
        <select name="category" id="category" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="siswa">siswa</option>
            <option value="guru">guru</option>
            <option value="ekstrakurikuler">ekstrakurikuler</option>
        </select>

        <label for="image">Gambar</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit">Simpan</button>
    </form>

    <a class="back-link" href="admin_prestasi.php">← Kembali ke Daftar Prestasi</a>
</div>

</body>
</html>
