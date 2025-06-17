<?php
require_once 'theme.php';

$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM ekstrakurikuler WHERE id_ekskul = $id";
$result = $connection->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Data ekstrakurikuler tidak ditemukan.");
}

$data = $result->fetch_assoc();
$image = !empty($data['image']) ? 'data:image/jpeg;base64,' . base64_encode($data['image']) : null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Ekstrakurikuler - <?= htmlspecialchars($data['name']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style/style.css?v=2">
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

        .back-button {
            margin-top: 40px;
            display: inline-block;
            text-decoration: none;
            color: #FFD700;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }

            .content p {
                font-size: 0.95em;
            }
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<div class="header">
    <h1>Ekstrakurikuler</h1>
</div>

<div class="image-wrapper">
    <?php if ($image): ?>
        <img src="<?= $image ?>" alt="Ekstrakurikuler" class="main-image">
    <?php endif; ?>
</div>

<div class="content">
    <h2><?= htmlspecialchars($data['name']) ?></h2>
    <p><?= nl2br(htmlspecialchars($data['description'])) ?></p>
    <a href="javascript:history.back()" class="back-button">&larr; Kembali</a>
</div>

 <?php include 'partials/footer.php'; ?>

</body>
</html>

<?php $connection->close(); ?>
