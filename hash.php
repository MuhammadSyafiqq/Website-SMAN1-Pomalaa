<?php
session_start();

// Waktu timeout (15 menit = 900 detik)
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

$message = '';

// Proses tambah admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    $nama = $connection->real_escape_string($_POST['nama']);
    $username = $connection->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin';

    $check = $connection->query("SELECT * FROM user WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $message = "Username sudah digunakan.";
    } else {
        $sql = "INSERT INTO user (nama, username, password, role, date)
                VALUES ('$nama', '$username', '$password', '$role', CURRENT_TIMESTAMP)";
        if ($connection->query($sql) === TRUE) {
            $message = "Admin berhasil didaftarkan!";
        } else {
            $message = "Terjadi kesalahan: " . $connection->error;
        }
    }
}

// Proses hapus admin
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $connection->query("DELETE FROM user WHERE id_user = $id");
    header("Location: manajemen_admin.php");
    exit();
}

// Ambil semua admin
$admins = $connection->query("SELECT * FROM user WHERE role = 'admin'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f0f0;
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #003366;
        }
        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
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
            background: #003366;
            color: white;
            border: none;
            padding: 10px;
            margin-top: 20px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #00589D;
        }
        .message {
            margin-top: 15px;
            text-align: center;
            color: green;
        }
        .error { color: red; }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        .actions a {
            margin-right: 10px;
            color: #00589D;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Manajemen Admin</h2>

    <?php if ($message): ?>
        <p class="message <?= strpos($message, 'berhasil') ? '' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="tambah">
        <label for="nama">Nama Lengkap</label>
        <input type="text" name="nama" id="nama" required>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Daftarkan Admin</button>
    </form>
    
    <div class="back-link">
        <a href="dashboard_admin.php">&larr; Kembali ke Dashboard Admin</a>
    </div>

    <h3 style="margin-top:40px;">Daftar Admin</h3>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Tanggal Dibuat / Diubah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($admin = $admins->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($admin['nama']) ?></td>
                <td><?= htmlspecialchars($admin['username']) ?></td>
                <td><?= htmlspecialchars($admin['date']) ?></td>
                <td class="actions">
                    <a href="edit_admin.php?id=<?= $admin['id_user'] ?>">Ubah</a>
                    <a href="?hapus=<?= $admin['id_user'] ?>" onclick="return confirm('Yakin ingin menghapus admin ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
