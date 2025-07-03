<?php
require_once('../koneksi.php');
session_start();
// Waktu timeout (dalam detik) — misal 15 menit = 900 detik
$timeout_duration = 900; 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();     // hapus semua session
    session_destroy();   // hancurkan session
    header("Location: login.php?timeout=true"); // redirect ke login (ganti dengan nama file login jika perlu)
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // perbarui waktu aktivitas terakhir

// Cek jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
require_once '../theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

$id = $_GET['id'];
$result = $connection->query("SELECT * FROM feedback WHERE id = $id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $balasan = $connection->real_escape_string($_POST['balasan']);
    $connection->query("UPDATE feedback SET balasan = '$balasan' WHERE id = $id");
    header("Location: admin_feedback.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Balas Feedback</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=6">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            padding: 60px 20px;
            color: white;
        }
        .form-container {
            max-width: 700px;
            margin: auto;
            background: #003366;
            color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        label {
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: vertical;
        }
        button {
            margin-top: 20px;
            background-color: #00589D;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #003f70;
        }
        .back-link {
            margin-top: 25px;
            display: block;
            text-align: center;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Balas Feedback</h2>
    <p><strong>Nama:</strong> <?= htmlspecialchars($row['nama']) ?></p>
    <p><strong>Komentar:</strong><br><?= nl2br(htmlspecialchars($row['komentar'])) ?></p>

    <form method="post">
        <label for="balasan">Balasan Anda:</label>
        <textarea name="balasan" id="balasan" rows="5" required><?= htmlspecialchars($row['balasan']) ?></textarea>
        <button type="submit">Kirim Balasan</button>
    </form>

    <a class="back-link" href="admin_feedback.php">← Kembali ke Feedback</a>
</div>

</body>
</html>
