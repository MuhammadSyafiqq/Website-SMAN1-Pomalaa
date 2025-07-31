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

$id = $_GET['id'];
$result = $connection->query("SELECT * FROM ekstrakurikuler WHERE id_ekskul = $id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $connection->real_escape_string($_POST['name']);
    $desc = $connection->real_escape_string($_POST['description']);
    $constructor = $connection->real_escape_string($_POST['constructor']);

    if ($_FILES['image']['tmp_name']) {
        $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
        $sql = "UPDATE ekstrakurikuler 
                SET name='$name', description='$desc', constructor='$constructor', image='$image' 
                WHERE id_ekskul=$id";
    } else {
        $sql = "UPDATE ekstrakurikuler 
                SET name='$name', description='$desc', constructor='$constructor' 
                WHERE id_ekskul=$id";
    }

    $connection->query($sql);
    header("Location: admin_ekskul.php?success=edit");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Ekstrakurikuler</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=6">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: white;
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
            color: #004030;
        }
        
        label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
            color: #004030;
        }
        
        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #004030;
            border-radius: 8px;
            color: black;
            margin-bottom: 15px;
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
        
        button {
            background-color: #004030;
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
            background-color: #003320;
        }
        
        button:disabled {
            background-color: #999;
            cursor: not-allowed;
        }
        
        .preview {
            margin-top: 15px;
        }
        
        .preview-img {
            max-height: 200px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        
        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: #004030;
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
    <h2>Edit Ekstrakurikuler</h2>
    <form method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        <label for="name">Nama:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($row['name']) ?>" required>

        <label for="constructor">Pembina:</label>
        <input type="text" name="constructor" id="constructor" value="<?= htmlspecialchars($row['constructor']) ?>" required>

        <label for="description">Deskripsi:</label>
        <textarea name="description" id="description" required><?= htmlspecialchars($row['description']) ?></textarea>

        <label for="image">Gambar Baru (jika ingin diganti):</label>
        <input type="file" name="image" id="image" accept="image/*">

        <?php if (!empty($row['image'])): ?>
        <div class="preview">
            <label>Gambar Saat Ini:</label><br>
            <img class="preview-img" src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar Saat Ini">
        </div>
        <?php endif; ?>

        <button type="submit" id="submitBtn">Simpan Perubahan</button>
    </form>

    <a class="back-link" href="admin_ekskul.php">← Kembali ke Daftar Ekskul</a>
</div>

<script>
function disableSubmitButton() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerText = "Menyimpan...";
}
</script>

</body>
</html>
