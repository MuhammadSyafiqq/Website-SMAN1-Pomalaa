<?php
// ====== File: edit_prestasi.php ======
session_start();
require_once 'theme.php';
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
        header("Location: admin_prestasi.php");
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
    <link rel="stylesheet" href="assets/style/style.css?v=15">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
            padding: 50px 20px;
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
    <h2>Edit Prestasi</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Judul</label>
        <input type="text" name="title" value="<?= htmlspecialchars($data['title']) ?>" required>

        <label for="description">Deskripsi</label>
        <textarea name="description" rows="5" required><?= htmlspecialchars($data['description']) ?></textarea>

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