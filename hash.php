<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Makassar');

session_start();

// Timeout
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
require_once 'hash.php'; // fungsi tulis_log

// Tambah admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'tambah') {
    $nama = $connection->real_escape_string($_POST['nama']);
    $username = $connection->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin';
    $created_at = date('Y-m-d H:i:s');

    $check = $connection->query("SELECT * FROM user WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $_SESSION['notif'] = ['type' => 'error', 'msg' => "Username sudah digunakan."];
    } else {
        $sql = "INSERT INTO user (nama, username, password, role, date)
                VALUES ('$nama', '$username', '$password', '$role', '$created_at')";
        if ($connection->query($sql) === TRUE) {
            $_SESSION['notif'] = ['type' => 'success', 'msg' => "Admin berhasil didaftarkan!"];
            try {
                tulis_log("Menambahkan admin: $nama ($username)", $_SESSION['username']);
            } catch (Throwable $e) {}
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'msg' => "Terjadi kesalahan: " . $connection->error];
        }
    }
    header("Location: hash.php");
    exit();
}

// Hapus admin
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $result = $connection->query("SELECT * FROM user WHERE id_user = $id AND role = 'admin'");
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if ($connection->query("DELETE FROM user WHERE id_user = $id") === TRUE) {
            try {
                tulis_log("Menghapus admin: {$admin['nama']} ({$admin['username']})", $_SESSION['username']);
            } catch (Throwable $e) {}
            $_SESSION['notif'] = ['type' => 'success', 'msg' => "Admin berhasil dihapus!"];
        } else {
            $_SESSION['notif'] = ['type' => 'error', 'msg' => "Gagal menghapus admin dari database."];
        }
    } else {
        $_SESSION['notif'] = ['type' => 'error', 'msg' => "Admin tidak ditemukan."];
    }
    header("Location: hash.php");
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/style/style.css?v=<?= time(); ?>">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f0f0;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 110px auto 40px;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        h2, h3 {
            text-align: center;
            color: #004030;
        }
        label {
            font-weight: 600;
            display: block;
            margin-top: 10px;
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
            background: #003220;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            text-decoration: none;
            color: #004030;
        }

        .notif {
            padding: 12px;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 600px;
            text-align: center;
            font-weight: 500;
        }
        .notif.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .notif.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-table th, .admin-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            white-space: nowrap;
        }
        .actions a {
            margin-right: 10px;
            color: #004030;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .admin-table thead {
                display: none;
            }
            .admin-table tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 6px;
                padding: 10px;
                background: #fefefe;
            }
            .admin-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 10px;
                border: none;
                border-bottom: 1px solid #eee;
                font-size: 14px;
            }
            .admin-table td::before {
                content: attr(data-label);
                font-weight: bold;
                flex-shrink: 0;
                color: #004030;
            }
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<?php if (isset($_SESSION['notif'])): ?>
    <div class="notif <?= $_SESSION['notif']['type'] ?>">
        <?= $_SESSION['notif']['msg'] ?>
    </div>
    <?php unset($_SESSION['notif']); ?>
<?php endif; ?>

<div class="container">
    <h2>Manajemen Admin</h2>

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

    <h3>Daftar Admin</h3>
    <div class="table-responsive">
        <table class="admin-table">
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
                        <td data-label="Nama"><?= htmlspecialchars($admin['nama']) ?></td>
                        <td data-label="Username"><?= htmlspecialchars($admin['username']) ?></td>
                        <td data-label="Tanggal Dibuat / Diubah"><?= htmlspecialchars($admin['date']) ?> WITA</td>
                        <td data-label="Aksi" class="actions">
                            <a href="edit_admin.php?id=<?= $admin['id_user'] ?>">Ubah</a>
                            <a href="?hapus=<?= $admin['id_user'] ?>" onclick="return confirm('Yakin ingin menghapus admin ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
