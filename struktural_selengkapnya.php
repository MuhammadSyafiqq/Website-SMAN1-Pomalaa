<?php
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struktural Selengkapnya</title>
    <link rel="stylesheet" href="assets/style/style.css?v=2">
    <style>
        .struktur-section {
            background: #ffffff;
            padding: 50px 20px;
        }

        .struktur-title {
            text-align: center;
            color: #003366;
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
        }

        .struktur-item {
            flex: 1 1 calc(25% - 30px); /* 4 per baris dengan gap */
            max-width: calc(25% - 30px);
            min-width: 220px;
            background: #f9f9f9;
            border: 3px solid #003366;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .struktur-item:hover {
            transform: translateY(-5px);
        }

        .struktur-item img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #00589D;
        }

        .struktur-info h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
            color: #003366;
        }

        .struktur-info p {
            margin: 2px 0;
            font-size: 13px;
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
                flex: 1 1 calc(50% - 30px);
                max-width: calc(50% - 30px);
            }
        }

        @media (max-width: 480px) {
            .struktur-item {
                flex: 1 1 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

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
