<?php
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struktur Pegawai</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">

    <style>
        .struktur-section {
            background: #ffffff;
            padding: 50px 0;
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

        .struktur-item {
            display: flex;
            align-items: center;
            margin: 40px auto;
            max-width: 900px;
            border-bottom: 4px solid #003366;
            padding-bottom: 30px;
        }

        .struktur-item img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 40px;
            border: 4px solid #00589D;
        }

        .struktur-info {
            color: #003366;
        }

        .struktur-info h3 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .struktur-info h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .struktur-info p {
            margin: 2px 0;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .struktur-item {
                flex-direction: column;
                text-align: center;
            }

            .struktur-item img {
                margin-right: 0;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<section class="struktur-section">
    <h2 class="struktur-title">STRUKTUR PEGAWAI</h2>

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
</section>

<div style="text-align: center; margin: 40px 0 60px;">
    <a href="struktural_selengkapnya.php" style="
        display: inline-block;
        padding: 14px 32px;
        background-color: #003366;
        color: white;
        text-decoration: none;
        border-radius: 30px;
        font-size: 16px;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: background-color 0.3s ease;
    " onmouseover="this.style.backgroundColor='#002244';" onmouseout="this.style.backgroundColor='#003366';">
        Lihat Pegawai Lainnya
    </a>
</div>


<?php include 'partials/footer.php'; ?>

</body>
</html>
