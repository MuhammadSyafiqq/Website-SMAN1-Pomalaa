<?php

session_start();

require_once 'config/database.php';
require_once 'models/KelasModel.php';
require_once 'models/JurusanModel.php';
require_once 'models/MataPelajaranModel.php';
require_once 'models/JadwalUjianModel.php';
require_once 'helpers/functions.php';

$kelasModel = new KelasModel($connection);
$jurusanModel = new JurusanModel($connection);
$mataPelajaranModel = new MataPelajaranModel($connection);
$jadwalModel = new JadwalUjianModel($connection);

$dataKelas = $kelasModel->getAll();
$dataJurusan = $jurusanModel->getAll();
$dataMapel = $mataPelajaranModel->getAll();
$dataJadwal = $jadwalModel->getAll();

// Handle request (kelas, jurusan, mapel, jadwal)
include 'handlers/handle_request.php';


?>

<?php if (isset($_SESSION['success'])): ?>
<div id="flash-message" class="flash-success"><?= $_SESSION['success'] ?></div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div id="flash-message" class="flash-error"><?= $_SESSION['error'] ?></div>
<?php unset($_SESSION['error']); endif; ?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/styles.css?v=2">
    <script src="assets/js/scripts.js" defer></script>
    <script src="js/admin-panel.js" defer></script>
    <style>
  #flash-message {
    position: fixed;
    top: 10%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: bold;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    animation: fadeOut 3s forwards;
  }
  .flash-success {
    background-color: #4CAF50;
    color: white;
  }
  .flash-error {
    background-color: #f44336;
    color: white;
  }
  @keyframes fadeOut {
    0% { opacity: 1; }
    80% { opacity: 1; }
    100% { opacity: 0; display: none; }
  }
</style>

</style>
</head>
<body>
    <h1>Admin Panel</h1>
    <div class ="container"> 

        <!-- Kelas Section -->
        <section class="form-section" aria-label="Form Input Kelas">
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
                        <button class="edit" onclick="editKelas('<?php echo $kelas['id']; ?>', '<?php echo htmlspecialchars($kelas['nama']); ?>')">Edit</button> |
                        <a href="?delete_kelas=<?= $kelas['id'] ?>" onclick="return confirm('Hapus kelas ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        </section>

        <!-- Jurusan Section -->
        <section id="form-section " aria-label="Form Input Jurusan">
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
                        <button class="edit" onclick="editJurusan('<?= $jurusan['id'] ?>', '<?= htmlspecialchars($jurusan['nama']) ?>')">Edit</button>
                        <button class="delete" onclick="confirmDelete('jurusan', '<?= $jurusan['id'] ?>', '<?= htmlspecialchars($jurusan['nama']) ?>')">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <!-- Mata Pelajaran Section -->
        <section id="form-section" aria-label="Form Input Mata Pelajaran">
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
                            <button class="edit" onclick="editMataPelajaran('<?php echo $mapel['id']; ?>', '<?php echo htmlspecialchars($mapel['nama']); ?>', '<?php echo htmlspecialchars($mapel['kategori']); ?>')">Edit</button>
                            <button class="delete" onclick="confirmDelete('mata_pelajaran', '<?php echo $mapel['id']; ?>', '<?php echo htmlspecialchars($mapel['nama']); ?>')">Hapus</button>
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
        </div>
    </div>

    <!-- Modal Edit Kelas -->
<div id="editKelasModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editKelasModal')">&times;</span>
        <h3>Edit Kelas</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_kelas_id">
            <label for="edit_kelas_nama">Nama Kelas:</label>
            <input type="text" name="edit_nama" id="edit_kelas_nama" required maxlength="10">
            <button type="submit" name="edit_kelas">Update Kelas</button>
        </form>
    </div>
</div>

<!-- Modal Edit Jurusan -->
<div id="editJurusanModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editJurusanModal')">&times;</span>
        <h3>Edit Jurusan</h3>
        <form method="POST">
            <input type="hidden" name="edit_id" id="edit_jurusan_id">
            <label for="edit_jurusan_nama">Nama Jurusan:</label>
            <input type="text" name="edit_nama" id="edit_jurusan_nama" required maxlength="10">
            <button type="submit" name="edit_jurusan">Update Jurusan</button>
        </form>
    </div>
</div>

<!-- Modal Edit Mata Pelajaran -->
<div id="editMataPelajaranModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editMataPelajaranModal')">&times;</span>
        <h3>Edit Mata Pelajaran</h3>
        <form method="POST">    
            <input type="hidden" name="edit_id" id="edit_mp_id">
            <label for="edit_mp_nama">Nama Mata Pelajaran:</label>
            <input type="text" name="edit_nama" id="edit_mp_nama" required maxlength="100">
            <label for="edit_mp_kategori">Kategori:</label>
            <select name="edit_kategori" id="edit_mp_kategori" required>
                <option value="umum">Umum</option>
                <option value="ipa">Peminatan IPA</option>
                <option value="ips">Peminatan IPS</option>
            </select>
            <button type="submit" name="edit_mata_pelajaran">Update Mata Pelajaran</button>
        </form>
    </div>
</div>

<!-- Modal Edit Jadwal Ujian -->
<!-- Modal Edit Jadwal Ujian -->
    <div id="editJadwalUjianModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="editJadwalUjianTitle">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editJadwalUjianModal')" aria-label="Close">&times;</span>
            <h3 id="editJadwalUjianTitle">Edit Jadwal Ujian</h3>
            <form method="POST">
                <input type="hidden" name="edit_id" id="edit_ju_id">
                
                <label for="edit_ju_kelas">Kelas:</label>
                <select name="edit_kelas_id" id="edit_ju_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?php echo $k['id'] ?>"><?php echo htmlspecialchars($k['nama']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="edit_ju_jurusan">Jurusan:</label>
                <select name="edit_jurusan_id" id="edit_ju_jurusan" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo $j['id'] ?>"><?php echo htmlspecialchars($j['nama']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="edit_ju_mata_pelajaran">Mata Pelajaran:</label>
                <select name="edit_mata_pelajaran_id" id="edit_ju_mata_pelajaran" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach ($mata_pelajaran_list as $mp): ?>
                        <option value="<?php echo $mp['id'] ?>"><?php echo htmlspecialchars($mp['nama']) ?> (<?php echo htmlspecialchars($mp['kategori']) ?>)</option>
                    <?php endforeach; ?>
                </select>

                <label for="edit_ju_date">Tanggal:</label>
                <input type="date" name="edit_date" id="edit_ju_date" required />

                <!-- Removed manual hari select -->

                <label for="edit_ju_jam_mulai">Jam Mulai:</label>
                <input type="time" name="edit_jam_mulai" id="edit_ju_jam_mulai" required>

                <label for="edit_ju_jam_selesai">Jam Selesai:</label>
                <input type="time" name="edit_jam_selesai" id="edit_ju_jam_selesai" required>

                <button type="submit" name="edit_jadwal_ujian">Update Jadwal Ujian</button>
            </form>
        </div>
    </div>
</body>
</html>