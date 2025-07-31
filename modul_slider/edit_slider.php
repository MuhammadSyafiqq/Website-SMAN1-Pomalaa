<?php
require_once '../config/database.php';
session_start();

// Cek timeout session
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Autentikasi
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = (int)$_GET['id'];
$data = $connection->query("SELECT * FROM slider WHERE id = $id")->fetch_assoc();
if (!$data) {
    die("Data slider tidak ditemukan.");
}

// Ambil urutan yang sudah digunakan, kecuali yang sedang diedit
$used_orders = [];
$urut_query = $connection->query("SELECT urutan FROM slider WHERE id != $id ORDER BY urutan ASC");
while ($row = $urut_query->fetch_assoc()) {
    $used_orders[] = (int)$row['urutan'];
}
$max_order = !empty($used_orders) ? max($used_orders) + 5 : 10;
$available_orders = [];
for ($i = 1; $i <= $max_order; $i++) {
    if (!in_array($i, $used_orders)) {
        $available_orders[] = $i;
    }
}

// Handle submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $connection->real_escape_string($_POST['judul']);
    $deskripsi = $connection->real_escape_string($_POST['deskripsi']);
    $urutan = (int)$_POST['urutan'];
    $tampil = isset($_POST['tampil']) ? 1 : 0;

    if (!empty($_FILES['gambar']['tmp_name'])) {
        $gambar = addslashes(file_get_contents($_FILES['gambar']['tmp_name']));
        $stmt = $connection->prepare("UPDATE slider SET judul=?, deskripsi=?, gambar=?, urutan=?, tampil=? WHERE id=? LIMIT 1");
        $stmt->bind_param("sssiii", $judul, $deskripsi, $gambar, $urutan, $tampil, $id);
    } else {
        $stmt = $connection->prepare("UPDATE slider SET judul=?, deskripsi=?, urutan=?, tampil=? WHERE id=? LIMIT 1");
        $stmt->bind_param("ssiii", $judul, $deskripsi, $urutan, $tampil, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: admin_slider.php?success=edit");
        exit();
    } else {
        $error = "Gagal memperbarui data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Slider</title>
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
    <h2>Edit Slider</h2>
    <form method="post" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        <label for="judul">Judul</label>
        <input type="text" name="judul" id="judul" value="<?= htmlspecialchars($data['judul']) ?>" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>

        <label for="urutan">Urutan</label>
        <select name="urutan" id="urutan" required>
            <option value="">-- Pilih Urutan --</option>
            <?php foreach ($available_orders as $order): ?>
                <option value="<?= $order ?>" <?= $order == $data['urutan'] ? 'selected' : '' ?>><?= $order ?></option>
            <?php endforeach; ?>
        </select>

        <label for="gambar">Ganti Gambar (kosongkan jika tidak ingin diubah)</label>
        <input type="file" name="gambar" id="gambar" accept="image/*" onchange="previewImage()">
        <?php if (!empty($data['gambar'])): ?>
            <img id="preview" class="img-preview" src="data:image/jpeg;base64,<?= base64_encode($data['gambar']) ?>" />
        <?php else: ?>
            <img id="preview" class="img-preview" style="display: none;" />
        <?php endif; ?>

        <label><input type="checkbox" name="tampil" value="1" <?= $data['tampil'] ? 'checked' : '' ?>> Tampilkan di slider</label>

        <button type="submit" class="btn-submit" id="submitBtn">Simpan Perubahan</button>
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
