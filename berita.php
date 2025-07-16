<?php
require_once 'theme.php';

require_once 'config/database.php';

$sql = "SELECT id_berita, title, deskripsi, image, date FROM berita ORDER BY date DESC";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Sekolah</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: white;
            color: #333;
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
            font-size: 38px;
            font-weight: bold;
            color: primary-blue;
            border-bottom: 4px solid #FFD700;
            display: inline-block;
            padding-bottom: 12px;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .news-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 16px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .news-card:hover {
            transform: translateY(-5px);
        }

        .news-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .news-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .news-content h3 {
            font-size: 20px;
            color: #003366;
            margin-bottom: 10px;
            white-space: normal; /* Biarkan teks mengalir */
            word-wrap: break-word;
        }


        .news-content .date {
            font-size: 13px;
            color: #888;
            margin-bottom: 8px;
        }

        .news-content p {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .read-more {
            align-self: flex-start;
            font-size: 14px;
            color:rgb(255, 255, 255);
            text-decoration: none;
            font-weight: 600;
            margin-top: auto;
        }

        .read-more:hover {
            text-decoration: underline;
            color:rgb(255, 255, 255);
        }

        .no-data {
            color: white;
            text-align: center;
            padding: 50px 0;
            font-size: 18px;
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<div class="container">
    <div class="header">
        <h1>Berita Sekolah</h1>
    </div>

    <div class="news-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $isi_pendek = implode(' ', array_slice(explode(' ', strip_tags($row['deskripsi'])), 0, 25)) . '...';
                    $judul_pendek = strip_tags($row['title']);
                    $tanggal = date('d M Y', strtotime($row['date']));
                ?>
                <div class="news-card">
                    <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="Gambar Berita <?= htmlspecialchars($row['title']) ?>">
                    <div class="news-content">
                        <span class="date"><?= $tanggal ?></span>
                        <h3><?= htmlspecialchars($judul_pendek) ?></h3>
                        <p><?= htmlspecialchars($isi_pendek) ?></p>
                        <a href="detail_berita.php?id=<?= $row['id_berita'] ?>" class="read-more">Baca Selengkapnya</a>
                    </div>
                </div>
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
