<?php
// File: tambah_slider.php
require_once '../config/database.php';
session_start();

// Debug: Tampilkan semua error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session timeout
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

require_once '../theme.php'; // hanya berisi fungsi, tidak mencetak HTML

// Ambil urutan yang sudah digunakan dari database
$used_orders = [];
$urut_query = $connection->query("SELECT urutan FROM slider ORDER BY urutan ASC");
while ($row = $urut_query->fetch_assoc()) {
    $used_orders[] = (int)$row['urutan'];
}

// Cegah error max() pada array kosong
$max_order = !empty($used_orders) ? max($used_orders) + 5 : 5;
$available_orders = [];
for ($i = 1; $i <= $max_order; $i++) {
    if (!in_array($i, $used_orders)) {
        $available_orders[] = $i;
    }
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $connection->real_escape_string($_POST['judul']);
    $deskripsi = $connection->real_escape_string($_POST['deskripsi']);
    $urutan = (int)$_POST['urutan'];
    $tampil = isset($_POST['tampil']) ? 1 : 0;

    if (!empty($_FILES['gambar']['tmp_name'])) {
        $gambar = addslashes(file_get_contents($_FILES['gambar']['tmp_name']));

        $sql = "INSERT INTO slider (judul, deskripsi, gambar, urutan, tampil) 
                VALUES ('$judul', '$deskripsi', '$gambar', $urutan, $tampil)";
        if ($connection->query($sql)) {
            header("Location: admin_slider.php?success=add");
            exit();
        } else {
            $error = "Gagal menambahkan slider.";
        }
    } else {
        $error = "File gambar tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id" data-theme="<?= getCurrentTheme(); ?>">
<head>
    <meta charset="UTF-8">
    <title>Tambah Slider</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=5">
    <style>
        <?= generateThemeCSS(); ?>

        body {
            font-family: 'Segoe UI', sans-serif;
            background: white;
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
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
        input[type="file"],
        select {
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
            background-color: #003020;
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

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Slider</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        <label for="judul">Judul</label>
        <input type="text" name="judul" id="judul" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" required></textarea>

        <label for="urutan">Urutan</label>
        <select name="urutan" id="urutan" required>
            <option value="">-- Pilih Urutan --</option>
            <?php foreach ($available_orders as $order): ?>
                <option value="<?= $order ?>"><?= $order ?></option>
            <?php endforeach; ?>
        </select>

        <label for="gambar">Gambar</label>
        <input type="file" name="gambar" id="gambar" accept="image/*" onchange="previewImage()" required>
        <img id="preview" class="img-preview" />

        <label><input type="checkbox" name="tampil" value="1" checked> Tampilkan di slider</label>

        <button type="submit" class="btn-submit" id="submitBtn">Simpan Slider</button>
    </form>

    <a class="back-link" href="admin_slider.php">← Kembali ke Daftar Slider</a>
</div>

<script>
    function previewImage() {
        const input = document.getElementById("gambar");
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
