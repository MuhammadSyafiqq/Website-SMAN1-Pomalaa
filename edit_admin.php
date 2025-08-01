<?php
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
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: hash.php");
    exit();
}

$id = intval($_GET['id']);
$result = $connection->query("SELECT * FROM user WHERE id_user = $id");

if ($result->num_rows !== 1) {
    header("Location: hash.php");
    exit();
}

$admin = $result->fetch_assoc();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $connection->real_escape_string($_POST['nama']);
    $username = $connection->real_escape_string($_POST['username']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = "UPDATE user SET nama='$nama', username='$username', password='$password' WHERE id_user=$id";
    } else {
        $update = "UPDATE user SET nama='$nama', username='$username' WHERE id_user=$id";
    }

    if ($connection->query($update)) {
        $_SESSION['notif'] = ['type' => 'success', 'msg' => 'Admin berhasil diperbarui!'];
        header("Location: hash.php");
        exit();
    } else {
        $message = "Gagal memperbarui admin: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Admin</title>
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f0f0;
            padding: 40px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #004030;
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
            color: #004030;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #004030;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 20px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #006a4e;
        }

        .message {
            text-align: center;
            color: green;
            margin-top: 10px;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #004030;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Admin</h2>
    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nama">Nama Lengkap</label>
        <input type="text" name="nama" id="nama" value="<?= $admin['nama'] ?>" required>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?= $admin['username'] ?>" required>

        <label for="password">Password Baru (biarkan kosong jika tidak diubah)</label>
        <input type="password" name="password" id="password">

        <button type="submit">Simpan Perubahan</button>
    </form>

    <div class="back-link">
        <a href="hash.php">&larr; Kembali ke Daftar Admin</a>
    </div>
</div>

</body>
</html>
