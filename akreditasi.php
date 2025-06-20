<?php require_once 'theme.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akreditasi Sekolah</title>
    <link rel="stylesheet" href="assets/style/style.css?v=2">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
        }

        .section-akreditasi {
            text-align: center;
            padding: 100px 20px 60px;
        }

        .section-akreditasi h2 {
            font-size: 36px;
            font-weight: bold;
            border-bottom: 4px solid #ff7f3f;
            display: inline-block;
            padding-bottom: 10px;
            margin-bottom: 40px;
        }

        .sertifikat-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sertifikat-box {
            max-width: 800px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.4);
        }

        .sertifikat-box img {
            width: 100%;
            border-radius: 8px;
        }

        .deskripsi-akreditasi {
            max-width: 1600px;
            margin: 60px auto 0;
            padding: 0 20px;
            text-align: justify;
            font-size: 17px;
            line-height: 1.8;
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .section-akreditasi h2 {
                font-size: 28px;
            }

            .deskripsi-akreditasi {
                font-size: 16px;
            }

            .sertifikat-box {
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<section class="section-akreditasi">
    <h2>AKREDITASI SEKOLAH</h2>

    <div class="sertifikat-wrapper">
        <div class="sertifikat-box">
            <img src="image/sertifikat_akreditasi.png" alt="Sertifikat Akreditasi Sekolah">
        </div>
    </div>

    <div class="deskripsi-akreditasi">
        <p>
            SMA Negeri 1 Pomalaa telah terakreditasi oleh Badan Akreditasi Nasional Sekolah/Madrasah (BAN-S/M) sebagai pengakuan resmi atas mutu penyelenggaraan pendidikan yang memenuhi standar nasional pendidikan.
        </p>
        <p>
            Akreditasi ini menjadi bukti komitmen sekolah dalam memberikan layanan pendidikan berkualitas serta menjamin proses belajar mengajar yang efektif, efisien, dan berkelanjutan. Dengan dukungan dari tenaga pendidik yang profesional dan fasilitas yang memadai, SMA Negeri 1 Pomalaa terus berupaya untuk mencetak lulusan yang kompeten dan berkarakter.
        </p>
        <p>
            Prestasi ini diharapkan dapat menjadi motivasi bagi seluruh warga sekolah untuk terus mengembangkan diri dan menjadikan sekolah sebagai pusat unggulan dalam bidang akademik maupun non-akademik.
        </p>
    </div>
</section>

<?php include 'partials/footer.php'; ?>

</body>
</html>
