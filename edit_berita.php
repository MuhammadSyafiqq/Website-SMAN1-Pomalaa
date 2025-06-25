<?php
session_start();
require_once 'theme.php'; // sesuaikan path jika perlu
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

$id = $_GET['id'];
$result = $connection->query("SELECT * FROM berita WHERE id_berita = $id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    $deskripsi = $connection->real_escape_string($_POST['deskripsi']);
    $id_user = $_SESSION['id_user'];

    if ($_FILES['image']['tmp_name']) {
        $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
        $sql = "UPDATE berita SET title='$title', deskripsi='$deskripsi', image='$image', id_user=$id_user WHERE id_berita=$id";
    } else {
        $sql = "UPDATE berita SET title='$title', deskripsi='$deskripsi', id_user=$id_user WHERE id_berita=$id";
    }

    $connection->query($sql);
    header("Location: admin_berita.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Berita</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=4">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            padding: 60px 20px;
            color: white;
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
            margin-bottom: 25px;
            color:rgb(255, 255, 255);
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
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            margin-top: 20px;
            background-color: #00589D;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: #003f70;
        }

        .preview {
            margin-top: 15px;
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

        img.preview-img {
            margin-top: 10px;
            max-height: 200px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Berita</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="title">Judul:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($row['title']) ?>" required>

        <label for="deskripsi">Deskripsi:</label>
        <textarea name="deskripsi" id="deskripsi" rows="6" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>

        <label for="image">Gambar Baru (jika ingin diganti):</label>
        <input type="file" name="image" id="image" accept="image/*">

        <?php if (!empty($row['image'])): ?>
            <div class="preview">
                <label>Gambar Saat Ini:</label><br>
                <img class="preview-img" src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar Sebelumnya">
            </div>
        <?php endif; ?>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a class="back-link" href="admin_berita.php">← Kembali ke Daftar Berita</a>
</div>

</body>
</html>
