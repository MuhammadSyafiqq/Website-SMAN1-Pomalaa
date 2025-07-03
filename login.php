<?php
session_start();

// Lama waktu tidak aktif sebelum logout otomatis (dalam detik)
$timeout_duration = 900; // 15 menit

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login_admin.php?timeout=true"); // ganti dengan nama file login kamu jika perlu
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $connection->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = $connection->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['LAST_ACTIVITY'] = time(); // Set ulang waktu aktivitas terakhir

            header("Location: dashboard_admin.php");
            exit();
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;700&display=swap">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            height: 100vh;
        }
        .left-panel {
            flex: 0.9;
            background: url('assets/image/bg-login.png') no-repeat center center;
            background-size: cover;
        }
        .right-panel {
            flex: 1;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
        }
        .login-box {
            max-width: 400px;
            width: 100%;
            margin: auto;
        }
        h1 {
            font-size: 26px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 30px;
            line-height: 1.3;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #003366;
            font-size: 14px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            border: none;
            border-bottom: 2px solid #003366;
            padding: 10px 5px;
            font-size: 15px;
            margin-bottom: 25px;
            outline: none;
        }
        button {
            background-color: #003366;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover { background-color: #00589D; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #333;
        }
        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 30px;
        }
        .logo img {
            width: 70px;
            margin-bottom: 10px;
        }
        .logo h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #003366;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel {
                flex: 1;
                padding: 30px;
            }
            .login-box { max-width: 100%; }
        }
    </style>
</head>
<body>

    <div class="left-panel"></div>

    <div class="right-panel">
        <div class="login-box">
            <h1>ADMIN DASHBOARD<br>SMA NEGERI 1 POMALAA</h1>

            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['timeout']) && $_GET['timeout'] === "true"): ?>
                <div class="error">Sesi Anda telah berakhir. Silakan login kembali.</div>
            <?php endif; ?>

            <form method="post" action="">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">LOG IN</button>
            </form>

            <div class="logo">
                <img src="assets/image/logo_sekolah.png" alt="Logo Sekolah">
                <h3>SMA NEGERI 1 POMALAA</h3>
            </div>

            <div class="footer">
                SMAN 1 POMALAA, berlokasi di JL. SALAK NO. 2 POMALAA, KUMORO, KEC. POMALAA, KAB. KOLAKA, PROV. SULAWESI TENGGARA,<br>
                merupakan sekolah menengah atas negeri yang memiliki reputasi baik di wilayahnya.<br>
                Dengan luas tanah mencapai 9.621 meter persegi, SMAN 1 POMALAA memiliki fasilitas lengkap untuk menunjang proses belajar mengajar yang berkualitas.
            </div>
        </div>
    </div>

</body>
</html>
