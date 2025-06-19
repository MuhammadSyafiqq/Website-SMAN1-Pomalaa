<?php
// File: admin_panel.php (satu halaman untuk semua fitur admin)

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/functions.php';
require_once __DIR__ . '/models/KelasModel.php';
require_once __DIR__ . '/models/JurusanModel.php';
require_once __DIR__ . '/models/MataPelajaranModel.php';
require_once __DIR__ . '/models/JadwalUjianModel.php';

$kelasModel = new KelasModel($connection);
$jurusanModel = new JurusanModel($connection);
mataPelajaranModel = new MataPelajaranModel($connection);
$jadwalUjianModel = new JadwalUjianModel($connection);

$kelas_list = $kelasModel->getAll()->fetch_all(MYSQLI_ASSOC);
$jurusan_list = $jurusanModel->getAll()->fetch_all(MYSQLI_ASSOC);
$mata_pelajaran_all = $mataPelajaranModel->getAll()->fetch_all(MYSQLI_ASSOC);
$mata_pelajaran_by_category = $mataPelajaranModel->getByKategori();
$jadwal_ujian_grouped = $jadwalUjianModel->getGroupedByJurusan();

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Tangani semua aksi CRUD di satu controller (atau gunakan file controller terpisah per tindakan jika perlu)
require_once __DIR__ . '/controllers/KelasController.php';
require_once __DIR__ . '/controllers/JurusanController.php';
require_once __DIR__ . '/controllers/MataPelajaranController.php';
require_once __DIR__ . '/controllers/JadwalUjianController.php';

// Tampilkan view utama (semua form dan tabel dalam satu halaman)
include __DIR__ . '/views/admin_panel_index.php';
<h2>Daftar Mata Pelajaran</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div style="background:#d4edda;padding:10px;border-radius:5px;">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<form method="POST" action="../../controllers/MataPelajaranController.php">
    <input type="text" name="nama" placeholder="Nama mata pelajaran" required>
    <select name="kategori" required>
        <option value="">Pilih Kategori</option>
        <option value="IPA">IPA</option>
        <option value="IPS">IPS</option>
        <option value="Umum">Umum</option>
    </select>
    <button type="submit" name="add_mapel">Tambah</button>
</form>

<table border="1" cellpadding="5" cellspacing="0" style="margin-top:20px;">
    <thead>
        <tr>
            <th>ID</th><th>Nama</th><th>Kategori</th><th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $data->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['kategori'] ?></td>
                <td>
                    <form method="POST" action="../../controllers/MataPelajaranController.php" style="display:inline;">
                        <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                        <input type="text" name="edit_nama" value="<?= $row['nama'] ?>" required>
                        <select name="edit_kategori" required>
                            <option value="IPA" <?= $row['kategori'] == 'IPA' ? 'selected' : '' ?>>IPA</option>
                            <option value="IPS" <?= $row['kategori'] == 'IPS' ? 'selected' : '' ?>>IPS</option>
                            <option value="Umum" <?= $row['kategori'] == 'Umum' ? 'selected' : '' ?>>Umum</option>
                        </select>
                        <button type="submit" name="edit_mapel">Update</button>
                    </form>
                    <a href="../../controllers/MataPelajaranController.php?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
