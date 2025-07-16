<?php
require_once '../config/database.php';
session_start();

// Timeout 15 menit
$timeout_duration = 900; 
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';

// Ambil ID
$id = $_GET['id'];
$result = $connection->query("SELECT * FROM feedback WHERE id = $id");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $balasan = $connection->real_escape_string($_POST['balasan']);
    $connection->query("UPDATE feedback SET balasan = '$balasan' WHERE id = $id");
    header("Location: admin_feedback.php?success=reply");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Balas Feedback</title>
    <link rel="stylesheet" href="../assets/style/style.css?v=7">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            padding: 60px 20px;
            color: #000;
        }

        .form-container {
            max-width: 750px;
            margin: auto;
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #003366;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 20px;
            margin-bottom: 8px;
            color: #003366;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            font-size: 16px;
            color: black;
        }

        button {
            margin-top: 25px;
            background-color: #00589D;
            color: white;
            padding: 14px 20px;
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
            display: block;
            margin-top: 25px;
            text-align: center;
            color: #00589D;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .feedback-info {
            margin-top: 15px;
            background: #f1f1f1;
            padding: 16px;
            border-radius: 8px;
        }

        .feedback-info strong {
            color: #003366;
        }

        .feedback-info p {
            margin: 6px 0 0;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Balas Feedback</h2>

    <div class="feedback-info">
        <strong>Nama:</strong> <?= htmlspecialchars($row['nama']) ?><br>
        <strong>Komentar:</strong>
        <p><?= nl2br(htmlspecialchars($row['komentar'])) ?></p>
    </div>

    <form method="post">
        <label for="balasan">Balasan Anda (Kosongkan jika ingin menghapus balasan):</label>
        <textarea name="balasan" id="balasan" rows="5"><?= htmlspecialchars($row['balasan']) ?></textarea>
        <button type="submit">Konfirmasi</button>
    </form>

    <a class="back-link" href="admin_feedback.php">← Kembali ke Daftar Feedback</a>
</div>

</body>
</html>
