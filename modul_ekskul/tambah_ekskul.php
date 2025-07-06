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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $connection->real_escape_string($_POST['name']);
    $desc = $connection->real_escape_string($_POST['description']);
    $date = date('Y-m-d');
    $constructor = $connection->real_escape_string($_POST['constructor']);
    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

    $sql = "INSERT INTO ekstrakurikuler (name, description, date, constructor, image)
            VALUES ('$name', '$desc', '$date', '$constructor', '$image')";
    $connection->query($sql);

    // Redirect ke admin_ekskul.php dengan parameter notifikasi
    header("Location: admin_ekskul.php?success=add");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Ekstrakurikuler</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f6f9ff;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: white;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            color: #003366;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            color: black;
            border-radius: 6px;
            border: 1px solid #00589D;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            margin-top: 25px;
            background-color: #00589D;
            color: white;
            padding: 12px 25px;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #003f73;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #00589D;
            font-weight: bold;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Ekstrakurikuler</h2>

    <form method="post" enctype="multipart/form-data">
        <label for="name">Nama:</label>
        <input type="text" name="name" id="name" required>

        <label for="constructor">Pembina:</label>
        <input type="text" name="constructor" id="constructor" required>

        <label for="description">Deskripsi:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="image">Gambar:</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit" class="btn-submit">Simpan</button>
    </form>

    <a class="back-link" href="admin_ekskul.php">← Kembali ke Daftar Ekskul</a>
</div>

</body>
</html>
