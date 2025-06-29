<?php
require_once 'theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

$id = $_GET['id'];
$query = $connection->prepare("SELECT * FROM struktur WHERE id_struktur = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $position = $_POST['position'];
    $status = $_POST['status'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = file_get_contents($_FILES['photo']['tmp_name']);
        $stmt = $connection->prepare("UPDATE struktur SET nama=?, nip=?, position=?, status=?, photo=? WHERE id_struktur=?");
        $stmt->bind_param("sssssi", $nama, $nip, $position, $status, $photo, $id);
        $stmt->send_long_data(4, $photo);
    } else {
        $stmt = $connection->prepare("UPDATE struktur SET nama=?, nip=?, position=?, status=? WHERE id_struktur=?");
        $stmt->bind_param("ssssi", $nama, $nip, $position, $status, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: admin_struktur.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Struktur</title>
    <link rel="stylesheet" href="assets/style/style.css?v=9">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
            padding: 40px;
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
            margin-bottom: 25px;
            color:rgb(255, 255, 255);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #00589D;
            color: white;
        }

        select option {
            background-color: #00589D;
            color: white;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #00589D;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #003f70;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #888;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .back-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Struktur</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="nama">Nama:</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

        <label for="nip">NIP:</label>
        <input type="text" name="nip" value="<?= htmlspecialchars($data['nip']) ?>">

        <label for="position">Jabatan/Posisi:</label>
        <input type="text" name="position" value="<?= htmlspecialchars($data['position']) ?>" required>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="Guru" <?= $data['status'] == 'Guru' ? 'selected' : '' ?>>Guru</option>
            <option value="Staf" <?= $data['status'] == 'Staf' ? 'selected' : '' ?>>Staf</option>
            <option value="" <?= $data['status'] == '' ? 'selected' : '' ?>>Lainnya</option>
        </select>

        <label for="photo">Foto (biarkan kosong jika tidak diubah):</label>
        <input type="file" name="photo" accept="image/*">

        <button type="submit">Simpan Perubahan</button>
    </form>

    <a href="admin_struktur.php" class="back-btn">← Kembali ke Daftar Struktur</a>
</div>

</body>
</html>
