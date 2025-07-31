<?php
session_start();
require_once 'config/database.php';

// Google reCAPTCHA secret key
$recaptcha_secret = '6LdMcZMrAAAAADBrOt1Wi4gbaUMpAixSFpwPgu3P';

// Session timeout
$timeout_duration = 900;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login_admin.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    header("Location: dashboard_admin.php");
    exit();
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}
$ip_address = getUserIP();

$limit_attempts = 5;
$lock_duration = 5;
$error = '';

$check = $connection->prepare("SELECT COUNT(*) AS total FROM login_logs WHERE ip_address = ? AND attempt_time > (NOW() - INTERVAL ? MINUTE)");
$check->bind_param("si", $ip_address, $lock_duration);
$check->execute();
$failed = $check->get_result()->fetch_assoc();

if ($failed['total'] >= $limit_attempts) {
    $error = "Terlalu banyak gagal login. Coba lagi dalam $lock_duration menit.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $failed['total'] < $limit_attempts) {
    $captcha_response = $_POST['g-recaptcha-response'] ?? '';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$captcha_response}&remoteip={$ip_address}");
    $captcha_success = json_decode($verify)->success;

    if (!$captcha_success) {
        $error = "Verifikasi captcha gagal.";
        $stmt = $connection->prepare("INSERT INTO login_logs (ip_address, attempt_time) VALUES (?, NOW())");
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
    } else {
        $username = $connection->real_escape_string($_POST['username']);
        $password = $_POST['password'];

        $query = $connection->query("SELECT * FROM user WHERE username = '$username'");
        if ($query->num_rows === 1) {
            $user = $query->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['LAST_ACTIVITY'] = time();
                header("Location: dashboard_admin.php");
                exit();
            } else {
                $error = "Password salah.";
                $stmt = $connection->prepare("INSERT INTO login_logs (ip_address, attempt_time) VALUES (?, NOW())");
                $stmt->bind_param("s", $ip_address);
                $stmt->execute();
            }
        } else {
            $error = "Username tidak ditemukan.";
            $stmt = $connection->prepare("INSERT INTO login_logs (ip_address, attempt_time) VALUES (?, NOW())");
            $stmt->bind_param("s", $ip_address);
            $stmt->execute();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SMAN 1 Pomalaa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;700&display=swap">
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: row;
            height: 100vh;
        }
        
        .left-panel {
            flex: 1;
            background: url('assets/image/Login.png') no-repeat center center;
            background-size: cover;
        }
        
        .right-panel {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        
        .login-box {
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            font-size: 26px;
            font-weight: bold;
            color: #004030; /* ganti dari #003366 */
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            color: #004030; /* ganti dari #003366 */
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: none;
            border-bottom: 2px solid #004030; /* ganti dari #003366 */
            margin-bottom: 20px;
        }
        
        button {
            background-color: #004030; /* ganti dari #003366 */
            color: #fff;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }
        
        button:hover {
            background-color: #006a4e; /* ganti dari #00589D, versi lebih terang dari #004030 */
        }
        
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 30px 0;
        }
        
        .logo img {
            width: 70px;
            margin-bottom: 10px;
        }
        
        .logo h3 {
            color: #004030; /* ganti dari #003366 */
            margin: 0;
            font-size: 16px;
        }
        
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 30px;
            color: #555;
        }
        
        @media screen and (max-width: 768px) {
            body {
                flex-direction: column;
            }
        
            .left-panel {
                display: none;
            }
        
            .right-panel {
                padding: 30px;
            }
        }

    </style>
</head>
<body>

    <div class="left-panel"></div>

    <div class="right-panel">
        <div class="login-box">
            <h1>SMA NEGERI 1 POMALAA</h1>

            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['timeout'])): ?>
                <div class="error">Sesi Anda telah berakhir. Silakan login kembali.</div>
            <?php endif; ?>

            <form method="post" action="">
                <label for="username">Username</label>
                <input type="text" name="username" required>

                <label for="password">Password</label>
                <input type="password" name="password" required>

                <div class="g-recaptcha" data-sitekey="6LdMcZMrAAAAABiGUg41ZLqrMz-EjHs7kB8QqHXA"></div>

                <br>
                <button type="submit" <?= ($failed['total'] >= $limit_attempts) ? 'disabled' : '' ?>>LOG IN</button>
            </form>

            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" style="text-decoration: none; color: #003366;">← Kembali ke Dashboard</a>
            </div>

            <a href="index.php" class="logo">
                <img src="assets/image/logo_sekolah.png" alt="Logo Sekolah">
                <h3>SMA NEGERI 1 POMALAA</h3>
            </a>

            <div class="footer">
                SMAN 1 POMALAA, JL. SALAK NO. 2 POMALAA, KEC. POMALAA, KAB. KOLAKA, SULTRA<br>
                Sekolah menengah atas negeri dengan reputasi baik dan fasilitas lengkap.
            </div>
        </div>
    </div>

</body>
</html>
