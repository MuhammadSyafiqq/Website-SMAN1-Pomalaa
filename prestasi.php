<?php
require_once 'theme.php';

$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$sql = "SELECT id_prestasi, title, description, image FROM prestasi";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Prestasi Siswa</title>
    <link rel="stylesheet" href="assets/style/style.css?v=3">
    <style>

        @media (max-width: 992px) {
            .poster-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .poster-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .poster-grid {
                grid-template-columns: 1fr;
            }
        }

        
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 100px 20px 60px;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 36px;
            font-weight: bold;
            border-bottom: 3px solid #ff7f3f;
            display: inline-block;
            padding-bottom: 10px;
            color: #fff;
        }

        .poster-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Maksimal 4 kolom */
            gap: 30px;
            justify-content: center;
        }


        .poster-link {
            text-decoration: none;
        }

        .poster {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            max-width: 260px;
            margin: 0 auto;
        }

        .poster:hover {
            transform: scale(1.05); /* efek zoom */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5); /* efek bayangan lebih besar saat hover */
        }



        .poster-content {
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .poster h3 {
            margin: 0 0 10px;
            font-size: 20px;
            color: #003366;
        }

        .poster p {
            font-size: 14px;
            color: #333;
            margin-top: auto; /* menjaga p selalu di bawah */
        }


        .no-data {
            text-align: center;
            font-size: 18px;
            padding: 50px 0;
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<div class="container">
    <div class="header">
        <h1>Prestasi Sekolah</h1>
    </div>

    <div class="poster-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $deskripsi_pendek = implode(' ', array_slice(explode(' ', strip_tags($row['description'])), 0, 10)) . '...';
                    $judul_pendek = implode(' ', array_slice(explode(' ', strip_tags($row['title'])), 0, 6)) . '...';
                ?>
                <a href="detail_prestasi.php?id=<?= $row['id_prestasi'] ?>" class="poster-link">
                    <div class="poster">
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                        <div class="poster-content">
                            <h3><?= htmlspecialchars($judul_pendek) ?></h3>
                            <p><?= htmlspecialchars($deskripsi_pendek) ?></p>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-data">Tidak ada data prestasi ditemukan.</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>


</body>
</html>

<?php $connection->close(); ?>
