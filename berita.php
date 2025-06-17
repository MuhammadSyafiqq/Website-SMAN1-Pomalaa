<?php
require_once 'theme.php';

$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$sql = "SELECT id_berita, title, deskripsi, image, date FROM berita ORDER BY date DESC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Sekolah</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style/style.css?v=3">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px 60px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 36px;
            font-weight: bold;
            border-bottom: 3px solid #FFD700;
            display: inline-block;
            padding-bottom: 10px;
            color: #fff;
        }

        .poster-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }

        .poster-link {
            text-decoration: none;
        }

        .poster {
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            width: 260px;
        }

        .poster:hover {
            transform: scale(1.03);
        }

        .poster img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .poster h3 {
            margin: 15px 20px 5px;
            font-size: 18px;
            color: #003366;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .poster p {
            font-size: 14px;
            color: #333;
            margin: 0 20px 15px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .no-data {
            text-align: center;
            font-size: 18px;
            padding: 50px 0;
        }

        @media (max-width: 1024px) {
            .poster {
                width: 45%;
            }
        }

        @media (max-width: 768px) {
            .poster {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<div class="container">
    <div class="header">
        <h1>Berita Sekolah</h1>
    </div>

    <div class="poster-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $isi_pendek = implode(' ', array_slice(explode(' ', strip_tags($row['deskripsi'])), 0, 10)) . '...';
                    $judul_pendek = implode(' ', array_slice(explode(' ', strip_tags($row['title'])), 0, 6)) . '...';
                ?>
                <a href="detail_berita.php?id=<?= $row['id_berita'] ?>" class="poster-link">
                    <div class="poster">
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar Berita <?= htmlspecialchars($row['title']) ?>">
                        <h3><?= htmlspecialchars($judul_pendek) ?></h3>
                        <p><?= htmlspecialchars($isi_pendek) ?></p>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-data">Tidak ada berita ditemukan.</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

</body>
</html>

<?php $connection->close(); ?>
