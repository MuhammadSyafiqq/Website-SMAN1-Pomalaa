<?php require_once 'theme.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akreditasi - SMAN 1 Pomalaa</title>
    <link rel="stylesheet" href="assets/style/style.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" href="assets/image/logo_sekolah.png">

    <style>
    body.akreditasi-page {
        background: #FFFFFF !important;
    }
    
    .akreditasi-container {
        max-width: 960px;
        margin: 100px auto 60px;
        background: white;
        color: #000;
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .akreditasi-container h1 {
        text-align: center;
        font-size: 30px;
        color: #004030; /* sebelumnya #003366 */
        margin-bottom: 30px;
        border-bottom: 3px solid #004030; /* sebelumnya #00589D */
        padding-bottom: 10px;
    }

    .akreditasi-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .akreditasi-table td {
        padding: 10px;
        vertical-align: top;
        font-size: 16px;
    }

    .akreditasi-table td.label {
        width: 35%;
        font-weight: bold;
    }

    .akreditasi-section-title {
        font-size: 20px;
        margin-top: 30px;
        font-weight: bold;
        color: #004030; /* sebelumnya #003366 */
    }

    .akreditasi-container p {
        text-align: justify;
        line-height: 1.7;
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .akreditasi-container {
            padding: 25px 20px;
        }

        .akreditasi-container h1 {
            font-size: 24px;
        }

        .akreditasi-table td {
            font-size: 15px;
        }

        .akreditasi-section-title {
            font-size: 18px;
        }
    }
</style>

</head>
<body class="akreditasi-page">

<?php include 'partials/navbar.php'; ?>

<div class="akreditasi-container">
    <h1>Sertifikat Akreditasi Sekolah</h1>

    <table class="akreditasi-table">
        <tr>
            <td class="label">Nama Sekolah</td>
            <td>: SMAN 1 Pomalaa</td>
        </tr>
        <tr>
            <td class="label">NPSN</td>
            <td>: 40401535</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td>: Jl. Salak No. 2, Pomalaa, Kab. Kolaka, Sulawesi Tenggara</td>
        </tr>
        <tr>
            <td class="label">Nilai Akreditasi</td>
            <td>: 91</td>
        </tr>
        <tr>
            <td class="label">Peringkat</td>
            <td>: A (Unggul)</td>
        </tr>
        <tr>
            <td class="label">Nomor SK</td>
            <td>: 40/BAP-SM/SULTRA/X/2017</td>
        </tr>
        <tr>
            <td class="label">Tanggal Penetapan</td>
            <td>: 30 Oktober 2017</td>
        </tr>
        <tr>
            <td class="label">Berlaku Hingga</td>
            <td>: 30 Oktober 2022</td>
        </tr>
        <tr>
            <td class="label">Tempat Penetapan</td>
            <td>: Kendari</td>
        </tr>
        <tr>
            <td class="label">Ditandatangani Oleh</td>
            <td>: Prof. Dr. H. Abdullah Alhadza, M.A. (Ketua BAN-S/M Provinsi Sulawesi Tenggara)</td>
        </tr>
    </table>

    <div class="akreditasi-section-title">Deskripsi</div>
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

<?php include 'partials/footer.php'; ?>

<script>
    window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            const scrollPosition = window.scrollY;
            navbar.classList.add('scrolled');
            
        });
</script>

</body>

</html>
