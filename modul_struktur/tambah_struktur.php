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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $position = $_POST['position'];
    $status = $_POST['status'];
    $photo = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = file_get_contents($_FILES['photo']['tmp_name']);
    }

    $stmt = $connection->prepare("INSERT INTO struktur (nama, nip, position, status, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $nip, $position, $status, $photo);
    $stmt->send_long_data(4, $photo);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_struktur.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Struktur</title>
    <link rel="stylesheet" href="assets/style/style.css?v=8">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
            padding: 40px;
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
            margin-bottom: 25px;
            color:rgb(255, 255, 255);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #00589D;
            color: white;
        }

        select option {
            background-color: #00589D;
            color: white;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #00589D;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #003f70;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #888;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Struktur</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="nama">Nama:</label>
        <input type="text" name="nama" required>

        <label for="nip">NIP:</label>
        <input type="text" name="nip">

        <label for="position">Jabatan/Posisi:</label>
        <input type="text" name="position" required>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="Guru">Guru</option>
            <option value="Staf">Staf</option>
            <option value="">Lainnya</option>
        </select>

        <label for="photo">Foto:</label>
        <input type="file" name="photo" accept="image/*">

        <button type="submit">Simpan</button>
    </form>

    <a href="admin_struktur.php" class="back-btn">← Kembali ke Daftar Struktur</a>
</div>

</body>
</html>
