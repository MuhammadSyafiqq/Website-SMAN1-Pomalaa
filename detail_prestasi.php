<?php
require_once 'theme.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_sman1pomalaa";

$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT title, description, image FROM prestasi WHERE id_prestasi = $id";
$result = $connection->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Prestasi tidak ditemukan.");
}

$data = $result->fetch_assoc();
?>

<link rel="stylesheet" href="assets/style/style.css?v=2">

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Prestasi</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom,#003366, #00589D);
            color: white;
        }

        .container {
            padding: 100px 60px 40px;
        }

        .detail-box {
            background: white;
            border-radius: 10px;
            padding: 30px;
            color: #000;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        .detail-box img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .detail-box h2 {
            margin-top: 0;
            font-size: 2em;
        }

        .back-link {
            display: block;
            margin: 30px auto;
            text-align: center;
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>
<?php include 'partials/navbar.php'; ?>

<div class="container">
    <div class="detail-box">
        <img src="data:image/jpeg;base64,<?= base64_encode($data['image']) ?>" alt="Gambar Prestasi">
        <h2><?= htmlspecialchars($data['title']) ?></h2>
        <p><?= nl2br(htmlspecialchars($data['description'])) ?></p>
    </div>

    <a href="prestasi.php" class="back-link">← Kembali ke daftar prestasi</a>
</div>

<?php include 'partials/footer.php'; ?>

</body>
</html>

<?php $connection->close(); ?>
