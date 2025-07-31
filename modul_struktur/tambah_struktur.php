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

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $position = $_POST['position'];
    $status = $_POST['status'];

    if (
        empty($nama) || empty($nip) || empty($position) || empty($status) ||
        $_FILES['photo']['error'] !== UPLOAD_ERR_OK
    ) {
        $error = "Semua field wajib diisi, termasuk NIP dan foto.";
    } elseif (!ctype_digit($nip)) {
        $error = "NIP hanya boleh diisi dengan angka.";
    } elseif (strlen($nip) > 50) {
        $error = "NIP maksimal 50 digit.";
    } else {
        $check = $connection->prepare("SELECT COUNT(*) FROM struktur WHERE nip = ?");
        $check->bind_param("s", $nip);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            $error = "NIP sudah digunakan oleh pegawai lain.";
        } else {
            $photo = file_get_contents($_FILES['photo']['tmp_name']);

            $stmt = $connection->prepare("INSERT INTO struktur (nama, nip, position, status, photo) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                $error = "Prepare gagal: " . $connection->error;
            } else {
                $stmt->bind_param("sssss", $nama, $nip, $position, $status, $photo);
                $stmt->send_long_data(4, $photo);

                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: admin_struktur.php?success=add");
                    exit();
                } else {
                    $error = "Gagal menambahkan data: " . $stmt->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Struktur</title>
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
            color: #004030; /* Ganti dari biru */
        }
    
        label {
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
            color: #004030; /* Ganti dari biru */
        }
    
        input[type="text"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #004030; /* Ganti dari biru */
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
            border: 1px dashed #004030; /* Ganti dari biru */
        }
    
        button {
            background-color: #004030; /* Ganti dari biru */
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
            background-color: #003420; /* Versi lebih gelap dari #004030 */
        }
    
        button:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }
    
        .back-link {
            display: block;
            margin-top: 30px;
            text-align: center;
            color: #004030; /* Ganti dari biru */
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
    <h2>Tambah Struktur</h2>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="disableSubmitButton()">
        <label for="nama">Nama</label>
        <input type="text" name="nama" required>

        <label for="nip">NIP</label>
        <input type="text" name="nip" required>

        <label for="position">Jabatan/Posisi</label>
<select name="position" required>
    <option value="">-- Pilih Jabatan --</option>
    <option value="Kepala Sekolah">Kepala Sekolah</option>
    <option value="Wakasek Kesiswaan">Wakasek Kesiswaan</option>
    <option value="Wakasek Prasarana">Wakasek Sarana & Prasarana</option>
    <option value="Wakasek Prasarana">Wakasek Kurikulum</option>
    <option value="Wakasek Prasarana">Wakasek Humas</option>
    <option value=" ">None</option>
</select>

<label for="status">Status</label>
<input type="text" name="status" placeholder="Contoh: Guru Matematika" required>


        <label for="photo">Foto</label>
        <input type="file" name="photo" accept="image/*" required>

        <button type="submit" id="submitBtn">Simpan</button>
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
