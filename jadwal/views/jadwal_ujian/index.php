<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/KelasModel.php';
require_once __DIR__ . '/../../models/JurusanModel.php';
require_once __DIR__ . '/../../models/MataPelajaranModel.php';
require_once __DIR__ . '/../../models/JadwalUjianModel.php';

$kelasModel = new KelasModel($connection);
$jurusanModel = new JurusanModel($connection);
$mataPelajaranModel = new MataPelajaranModel($connection);
$jadwalUjianModel = new JadwalUjianModel($connection);

$kelas_list = $kelasModel->getAll()->fetch_all(MYSQLI_ASSOC);
$jurusan_list = $jurusanModel->getAll()->fetch_all(MYSQLI_ASSOC);
$mapel_list = $mataPelajaranModel->getAll()->fetch_all(MYSQLI_ASSOC);
$jadwal_list = $jadwalUjianModel->getAll()->fetch_all(MYSQLI_ASSOC);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Jadwal Ujian</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Dashboard Admin</h1>

  <?php if ($message): ?>
    <div class="alert"><?= $message ?></div>
  <?php endif; ?>

  <h2>Form <?= isset($editData) ? 'Edit' : 'Tambah' ?> Jadwal Ujian</h2>
  <form method="POST" action="controllers/JadwalUjianController.php">
    <?php if (isset($editData)): ?>
      <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
    <?php endif; ?>

    <label>Kelas:</label>
    <select name="kelas" id="kelas" required>
      <?php foreach ($kelas_list as $row): ?>
        <option value="<?= $row['id'] ?>" <?= (isset($editData) && $editData['kelas_id'] == $row['id']) ? 'selected' : '' ?>>
          <?= $row['nama'] ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Jurusan:</label>
    <select name="jurusan" id="jurusan" required>
      <?php foreach ($jurusan_list as $row): ?>
        <option value="<?= $row['id'] ?>" <?= (isset($editData) && $editData['jurusan_id'] == $row['id']) ? 'selected' : '' ?>>
          <?= $row['nama'] ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Mata Pelajaran:</label>
    <select name="mapel" id="mapel" required>
      <?php foreach ($mapel_list as $row): ?>
        <option value="<?= $row['id'] ?>" <?= (isset($editData) && $editData['mata_pelajaran_id'] == $row['id']) ? 'selected' : '' ?>>
          <?= $row['nama'] ?> (<?= $row['kategori'] ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <label>Tanggal:</label>
    <input type="date" name="tanggal" value="<?= $editData['tanggal'] ?? '' ?>" required>

    <label>Jam Mulai:</label>
    <input type="time" name="jam_mulai" value="<?= $editData['jam_mulai'] ?? '' ?>" required>

    <label>Jam Selesai:</label>
    <input type="time" name="jam_selesai" value="<?= $editData['jam_selesai'] ?? '' ?>" required>

    <button type="submit" name="<?= isset($editData) ? 'edit_jadwal' : 'add_jadwal' ?>">
      <?= isset($editData) ? 'Update' : 'Tambah' ?>
    </button>
  </form>

  <h2>Data Jadwal Ujian</h2>
  <table border="1" cellpadding="5">
    <thead>
      <tr>
        <th>ID</th>
        <th>Kelas</th>
        <th>Jurusan</th>
        <th>Mata Pelajaran</th>
        <th>Tanggal</th>
        <th>Hari</th>
        <th>Jam Mulai</th>
        <th>Jam Selesai</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($jadwal_list as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= $row['kelas_id'] ?></td>
          <td><?= $row['jurusan_id'] ?></td>
          <td><?= $row['mata_pelajaran_id'] ?></td>
          <td><?= $row['tanggal'] ?></td>
          <td><?= $row['hari'] ?></td>
          <td><?= $row['jam_mulai'] ?></td>
          <td><?= $row['jam_selesai'] ?></td>
          <td>
            <a href="?edit=<?= $row['id'] ?>">Edit</a> |
            <a href="controllers/JadwalUjianController.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const kelasSelect = document.getElementById('kelas');
    const jurusanSelect = document.getElementById('jurusan');
    const mapelSelect = document.getElementById('mapel');

    function loadMapel() {
      const kelasId = kelasSelect.value;
      const jurusanId = jurusanSelect.value;

      if (!kelasId || !jurusanId) return;

      fetch(`ajax/mata_pelajaran_filter.php?kelas_id=${kelasId}&jurusan_id=${jurusanId}`)
        .then(res => res.json())
        .then(data => {
          mapelSelect.innerHTML = '';
          data.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = `${m.nama} (${m.kategori})`;
            mapelSelect.appendChild(opt);
          });
        });
    }

    kelasSelect.addEventListener('change', loadMapel);
    jurusanSelect.addEventListener('change', loadMapel);
  });
  </script>
</body>
</html>
