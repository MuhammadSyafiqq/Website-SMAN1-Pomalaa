<?php
session_start();
require_once 'theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    $description = $connection->real_escape_string($_POST['description']);
    $level = $connection->real_escape_string($_POST['level']);
    $date = $connection->real_escape_string($_POST['date']);
    $category = $connection->real_escape_string($_POST['category']);
    $image = $_FILES['image']['tmp_name'] ? addslashes(file_get_contents($_FILES['image']['tmp_name'])) : null;

    $sql = "INSERT INTO prestasi (title, description, level, date, category, image)
            VALUES ('$title', '$description', '$level', '$date', '$category', '$image')";

    if ($connection->query($sql) === TRUE) {
        header("Location: admin_prestasi.php");
        exit();
    } else {
        echo "Gagal menyimpan data: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Prestasi</title>
    <link rel="stylesheet" href="assets/style/style.css?v=15">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
            padding: 50px 20px;
        }
        .form-container {
            max-width: 700px;
            margin: auto;
            background:003366;
            color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            color:rgb(255, 255, 255);
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"],
        textarea,
        select,
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: #00589D;
            color: white;
        }
        select option {
            background-color: #00589D;
            color: white;
        }
        input[type="file"] {
            margin-top: 8px;
            padding: 5px;
        }
        button {
            margin-top: 25px;
            background-color: #00589D;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            display: block;
            width: 100%;
        }
        button:hover {
            background-color: #003f70;
        }
        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            color:rgb(255, 255, 255);
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
    <h2>Tambah Prestasi</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Deskripsi</label>
        <textarea name="description" id="description" rows="5" required></textarea>

        <label for="level">Level</label>
        <select name="level" id="level" required>
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
            <option value="siswa">siswa</option>
            <option value="guru">guru</option>
            <option value="ekstrakurikuler">ekstrakurikuler</option>
        </select>

        <label for="image">Gambar</label>
        <input type="file" name="image" id="image" accept="image/*">

        <button type="submit">Simpan</button>

    </form>

    <a class="back-link" href="admin_prestasi.php">← Kembali ke Daftar Prestasi</a>
</div>

</body>
</html>
