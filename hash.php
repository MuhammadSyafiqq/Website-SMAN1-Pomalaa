<?php
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $connection->real_escape_string($_POST['nama']);
    $username = $connection->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'admin'; // default role

    // Cek apakah username sudah digunakan
    $check = $connection->query("SELECT * FROM user WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $message = "Username sudah digunakan.";
    } else {
        $sql = "INSERT INTO user (nama, username, password, role)
                VALUES ('$nama', '$username', '$password', '$role')";
        if ($connection->query($sql) === TRUE) {
            $message = "Admin berhasil didaftarkan!";
        } else {
            $message = "Terjadi kesalahan: " . $connection->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
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

        .error {
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Register Admin Baru</h2>
    <?php if ($message): ?>
        <p class="message <?= strpos($message, 'berhasil') ? '' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <label for="nama">Nama Lengkap</label>
        <input type="text" name="nama" id="nama" required>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Daftarkan Admin</button>
    </form>
</div>

</body>
</html>
