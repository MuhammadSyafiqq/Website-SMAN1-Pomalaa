<?php
require_once 'theme.php';
require_once 'config/database.php';

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT title, deskripsi, image, date FROM berita WHERE id_berita = $id";
$result = $connection->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Berita tidak ditemukan.");
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Berita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/style.css?v=4">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: white;
            color: white;
        }

        .container {
            padding: 100px 20px 40px;
        }

        .detail-box {
            background: white;
            border-radius: 10px;
            padding: 30px;
            color: #000;
            max-width: 1000px;
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
            color: #003366;
        }

        .meta-info {
            font-size: 14px;
            margin-bottom: 20px;
            color: #555;
        }

        .meta-info strong {
            display: inline-block;
            width: 90px;
        }

        .detail-box p {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }

        .back-link {
            display: block;
            margin: 30px auto;
            text-align: center;
            color: #003366;
            text-decoration: underline;
        }

    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<div class="container">
    <div class="detail-box">
        <img src="data:image/jpeg;base64,<?= base64_encode($data['image']) ?>" alt="Gambar Berita">
        <h2><?= htmlspecialchars($data['title']) ?></h2>

        <div class="meta-info">
            <div><strong>Tanggal:</strong> <?= date('d M Y', strtotime($data['date'])) ?></div>
        </div>

        <p><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

</body>
</html>

<?php $connection->close(); ?>
