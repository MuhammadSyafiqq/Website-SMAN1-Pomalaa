<?php require_once 'theme.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Sekolah</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">

    <style>
    *{
        margin : 0;
        padding :0;
    }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #ffffff;
            color: #003366;
        }

        .section-tentang {
            padding: 80px 20px;
            background: linear-gradient(to bottom, #003366, #00589D);
            color: white;
            text-align: center;
        }

        .section-tentang h2 {
            font-size: 36px;
            border-bottom: 4px solid #ff7f3f;
            display: inline-block;
            padding-bottom: 10px;
        }

        .section-tentang p {
            max-width: 1100px;
            margin: 30px auto 0;
            font-size: 18px;
            line-height: 1.7;
            text-align: justify;
        }

        .section-sambutan {
    background: #fff;
    padding: 60px 20px;
    color: #003366;
}

.sambutan-container {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    max-width: 1100px;
    margin: auto;
    gap: 30px;
    align-items: flex-start;
}

.sambutan-img {
    flex: 0 0 auto;
    width: 220px;
}

.sambutan-img img {
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.sambutan-text {
    flex: 1;
    min-width: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.sambutan-text h3 {
    font-size: 28px;
    margin: 0 0 15px 0;
    color: #003366;
}

.sambutan-text p {
    font-size: 18px;
    line-height: 1.7;
    text-align: justify;
}

.sambutan-penutup {
    max-width: 1150px;
    margin: 30px auto 0 auto;
    color: #003366;
    font-size: 18px;
    line-height: 1.7;
    text-align: justify;
    padding: 0 20px;
}


@media (max-width: 768px) {
    .sambutan-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .sambutan-text {
        align-items: center;
    }

    .sambutan-text p {
        text-align: justify;
    }

    .sambutan-img {
        width: 180px;
    }
}

    </style>
</head>
<body>

<?php include 'partials/navbar.php'; ?>

<!-- Bagian Tentang Sekolah -->
<section class="section-tentang">
    <h2>TENTANG SEKOLAH</h2>
    <p>
        SMA Negeri 1 Pomalaa berlokasi di Jl. Salak No. 2, Pomalaa, Kolaka, Sulawesi Tenggara.
        Sekolah ini merupakan salah satu institusi pendidikan menengah yang memiliki reputasi baik
        di wilayahnya. Dengan tekad menjadi sekolah yang unggul dalam prestasi serta menjunjung tinggi
        nilai-nilai karakter, SMAN 1 Pomalaa terus berbenah dalam pengembangan fasilitas, kurikulum,
        dan tenaga pendidik yang profesional untuk menciptakan generasi penerus bangsa yang berkualitas.
    </p>
</section>

<!-- Bagian Sambutan -->
<section class="section-sambutan">
    <div class="sambutan-container">
        <div class="sambutan-img">
            <img src="image/kepala_sekolah.png" alt="Kepala Sekolah">
        </div>
        <div class="sambutan-text">
            <h3>SAMBUTAN</h3>
            <p>Assalamu’alaikum warahmatullahi wabarakatuh.</p>
            <p>
                Puji syukur ke hadirat Allah SWT atas rahmat dan karunia-Nya, sehingga website SMA Negeri 1 Pomalaa ini dapat dikembangkan sebagai sarana informasi dan komunikasi sekolah dengan masyarakat. Kami berharap media ini dapat memberikan gambaran tentang profil sekolah, kegiatan belajar-mengajar, prestasi siswa, serta berbagai program yang telah dan akan kami laksanakan.
            </p>
        </div>
    </div>

    <div class="sambutan-penutup">
        <p>
            Semoga dengan adanya website ini, terjalin hubungan yang baik antara sekolah dengan peserta didik, orang tua, alumni, serta masyarakat luas. Terima kasih atas dukungan semua pihak demi kemajuan pendidikan di SMA Negeri 1 Pomalaa.
        </p>
        <p>Wassalamu’alaikum warahmatullahi wabarakatuh.</p>
    </div>
</section>


<?php include 'partials/footer.php'; ?>

</body>
</html>
