<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Jadwal Ujian</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="/">Beranda</a></li>
                <li><a href="/kelas">Kelas</a></li>
                <li><a href="/jurusan">Jurusan</a></li>
                <li><a href="/mata-pelajaran">Mata Pelajaran</a></li>
                <li><a href="/jadwal-ujian">Jadwal Ujian</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> SMAN 1 Pomalaa</p>
    </footer>

    <script src="/assets/js/script.js"></script>
</body>
</html>
