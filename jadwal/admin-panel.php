<?php
require_once 'config/database.php';
require_once 'models/KelasModel.php';
require_once 'models/JurusanModel.php';
require_once 'models/MataPelajaranModel.php';
require_once 'models/JadwalUjianModel.php';
require_once 'helpers/functions.php';

$kelasModel = new KelasModel($connection);
$jurusanModel = new JurusanModel($connection);
$mapelModel = new MataPelajaranModel($connection);
$jadwalModel = new JadwalUjianModel($connection);

$dataKelas = $kelasModel->getAll();
$dataJurusan = $jurusanModel->getAll();
$dataMapel = $mapelModel->getAll();
$dataJadwal = $jadwalModel->getAll();

// Handle request (kelas, jurusan, mapel, jadwal)
include 'handlers/handle_request.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <script src="admin-panel.js" defer></script>
</head>
<body>
    <h1>Admin Panel</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Kelas Section -->
    <section id="kelas-section">
        <h2>Manajemen Kelas</h2>
        <form method="POST">
            <input type="text" name="kelas_nama" placeholder="Nama Kelas">
            <button type="submit" name="add_kelas">Tambah Kelas</button>
        </form>

        <table>
            <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
            <?php foreach ($dataKelas as $kelas): ?>
                <tr>
                    <td><?= $kelas['id'] ?></td>
                    <td><?= $kelas['nama'] ?></td>
                    <td>
                        <a href="?edit_kelas=<?= $kelas['id'] ?>">Edit</a> |
                        <a href="?delete_kelas=<?= $kelas['id'] ?>" onclick="return confirm('Hapus kelas ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <!-- Jurusan Section -->
    <section id="jurusan-section">
        <h2>Manajemen Jurusan</h2>
        <form method="POST">
            <input type="text" name="jurusan_nama" placeholder="Nama Jurusan">
            <button type="submit" name="add_jurusan">Tambah Jurusan</button>
        </form>

        <table>
            <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
            <?php foreach ($dataJurusan as $jurusan): ?>
                <tr>
                    <td><?= $jurusan['id'] ?></td>
                    <td><?= $jurusan['nama'] ?></td>
                    <td>
                        <a href="?edit_jurusan=<?= $jurusan['id'] ?>">Edit</a> |
                        <a href="?delete_jurusan=<?= $jurusan['id'] ?>" onclick="return confirm('Hapus jurusan ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <!-- Mata Pelajaran Section -->
    <section id="mapel-section">
        <h2>Manajemen Mata Pelajaran</h2>
        <form method="POST">
            <input type="text" name="mapel_nama" placeholder="Nama Mata Pelajaran">
            <select name="mapel_kategori">
                <option value="Umum">Umum</option>
                <option value="IPA">IPA</option>
                <option value="IPS">IPS</option>
            </select>
            <button type="submit" name="add_mapel">Tambah</button>
        </form>

        <table>
            <tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Aksi</th></tr>
            <?php foreach ($dataMapel as $mapel): ?>
                <tr>
                    <td><?= $mapel['id'] ?></td>
                    <td><?= $mapel['nama'] ?></td>
                    <td><?= $mapel['kategori'] ?></td>
                    <td>
                        <a href="?edit_mapel=<?= $mapel['id'] ?>">Edit</a> |
                        <a href="?delete_mapel=<?= $mapel['id'] ?>" onclick="return confirm('Hapus mata pelajaran ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>

    <!-- Jadwal Ujian Section -->
    <section id="jadwal-section">
        <h2>Manajemen Jadwal Ujian</h2>
        <form method="POST">
            <select name="kelas_id" id="kelas_select">
                <?php foreach ($dataKelas as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                <?php endforeach; ?>
            </select>

            <select name="jurusan_id" id="jurusan_select">
                <?php foreach ($dataJurusan as $j): ?>
                    <option value="<?= $j['id'] ?>"><?= $j['nama'] ?></option>
                <?php endforeach; ?>
            </select>

            <select name="mata_pelajaran_id" id="mapel_select">
                <?php foreach ($dataMapel as $m): ?>
                    <option value="<?= $m['id'] ?>" data-kategori="<?= $m['kategori'] ?>"><?= $m['nama'] ?></option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="tanggal" id="tanggal_input">
            <input type="text" name="jam_mulai" placeholder="Jam Mulai">
            <input type="text" name="jam_selesai" placeholder="Jam Selesai">
            <input type="hidden" name="hari" id="hari_input">
            <button type="submit" name="add_jadwal">Tambah Jadwal</button>
        </form>

        <table>
            <tr><th>Kelas</th><th>Jurusan</th><th>Mata Pelajaran</th><th>Tanggal</th><th>Hari</th><th>Jam</th><th>Aksi</th></tr>
            <?php foreach ($dataJadwal as $jadwal): ?>
                <tr>
                    <td><?= $jadwal['kelas_nama'] ?></td>
                    <td><?= $jadwal['jurusan_nama'] ?></td>
                    <td><?= $jadwal['mapel_nama'] ?></td>
                    <td><?= $jadwal['tanggal'] ?></td>
                    <td><?= $jadwal['hari'] ?></td>
                    <td><?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></td>
                    <td>
                        <a href="?edit_jadwal=<?= $jadwal['id'] ?>">Edit</a> |
                        <a href="?delete_jadwal=<?= $jadwal['id'] ?>" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</body>
</html>