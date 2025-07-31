<?php require_once 'theme.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Visi & Misi</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">

    <style>
        body {
            margin: 0;
            margin-top: 70px;
            font-family: 'Segoe UI', sans-serif;
            background: white;
            color: white;
        }

        .section-visimisi {
            padding: 60px 20px;
        }

        .section-visimisi .container {
            max-width: 900px;
            margin: auto;
        }

        .section-visimisi h2 {
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 40px;
            color: black;
            border-bottom: 3px solid #FFD700;
            display: inline-block;
            padding-bottom: 10px;
        }

        .visi, .misi {
            margin-top: 30px;
            text-align: justify;
        }

        .visi h3, .misi h3 {
            font-size: 26px;
            margin-bottom: 15px;
            color: black;
        }

        .visi p {
            font-size: 18px;
            line-height: 1.6;
            color: black;
        }

        .misi ol {
            font-size: 18px;
            line-height: 1.8;
            padding-left: 20px;
            color: #004030;
        }

        .misi li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<section class="section-visimisi">
    <div class="container">
        <h2>VISI & MISI</h2>

        <div class="visi">
            <h3>Visi</h3>
            <p>Melalui Keseimbangan Imtaq dan Iptek Untuk Mencapai Keunggulan Kompetitif</p>
        </div>

        <div class="misi">
            <h3>Misi</h3>
            <ol>
                <li>Meningkatkan penghayatan dan pengamalan nilai-nilai keagamaan yang menjadi kearifan bertindak</li>
                <li>Menerapkan budaya disiplin dan demokrasi dalam segala aktivitas sekolah</li>
                <li>Menciptakan suasana pembelajaran yang kondusif dan kompetitif untuk memacu keterampilan warga sekolah</li>
                <li>Mengoptimalkan potensi sekolah untuk mengembangkan bakat, kemampuan dan keterampilan warga sekolah</li>
                <li>Meningkatkan kemampuan belajar secara maksimal untuk mencapai prestasi sesuai perkembangan teknologi</li>
            </ol>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>

</body>
</html>
