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
    header("Location: admin_berita.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Berita</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=5">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            margin: 0;
            padding: 40px 20px;
            color: #111;
        }

        .form-container {
            max-width: 700px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.25);
        }

        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 16px;
            color: #111827;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1.5px solid #ccc;
            color: #1f2937;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            margin-top: 24px;
            background-color: #00589D;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            font-weight: 600;
        }

        button:hover {
            background-color: #003f70;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-weight: 600;
            color: #00589D;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .preview {
            margin-top: 16px;
        }

        .preview label {
            margin-bottom: 8px;
            font-weight: bold;
            display: block;
        }

        img.preview-img {
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid #ccc;
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
                <label>Gambar Saat Ini:</label>
                <img class="preview-img" src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar Saat Ini">
            </div>
        <?php endif; ?>

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a class="back-link" href="admin_berita.php">← Kembali ke Daftar Berita</a>
</div>

</body>
</html>
