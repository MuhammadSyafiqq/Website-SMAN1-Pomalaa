<?php
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struktur Pegawai</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
        }

        .struktur-section {
            background: #ffffff;
            padding: 80px 20px;
        }

        .struktur-title {
            text-align: center;
            color: #004030;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 40px;
            position: relative;
        }

        .struktur-title::after {
            content: "";
            width: 80px;
            height: 5px;
            background-color: gold;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .struktur-container {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .struktur-item {
            display: flex;
            flex-direction: row;
            align-items: center;
            background-color: #fff;
            border-bottom: 4px solid #004030;
            padding-bottom: 30px;
            gap: 30px;
            flex-wrap: wrap;
        }

        .struktur-item img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #004030;
            flex-shrink: 0;
        }

        .struktur-info {
            color: #004030;
            flex: 1;
        }

        .struktur-info h3 {
            font-size: 18px;
            margin-bottom: 6px;
        }

        .struktur-info h2 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .struktur-info p {
            margin: 2px 0;
            font-size: 14px;
        }

        .lihat-selengkapnya {
            text-align: center;
            margin: 40px 0 60px;
        }

        .lihat-selengkapnya a {
            display: inline-block;
            padding: 14px 32px;
            background-color: #004030;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: 0.3s ease;
        }

        .lihat-selengkapnya a:hover {
            background-color: #00664e;
        }

        @media (max-width: 768px) {
            .struktur-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding-bottom: 20px;
            }

            .struktur-item img {
                width: 130px;
                height: 130px;
                margin-bottom: 20px;
            }

            .struktur-info h3 {
                font-size: 16px;
            }

            .struktur-info h2 {
                font-size: 20px;
            }

            .struktur-info p {
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .struktur-title {
                font-size: 26px;
            }

            .lihat-selengkapnya a {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<section class="struktur-section">
    <h2 class="struktur-title">STRUKTUR PEGAWAI</h2>

    <div class="struktur-container">
        <?php
        $query = mysqli_query($connection, "SELECT * FROM struktur WHERE position IS NOT NULL AND TRIM(position) != '' ORDER BY id_struktur ASC");
        while ($data = mysqli_fetch_assoc($query)) {
            $photo = base64_encode($data['photo']);
            $imgSrc = 'data:image/jpeg;base64,' . $photo;
        ?>
        <div class="struktur-item">
            <img src="<?= $imgSrc ?>" alt="Foto <?= htmlspecialchars($data['nama']) ?>">
            <div class="struktur-info">
                <h3><?= strtoupper(htmlspecialchars($data['position'])) ?></h3>
                <h2><?= strtoupper(htmlspecialchars($data['nama'])) ?></h2>
                <p>NIP: <?= htmlspecialchars($data['nip']) ?></p>
                <p>Status: <?= htmlspecialchars($data['status']) ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

<div class="lihat-selengkapnya">
    <a href="struktural_selengkapnya.php">Lihat Pegawai Lainnya</a>
</div>

<?php include 'partials/footer.php'; ?>

</body>
</html>
