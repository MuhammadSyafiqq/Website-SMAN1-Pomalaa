<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/KelasController.php';
require_once __DIR__ . '/controllers/JurusanController.php';
require_once __DIR__ . '/controllers/MataPelajaranController.php';
require_once __DIR__ . '/controllers/JadwalUjianController.php';

$kelasController = new KelasController($connection);
$jurusanController = new JurusanController($connection);
$mapelController = new MataPelajaranController($connection);
$jadwalController = new JadwalUjianController($connection);

$allKelas = $kelasController->index();
$allJurusan = $jurusanController->index();
$allMapel = $mapelController->index();
$allJadwal = $jadwalController->index();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<<<<<<< Updated upstream
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel - Input Data Jadwal Ujian</title>
    <style>
        
    </style>
=======
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="assets/css/admin-style.css">
>>>>>>> Stashed changes
</head>
<body>
  <h1>Panel Admin</h1>

  <section>
    <h2>Kelola Kelas</h2>
    <form method="POST" id="form-kelas">
      <input type="text" name="nama" id="kelas_nama" placeholder="Nama Kelas" required>
      <button type="submit" name="add_kelas">Tambah</button>
    </form>
    <ul>
      <?php foreach ($allKelas as $kelas): ?>
        <li>
          <?= htmlspecialchars($kelas['nama']) ?>
          <button onclick="editKelas(<?= $kelas['id'] ?>, '<?= $kelas['nama'] ?>')">Edit</button>
          <button onclick="confirmDelete('kelas', <?= $kelas['id'] ?>, '<?= $kelas['nama'] ?>')">Hapus</button>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section>
    <h2>Kelola Jurusan</h2>
    <form method="POST" id="form-jurusan">
      <input type="text" name="nama" id="jurusan_nama" placeholder="Nama Jurusan" required>
      <button type="submit" name="add_jurusan">Tambah</button>
    </form>
    <ul>
      <?php foreach ($allJurusan as $jurusan): ?>
        <li>
          <?= htmlspecialchars($jurusan['nama']) ?>
          <button onclick="editJurusan(<?= $jurusan['id'] ?>, '<?= $jurusan['nama'] ?>')">Edit</button>
          <button onclick="confirmDelete('jurusan', <?= $jurusan['id'] ?>, '<?= $jurusan['nama'] ?>')">Hapus</button>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

<<<<<<< Updated upstream
        <section class="form-section" aria-label="Form Input Jurusan">
            <h2>Input Jurusan</h2>
            <form method="POST" aria-describedby="jurusan-desc">
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
=======
  <section>
    <h2>Kelola Mata Pelajaran</h2>
    <form method="POST" id="form-mata-pelajaran">
      <input type="text" name="nama" id="mata_pelajaran_nama" placeholder="Nama Mata Pelajaran" required>
      <select name="kategori" id="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <option value="umum">Umum</option>
        <option value="ipa">IPA</option>
        <option value="ips">IPS</option>
      </select>
      <button type="submit" name="add_mata_pelajaran">Tambah</button>
    </form>
    <ul>
      <?php foreach ($allMapel as $mapel): ?>
        <li>
          <?= htmlspecialchars($mapel['nama']) ?> (<?= $mapel['kategori'] ?>)
          <button onclick="editMataPelajaran(<?= $mapel['id'] ?>, '<?= $mapel['nama'] ?>', '<?= $mapel['kategori'] ?>')">Edit</button>
          <button onclick="confirmDelete('mata_pelajaran', <?= $mapel['id'] ?>, '<?= $mapel['nama'] ?>')">Hapus</button>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>

  <section>
    <h2>Kelola Jadwal Ujian</h2>
    <form method="POST" id="form-jadwal">
      <select name="kelas_id" id="kelas_id" required>
        <option value="">-- Pilih Kelas --</option>
        <?php foreach ($allKelas as $kelas): ?>
          <option value="<?= $kelas['id'] ?>"><?= $kelas['nama'] ?></option>
        <?php endforeach; ?>
      </select>

      <select name="jurusan_id" id="jurusan_id" required>
        <option value="">-- Pilih Jurusan --</option>
        <?php foreach ($allJurusan as $jurusan): ?>
          <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama'] ?></option>
        <?php endforeach; ?>
      </select>

      <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
        <option value="">-- Pilih Mata Pelajaran --</option>
      </select>

      <input type="date" name="tanggal" id="date" required>
      <input type="text" name="hari" id="hari" readonly>
      <input type="time" name="jam_mulai" required>
      <input type="time" name="jam_selesai" required>
      <button type="submit" name="add_jadwal">Tambah</button>
    </form>

    <table>
      <thead>
>>>>>>> Stashed changes
        <tr>
          <th>Kelas</th><th>Jurusan</th><th>Mata Pelajaran</th><th>Tanggal</th><th>Hari</th><th>Jam</th><th>Aksi</th>
        </tr>
<<<<<<< Updated upstream
        <?php endforeach; ?>
        </tbody>
</table>
        </section>
=======
      </thead>
      <tbody>
        <?php foreach ($allJadwal as $jadwal): ?>
          <tr>
            <td><?= $jadwal['kelas_nama'] ?></td>
            <td><?= $jadwal['jurusan_nama'] ?></td>
            <td><?= $jadwal['mata_pelajaran_nama'] ?></td>
            <td><?= $jadwal['tanggal'] ?></td>
            <td><?= $jadwal['hari'] ?></td>
            <td><?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></td>
            <td>
              <button onclick="confirmDelete('jadwal', <?= $jadwal['id'] ?>, '<?= $jadwal['mata_pelajaran_nama'] ?>')">Hapus</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
>>>>>>> Stashed changes

  <script id="allMataPelajaranData" type="application/json">
    <?= json_encode($allMapel); ?>
  </script>
  <script src="assets/js/admin.js"></script>
</body>
</html>
