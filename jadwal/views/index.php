<?php
session_start();
require_once '../config/database.php';
require_once '../models/Kelas.php';
require_once '../models/Jurusan.php';
require_once '../models/MataPelajaran.php';
require_once '../models/JadwalUjian.php';

// Instantiate models
$kelasModel = new Kelas($connection);
$jurusanModel = new Jurusan($connection);
$mataPelajaranModel = new MataPelajaran($connection);
$jadwalUjianModel = new JadwalUjian($connection);

// Get all data for display
$kelas_list = $kelasModel->getAll();
$jurusan_list = $jurusanModel->getAll();
$kategori_filter = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : null;
$mata_pelajaran_list = $mataPelajaranModel->getAll($kategori_filter);
$mata_pelajaran_by_category = $mataPelajaranModel->getByCategory();
$jadwal_ujian_list = $jadwalUjianModel->getAll();

// Group jadwal by jurusan and kelas for frontend rendering
$grouped_jadwal = [];
foreach ($jadwal_ujian_list as $item) {
    $jurusan = $item['jurusan'];
    $kelas = $item['kelas'];
    if (!isset($grouped_jadwal[$jurusan])) {
        $grouped_jadwal[$jurusan] = [];
    }
    if (!isset($grouped_jadwal[$jurusan][$kelas])) {
        $grouped_jadwal[$jurusan][$kelas] = [];
    }
    $grouped_jadwal[$jurusan][$kelas][] = $item;
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Encode grouped jadwal for JS
$grouped_jadwal_json = json_encode($grouped_jadwal);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Input Data Jadwal Ujian</title>
    <link rel="stylesheet" href="../assets/css/styles.css" />
</head>
<body>
    <h1>Admin Panel - Input Data Jadwal Ujian</h1>

    <div class="container">
        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Gagal') !== false) ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <!-- Kelas Section -->
        <section class="form-section" aria-label="Form Input Kelas">
            <h2>Kelola Kelas</h2>
            <form method="POST" action="../controllers/KelasController.php" aria-describedby="kelas-desc">
                <label for="kelas_nama">Nama Kelas (misal: X, XI, XII)</label>
                <input type="text" id="kelas_nama" name="kelas_nama" maxlength="10" placeholder="Contoh: X" required />
                <button type="submit" name="add_kelas">Tambahkan Kelas</button>
            </form>
            <table aria-label="Daftar Kelas">
                <thead>
                    <tr><th>ID</th><th>Nama Kelas</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($kelas_list as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="actions">
                            <button class="edit" onclick="editKelas('<?php echo htmlspecialchars($row['id']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Edit</button>
                            <button class="delete" onclick="confirmDelete('kelas', '<?php echo htmlspecialchars($row['id']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Jurusan Section -->
        <section class="form-section" aria-label="Form Input Jurusan">
            <h2>Input Jurusan</h2>
            <form method="POST" action="../controllers/JurusanController.php" aria-describedby="jurusan-desc">
                <label for="jurusan_nama">Nama Jurusan (misal: IPA, IPS)</label>
                <input type="text" id="jurusan_nama" name="jurusan_nama" maxlength="10" placeholder="Contoh: IPA" required />
                <button type="submit" name="add_jurusan">Tambahkan Jurusan</button>
            </form>
            <table aria-label="Daftar Jurusan">
                <thead>
                    <tr><th>ID</th><th>Nama Jurusan</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($jurusan_list as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="actions">
                            <button class="edit" onclick="editJurusan('<?php echo htmlspecialchars($row['id']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Edit</button>
                            <button class="delete" onclick="confirmDelete('jurusan', '<?php echo htmlspecialchars($row['id']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Mata Pelajaran Section -->
        <section class="form-section" aria-label="Form Input Mata Pelajaran">
            <h2>Input Mata Pelajaran</h2>
            <form method="POST" action="../controllers/MataPelajaranController.php" aria-describedby="mp-desc">
                <label for="mata_pelajaran_nama">Nama Mata Pelajaran</label>
                <input type="text" id="mata_pelajaran_nama" name="mata_pelajaran_nama" maxlength="100" placeholder="Contoh: Matematika" required />
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="umum">Umum</option>
                    <option value="ipa">Peminatan IPA</option>
                    <option value="ips">Peminatan IPS</option>
                </select>
                <button type="submit" name="add_mata_pelajaran">Tambahkan Mata Pelajaran</button>
            </form>
        </section>
        <section class="form-section" aria-label="Daftar Mata Pelajaran">
            <h2>Daftar Mata Pelajaran</h2>
            <?php foreach ($mata_pelajaran_by_category as $kategori => $mata_pelajaran_list): ?>
                <h3>Mata Pelajaran Jurusan <?php echo htmlspecialchars(ucfirst($kategori)); ?></h3>
                <table aria-label="Daftar Mata Pelajaran <?php echo htmlspecialchars($kategori); ?>">
                    <thead>
                        <tr><th>ID</th><th>Nama Mata Pelajaran</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mata_pelajaran_list as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="actions">
                                <button class="edit" onclick="editMataPelajaran('<?php echo htmlspecialchars($row['id']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>', '<?php echo htmlspecialchars($row['kategori']); ?>')">Edit</button>
                                <button class="delete" onclick="confirmDelete('mata_pelajaran', '<?php echo htmlspecialchars($row['id']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        </section>

        <!-- Jadwal Ujian Section -->
        <section class="form-section" aria-label="Form Input Jadwal Ujian">
            <h2>Input Jadwal Ujian</h2>
            <form method="POST" action="../controllers/JadwalUjianController.php" aria-describedby="jadwal-desc" id="jadwalForm">
                <label for="kelas_id">Pilih Kelas</label>
                <select name="kelas_id" id="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?php echo htmlspecialchars($k['id']); ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="jurusan_id">Pilih Jurusan</label>
                <select name="jurusan_id" id="jurusan_id" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo htmlspecialchars($j['id']); ?>"><?php echo htmlspecialchars($j['nama']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="mata_pelajaran_id">Pilih Mata Pelajaran</label>
                <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                </select>

                <label for="date">Tanggal</label>
                <input type="date" id="date" name="date" required />

                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" required />

                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" required />

                <button type="submit" name="add_jadwal_ujian">Tambahkan Jadwal Ujian</button>
            </form>

            <div class="filter-section" aria-label="Filter Jadwal Ujian berdasarkan Jurusan">
                <label for="filter_jurusan">Filter Jadwal Ujian berdasarkan Jurusan:</label>
                <select id="filter_jurusan">
                    <option value="all">Semua Jurusan</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo htmlspecialchars($j['nama']); ?>"><?php echo htmlspecialchars($j['nama']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="jadwal-container" aria-live="polite" aria-relevant="additions removals">
                <p>Memuat data jadwal ujian...</p>
            </div>
        </section>
    </div>

    <script>
        // Inject groupedJadwal data for JS use
        window.groupedJadwal = <?php echo $grouped_jadwal_json; ?>;
    </script>
    <script src="../assets/js/scripts.js"></script>
</body>
</html>

