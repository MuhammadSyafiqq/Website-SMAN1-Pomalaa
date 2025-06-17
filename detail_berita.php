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

// Ambil data berita dan user-nya
$sql = "SELECT b.title, b.deskripsi, b.image, b.date, u.username AS publisher
        FROM berita b
        JOIN user u ON b.id_user = u.id_user
        WHERE b.id_berita = $id";

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
    <title>Detail Berita - <?= htmlspecialchars($data['title']) ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #111;
        }

        .header {
            background: linear-gradient(to right, #003366, #00589D);
            padding: 50px 20px 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 3em;
            margin: 0;
            letter-spacing: 2px;
            position: relative;
            display: inline-block;
        }

        .header h1::after {
            content: '';
            display: block;
            width: 100px;
            height: 5px;
            background: #FFD700;
            margin: 10px auto 0;
        }

        .image-wrapper {
            position: relative;
        }

        .main-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            display: block;
        }

        .content {
            padding: 10px 60px 80px;
            text-align: center;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
        }

        .content h2 {
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 1em;
            max-width: 1600px;
            margin: 0 auto;
            line-height: 1.8em;
            text-align: justify;
        }

        .berita-info {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #ddd;
            font-size: 0.95em;
            text-align: left;
            max-width: 1600px;
            margin-left: auto;
            margin-right: auto;
        }

        .publisher-bottom {
            color: #ccc;
            font-size: 0.9em;
            font-style: italic;
            text-align: left;
            max-width: 1600px;
            margin: 0 auto;
            margin-top: 60px;
            margin-bottom: -60px;
        }


        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            .content p {
                font-size: 0.95em;
            }
            .footer {
                padding: 20px;
            }
            .berita-info,
            .publisher-bottom {
                text-align: left;
                max-width: 100%;
            }
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style/style.css?v=2">
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<div class="image-wrapper">
    <img src="data:image/jpeg;base64,<?= base64_encode($data['image']) ?>" alt="Berita" class="main-image">
</div>

<div class="content">
    <h2><?= htmlspecialchars($data['title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>

    <div class="berita-info">
        <span><strong>Tanggal:</strong> <?= htmlspecialchars($data['date']) ?></span>
    </div>

    <div class="publisher-bottom">
        Publisher: <?= htmlspecialchars($data['publisher']) ?>
    </div>
</div>

 <?php include 'partials/footer.php'; ?>

</body>
</html>

<?php $connection->close(); ?>
