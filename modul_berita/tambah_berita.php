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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    $deskripsi = $connection->real_escape_string($_POST['deskripsi']);
    $date = date('Y-m-d');
    $id_user = $_SESSION['id_user'];

    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

    $sql = "INSERT INTO berita (title, deskripsi, date, image, id_user) 
            VALUES ('$title', '$deskripsi', '$date', '$image', $id_user)";
    $connection->query($sql);
    header("Location: admin_berita.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Berita</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=4">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: #fff;
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
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
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
            background-color: #00457c;
        }

        .img-preview {
            margin-top: 15px;
            max-height: 200px;
            border-radius: 8px;
        }

        .back-link {
            display: block;
            margin-top: 25px;
            text-align: center;
            color:rgb(255, 255, 255);
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Berita</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" required></textarea>

        <label for="image">Gambar</label>
        <input type="file" name="image" id="image" accept="image/*" onchange="previewImage()" required>
        <img id="preview" class="img-preview" style="display:none;" />

        <button type="submit" class="btn-submit">Simpan Berita</button>
    </form>

    <a class="back-link" href="admin_berita.php">← Kembali ke Daftar Berita</a>
</div>

<script>
    function previewImage() {
        const input = document.getElementById("image");
        const preview = document.getElementById("preview");
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    }
</script>

</body>
</html>