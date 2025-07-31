<?php
require_once 'theme.php';
require_once 'config/database.php';

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT judul, deskripsi, gambar, urutan FROM slider WHERE id = $id";
$result = $connection->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Informasi tidak ditemukan.");
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Informasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">   
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">
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
        <img src="data:image/jpeg;base64,<?= base64_encode($data['gambar']) ?>" alt="Gambar Informasi">
        <h2><?= htmlspecialchars($data['judul']) ?></h2>
        <p><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

</body>
</html>

<?php $connection->close(); ?>
