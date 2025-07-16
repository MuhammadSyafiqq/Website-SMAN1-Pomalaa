<?php
session_start();
require_once '../config/database.php';

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

$id = $_GET['id'];
$query = $connection->prepare("SELECT * FROM struktur WHERE id_struktur = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $position = $_POST['position'];
    $status = $_POST['status'];

    if (
        empty($nama) || empty($nip) || empty($position) || empty($status)
    ) {
        $error = "Semua field wajib diisi.";
    } elseif (!ctype_digit($nip)) {
        $error = "NIP hanya boleh diisi dengan angka.";
    } elseif (strlen($nip) > 50) {
        $error = "NIP maksimal 50 digit.";
    } else {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photo = file_get_contents($_FILES['photo']['tmp_name']);
            $stmt = $connection->prepare("UPDATE struktur SET nama=?, nip=?, position=?, status=?, photo=? WHERE id_struktur=?");
            $stmt->bind_param("sssssi", $nama, $nip, $position, $status, $photo, $id);
            $stmt->send_long_data(4, $photo);
        } else {
            $stmt = $connection->prepare("UPDATE struktur SET nama=?, nip=?, position=?, status=? WHERE id_struktur=?");
            $stmt->bind_param("ssssi", $nama, $nip, $position, $status, $id);
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: admin_struktur.php?success=edit");
            exit();
        } else {
            $error = "Gagal memperbarui data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Struktur</title>
    <link rel="stylesheet" href="assets/style/style.css?v=11">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            margin: 0;
            padding: 60px 20px;
            color: #000;
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
        select,
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

        button:disabled {
            background-color: #aaa;
            cursor: not-allowed;
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

        .error-msg {
            background-color: #ff4d4d;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Struktur</h2>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        <label for="nama">Nama</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

        <label for="nip">NIP</label>
        <input type="text" name="nip" value="<?= htmlspecialchars($data['nip']) ?>" required>

        <label for="position">Jabatan/Posisi</label>
        <select name="position" required>
            <option value="">-- Pilih Jabatan --</option>
            <option value="Kepala Sekolah" <?= $data['position'] == 'Kepala Sekolah' ? 'selected' : '' ?>>Kepala Sekolah</option>
            <option value="Wakasek Kesiswaan" <?= $data['position'] == 'Wakasek Kesiswaan' ? 'selected' : '' ?>>Wakasek Kesiswaan</option>
            <option value="Wakasek Prasarana" <?= $data['position'] == 'Wakasek Prasarana' ? 'selected' : '' ?>>Wakasek Prasarana</option>
            <option value=" " <?= trim($data['position']) == '' ? 'selected' : '' ?>>None</option>
        </select>

        <label for="status">Status</label>
        <input type="text" name="status" value="<?= htmlspecialchars($data['status']) ?>" placeholder="Contoh: Guru Matematika" required>

        <label for="photo">Foto (biarkan kosong jika tidak diubah)</label>
        <input type="file" name="photo" accept="image/*">

        <button type="submit" id="submitBtn">Simpan Perubahan</button>
    </form>

    <a class="back-link" href="admin_struktur.php">← Kembali ke Daftar Struktur</a>
</div>

<script>
    function disableSubmitButton() {
        const btn = document.getElementById("submitBtn");
        btn.disabled = true;
        btn.innerText = "Menyimpan...";
    }
</script>

</body>
</html>
