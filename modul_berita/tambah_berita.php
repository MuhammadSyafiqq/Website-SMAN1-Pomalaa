<?php
require_once '../config/database.php';
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $connection->real_escape_string($_POST['title']);
    $deskripsi = $connection->real_escape_string($_POST['deskripsi']);
    $date = date('Y-m-d');
    $id_user = $_SESSION['id_user'];
    $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));

    // Cek duplikasi berdasarkan judul dan tanggal
    $check_sql = "SELECT * FROM berita 
                  WHERE title='$title' AND date='$date' AND id_user=$id_user";
    $check_result = $connection->query($check_sql);

    if ($check_result->num_rows == 0) {
        $sql = "INSERT INTO berita (title, deskripsi, date, image, id_user) 
                VALUES ('$title', '$deskripsi', '$date', '$image', $id_user)";
        $connection->query($sql);
        header("Location: admin_berita.php?added=1");
        exit();
    } else {
        header("Location: admin_berita.php?error=duplicate");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Berita</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=5">
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
        color: #004030;
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 20px;
    }

    label {
        font-weight: 600;
        margin-top: 15px;
        display: block;
        color: #004030;
    }

    input[type="text"],
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 12px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #004030;
        color: #1f2937;
        font-size: 16px;
        outline: none;
    }

    textarea {
        resize: vertical;
        min-height: 120px;
    }

    input[type="file"] {
        border: 1px dashed #004030;
    }

    .btn-submit {
        margin-top: 25px;
        background-color: #004030;
        color: white;
        padding: 12px 25px;
        border: none;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        width: 100%;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background-color: #002c20;
    }

    .btn-submit:disabled {
        background-color: #888;
        cursor: not-allowed;
    }

    .img-preview {
        margin-top: 15px;
        max-height: 200px;
        border-radius: 8px;
        display: none;
    }

    .back-link {
        display: block;
        margin-top: 20px;
        text-align: center;
        color: #004030;
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
    <form method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        <label for="title">Judul</label>
        <input type="text" name="title" id="title" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" required></textarea>

        <label for="image">Gambar</label>
        <input type="file" name="image" id="image" accept="image/*" onchange="previewImage()" required>
        <img id="preview" class="img-preview" />

        <button type="submit" class="btn-submit" id="submitBtn">Simpan Berita</button>
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

    function disableSubmitButton() {
        const btn = document.getElementById("submitBtn");
        btn.disabled = true;
        btn.innerText = "Menyimpan...";
    }
</script>

</body>
</html>
