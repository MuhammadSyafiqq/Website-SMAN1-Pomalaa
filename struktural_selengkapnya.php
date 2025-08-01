<?php
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktural Selengkapnya</title>
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
            padding: 60px 20px;
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
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 10px;
        }

        .struktur-item {
            flex: 1 1 calc(25% - 30px);
            max-width: calc(25% - 30px);
            min-width: 220px;
            background: #fefefe;
            border: 3px solid #003366;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .struktur-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }

        .struktur-item img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #004030;
        }

        .struktur-info h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #003366;
        }

        .struktur-info p {
            margin: 2px 0;
            font-size: 14px;
            color: #003366;
        }

        @media (max-width: 992px) {
            .struktur-item {
                flex: 1 1 calc(33.33% - 30px);
                max-width: calc(33.33% - 30px);
            }
        }

        @media (max-width: 768px) {
            .struktur-item {
                flex: 1 1 calc(50% - 20px);
                max-width: calc(50% - 20px);
            }

            .struktur-info h2 {
                font-size: 17px;
            }

            .struktur-info p {
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .struktur-item {
                flex: 1 1 100%;
                max-width: 100%;
            }

            .struktur-info h2 {
                font-size: 16px;
            }

            .struktur-info p {
                font-size: 12px;
            }

            .struktur-item {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body style="margin-top: 110px; background-color: #f4f4f4;">

<?php include 'partials/navbar.php'; ?>

<section class="struktur-section">
    <h2 class="struktur-title">DATA PEGAWAI LAINNYA</h2>

    <div class="struktur-container">
        <?php
        $query = mysqli_query($connection, "SELECT * FROM struktur WHERE position IS NULL OR TRIM(position) = '' ORDER BY id_struktur ASC");
        while ($data = mysqli_fetch_assoc($query)) {
            $photo = base64_encode($data['photo']);
            $imgSrc = 'data:image/jpeg;base64,' . $photo;
        ?>
        <div class="struktur-item">
            <img src="<?= $imgSrc ?>" alt="Foto <?= htmlspecialchars($data['nama']) ?>">
            <div class="struktur-info">
                <h2><?= strtoupper(htmlspecialchars($data['nama'])) ?></h2>
                <p>NIP: <?= htmlspecialchars($data['nip']) ?></p>
                <p>Status: <?= htmlspecialchars($data['status']) ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

<?php include 'partials/footer.php'; ?>

</body>
</html>
