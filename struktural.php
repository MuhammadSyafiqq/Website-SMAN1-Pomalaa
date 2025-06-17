<?php
$host = "localhost";
$user = "root";
$pass = ""; // Kosongkan jika tidak ada password
$db   = "db_sman1pomalaa";

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struktur Pegawai</title>
    <link rel="stylesheet" href="assets/style/style.css?v=2">
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
            .content {
                padding: 20px;
            }
            .content p {
                font-size: 0.95em;
            }
            .berita-info,
            .publisher-bottom {
                text-align: left;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    
<?php include 'partials/navbar.php'; ?>
    
    <section class="struktur-section">
        <h2 class="struktur-title">STRUKTUR PEGAWAI</h2>

        <?php
        $query = mysqli_query($koneksi, "SELECT * FROM struktur");
        while ($data = mysqli_fetch_assoc($query)) {
            $photo = base64_encode($data['photo']);
            $imgSrc = 'data:image/jpeg;base64,' . $photo;
        ?>
        <div class="struktur-item">
            <img src="<?= $imgSrc ?>" alt="Foto <?= $data['nama'] ?>">
            <div class="struktur-info">
                <h3><?= strtoupper($data['position']) ?></h3>
                <h2><?= strtoupper($data['nama']) ?></h2>
                <p>NIP: <?= $data['nip'] ?></p>
                <p>Status: <?= $data['status'] ?></p>
            </div>
        </div>
        <?php } ?>
    </section>

    <?php include 'partials/footer.php'; ?>

</body>
</html>
