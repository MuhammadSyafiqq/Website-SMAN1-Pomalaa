<?php require_once 'theme.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Sekolah</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">

    <style>
    *{
        margin : 0;
        padding :0;
    }
        body {
            margin: 0;
            margin-top: 30px;
            font-family: 'Segoe UI', sans-serif;
            background: white;
            color: black;
        }

        .section-tentang {
            padding: 80px 20px;
            background: white);
            color: white;
            text-align: center;
        }

        .section-tentang h2 {
            font-size: 36px;
            border-bottom: 4px solid #ff7f3f;
            display: inline-block;
            color: black;
            padding-bottom: 10px;
        }

        .section-tentang p {
            max-width: 1100px;
            margin: 30px auto 0;
            font-size: 18px;
            color: black;
            line-height: 1.7;
            text-align: justify;
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
        SMA Negeri 1 Pomalaa merupakan salah satu lembaga pendidikan menengah atas negeri yang berlokasi di Jl. Salak No. 2, Kecamatan Pomalaa, Kabupaten Kolaka, Provinsi Sulawesi Tenggara. Berdiri sebagai institusi yang telah lama berkontribusi dalam dunia pendidikan, SMAN 1 Pomalaa terus menunjukkan eksistensinya sebagai sekolah yang unggul, berprestasi, dan berakar kuat pada nilai-nilai luhur bangsa. Dengan landasan filosofi pendidikan yang menempatkan siswa sebagai pusat pembelajaran, sekolah ini tidak hanya berfokus pada pencapaian akademik, tetapi juga pada pembentukan karakter, pengembangan potensi, dan penanaman nilai-nilai moral serta etika.
    </p>
    <p>Didukung oleh tenaga pendidik yang profesional, berkompeten, dan berdedikasi tinggi, SMAN 1 Pomalaa menghadirkan proses belajar yang inovatif dan adaptif terhadap perkembangan zaman. Kurikulum yang diterapkan mengakomodasi kebutuhan abad ke-21 dengan menekankan pada kemampuan berpikir kritis, kreativitas, kolaborasi, serta literasi digital. Berbagai program ekstrakurikuler juga dikembangkan secara aktif untuk menggali dan menyalurkan minat serta bakat siswa, seperti olahraga, seni, sains, keagamaan, hingga kepemimpinan.
    </p>
    <p>
        Lingkungan sekolah yang bersih, aman, dan ramah anak menjadi salah satu kekuatan utama SMAN 1 Pomalaa dalam menciptakan atmosfer belajar yang menyenangkan dan produktif. Fasilitas yang terus diperbarui, mulai dari laboratorium, perpustakaan, ruang kelas modern, hingga sarana ibadah, menjadi penunjang penting dalam mencapai visi sekolah: “Menjadi sekolah yang berprestasi, berkarakter, dan berwawasan global.”
    </p>
    <p>
        Sebagai bagian dari komunitas pendidikan di Kabupaten Kolaka, SMAN 1 Pomalaa juga aktif menjalin kerja sama dengan berbagai pihak, baik instansi pemerintah, perguruan tinggi, dunia usaha, maupun masyarakat sekitar. Hal ini menjadi wujud nyata dari semangat kolaboratif dalam membangun pendidikan yang inklusif dan berdaya saing tinggi.
    </p>
</section>




<?php include 'partials/footer.php'; ?>

</body>
</html>
