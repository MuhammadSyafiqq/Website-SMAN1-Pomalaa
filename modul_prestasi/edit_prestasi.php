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

if (!isset($_GET['id'])) {
    header("Location: admin_prestasi.php");
    exit();
}

$id = (int) $_GET['id'];
$query = $connection->query("SELECT * FROM prestasi WHERE id_prestasi = $id");
$data = $query->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    $description = $connection->real_escape_string($_POST['description']);
    $level = $connection->real_escape_string($_POST['level']);
    $date = $connection->real_escape_string($_POST['date']);
    $category = $connection->real_escape_string($_POST['category']);
    $image = $_FILES['image']['tmp_name'] ? addslashes(file_get_contents($_FILES['image']['tmp_name'])) : null;

    if ($image) {
        $sql = "UPDATE prestasi SET title='$title', description='$description', level='$level', date='$date', category='$category', image='$image' WHERE id_prestasi=$id";
    } else {
        $sql = "UPDATE prestasi SET title='$title', description='$description', level='$level', date='$date', category='$category' WHERE id_prestasi=$id";
    }

    if ($connection->query($sql)) {
        header("Location: admin_prestasi.php?success=edit");
        exit();
    } else {
        echo "Gagal mengupdate: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Prestasi</title>
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
    </style>
</head>
<body>
<div class="form-container">
    <h2>Edit Prestasi</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($data['title']) ?>" required>

        <label for="description">Deskripsi</label>
        <textarea name="description" id="description" required><?= htmlspecialchars($data['description']) ?></textarea>

        <label for="level">Level</label>
        <select name="level" required>
            <?php
            $levels = ["SEKOLAH", "KABUPATEN", "PROVINSI", "NASIONAL", "INTERNASIONAL"];
            foreach ($levels as $lvl) {
                echo "<option value='$lvl'" . ($lvl == $data['level'] ? " selected" : "") . ">$lvl</option>";
            }
            ?>
        </select>

        <label for="date">Tanggal</label>
        <input type="date" name="date" value="<?= $data['date'] ?>" required>

        <label for="category">Kategori</label>
        <select name="category" required>
            <?php
            $categories = ["siswa", "guru", "ekstrakurikuler"];
            foreach ($categories as $cat) {
                echo "<option value='$cat'" . ($cat == $data['category'] ? " selected" : "") . ">$cat</option>";
            }
            ?>
        </select>

        <label for="image">Gambar (kosongkan jika tidak diubah)</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a class="back-link" href="admin_prestasi.php">← Kembali ke Daftar Prestasi</a>
</div>
</body>
</html>
