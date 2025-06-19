<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Kelola Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Panel Admin - Kelola Data Sekolah</h1>
    <div class="container">

        <!-- PESAN -->
        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Gagal') !== false) ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <!-- KELAS -->
        <section class="form-section" aria-label="Kelola Kelas">
            <h2>Kelola Kelas</h2>
            <form method="POST">
                <label for="kelas_nama">Nama Kelas</label>
                <input type="text" name="kelas_nama" id="kelas_nama" required>
                <button type="submit" name="add_kelas">Tambah</button>
            </form>
            <table>
                <thead><tr><th>ID</th><th>Nama</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($kelas_list as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="actions">
                                <button onclick="editKelas('<?php echo $row['id']; ?>', '<?php echo $row['nama']; ?>')">Edit</button>
                                <button onclick="confirmDelete('kelas', '<?php echo $row['id']; ?>')">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- JURUSAN -->
        <section class="form-section" aria-label="Kelola Jurusan">
            <h2>Kelola Jurusan</h2>
            <form method="POST">
                <label for="jurusan_nama">Nama Jurusan</label>
                <input type="text" name="jurusan_nama" id="jurusan_nama" required>
                <button type="submit" name="add_jurusan">Tambah</button>
            </form>
            <table>
                <thead><tr><th>ID</th><th>Nama</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($jurusan_list as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="actions">
                                <button onclick="editJurusan('<?php echo $row['id']; ?>', '<?php echo $row['nama']; ?>')">Edit</button>
                                <button onclick="confirmDelete('jurusan', '<?php echo $row['id']; ?>')">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- MATA PELAJARAN -->
        <section class="form-section" aria-label="Kelola Mata Pelajaran">
            <h2>Kelola Mata Pelajaran</h2>
            <form method="POST">
                <label for="mata_pelajaran_nama">Nama Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran_nama" id="mata_pelajaran_nama" required>
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" required>
                    <option value="umum">Umum</option>
                    <option value="ipa">IPA</option>
                    <option value="ips">IPS</option>
                </select>
                <button type="submit" name="add_mata_pelajaran">Tambah</button>
            </form>
            <?php foreach ($mata_pelajaran_by_category as $kategori => $mata_pelajaran_list): ?>
                <h3>Kategori: <?php echo ucfirst($kategori); ?></h3>
                <table>
                    <thead><tr><th>ID</th><th>Nama</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($mata_pelajaran_list as $mp): ?>
                            <tr>
                                <td><?php echo $mp['id']; ?></td>
                                <td><?php echo htmlspecialchars($mp['nama']); ?></td>
                                <td class="actions">
                                    <button onclick="editMataPelajaran('<?php echo $mp['id']; ?>', '<?php echo $mp['nama']; ?>', '<?php echo $mp['kategori']; ?>')">Edit</button>
                                    <button onclick="confirmDelete('mata_pelajaran', '<?php echo $mp['id']; ?>')">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        </section>

        <!-- JADWAL UJIAN -->
        <section class="form-section" aria-label="Kelola Jadwal Ujian">
            <h2>Kelola Jadwal Ujian</h2>
            <form method="POST">
                <label for="kelas_id">Kelas</label>
                <select name="kelas_id" id="kelas_id" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo $k['nama']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="jurusan_id">Jurusan</label>
                <select name="jurusan_id" id="jurusan_id" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo $j['id']; ?>"><?php echo $j['nama']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="mata_pelajaran_id">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
                    <option value="">-- Pilih --</option>
                </select>

                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required>

                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" name="jam_mulai" id="jam_mulai" required>

                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" name="jam_selesai" id="jam_selesai" required>

                <button type="submit" name="add_jadwal_ujian">Tambah Jadwal</button>
            </form>

            <!-- Filter -->
            <label for="filter_jurusan">Filter Jurusan:</label>
            <select id="filter_jurusan">
                <option value="all">Semua</option>
                <?php foreach ($jurusan_list as $j): ?>
                    <option value="<?php echo $j['nama']; ?>"><?php echo $j['nama']; ?></option>
                <?php endforeach; ?>
            </select>

            <div id="jadwal-container">
                <p>Memuat data jadwal ujian...</p>
            </div>
        </section>
    </div>

    <script src="scripts/jadwal_dynamic_filter.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Kelola Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Panel Admin - Kelola Data Sekolah</h1>
    <div class="container">

        <!-- PESAN -->
        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Gagal') !== false) ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <!-- KELAS -->
        <section class="form-section" aria-label="Kelola Kelas">
            <h2>Kelola Kelas</h2>
            <form method="POST">
                <label for="kelas_nama">Nama Kelas</label>
                <input type="text" name="kelas_nama" id="kelas_nama" required>
                <button type="submit" name="add_kelas">Tambah</button>
            </form>
            <table>
                <thead><tr><th>ID</th><th>Nama</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($kelas_list as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="actions">
                                <button onclick="editKelas('<?php echo $row['id']; ?>', '<?php echo $row['nama']; ?>')">Edit</button>
                                <button onclick="confirmDelete('kelas', '<?php echo $row['id']; ?>')">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- JURUSAN -->
        <section class="form-section" aria-label="Kelola Jurusan">
            <h2>Kelola Jurusan</h2>
            <form method="POST">
                <label for="jurusan_nama">Nama Jurusan</label>
                <input type="text" name="jurusan_nama" id="jurusan_nama" required>
                <button type="submit" name="add_jurusan">Tambah</button>
            </form>
            <table>
                <thead><tr><th>ID</th><th>Nama</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($jurusan_list as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td class="actions">
                                <button onclick="editJurusan('<?php echo $row['id']; ?>', '<?php echo $row['nama']; ?>')">Edit</button>
                                <button onclick="confirmDelete('jurusan', '<?php echo $row['id']; ?>')">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- MATA PELAJARAN -->
        <section class="form-section" aria-label="Kelola Mata Pelajaran">
            <h2>Kelola Mata Pelajaran</h2>
            <form method="POST">
                <label for="mata_pelajaran_nama">Nama Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran_nama" id="mata_pelajaran_nama" required>
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori" required>
                    <option value="umum">Umum</option>
                    <option value="ipa">IPA</option>
                    <option value="ips">IPS</option>
                </select>
                <button type="submit" name="add_mata_pelajaran">Tambah</button>
            </form>
            <?php foreach ($mata_pelajaran_by_category as $kategori => $mata_pelajaran_list): ?>
                <h3>Kategori: <?php echo ucfirst($kategori); ?></h3>
                <table>
                    <thead><tr><th>ID</th><th>Nama</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($mata_pelajaran_list as $mp): ?>
                            <tr>
                                <td><?php echo $mp['id']; ?></td>
                                <td><?php echo htmlspecialchars($mp['nama']); ?></td>
                                <td class="actions">
                                    <button onclick="editMataPelajaran('<?php echo $mp['id']; ?>', '<?php echo $mp['nama']; ?>', '<?php echo $mp['kategori']; ?>')">Edit</button>
                                    <button onclick="confirmDelete('mata_pelajaran', '<?php echo $mp['id']; ?>')">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        </section>

        <!-- JADWAL UJIAN -->
        <section class="form-section" aria-label="Kelola Jadwal Ujian">
            <h2>Kelola Jadwal Ujian</h2>
            <form method="POST">
                <label for="kelas_id">Kelas</label>
                <select name="kelas_id" id="kelas_id" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($kelas_list as $k): ?>
                        <option value="<?php echo $k['id']; ?>"><?php echo $k['nama']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="jurusan_id">Jurusan</label>
                <select name="jurusan_id" id="jurusan_id" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?php echo $j['id']; ?>"><?php echo $j['nama']; ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="mata_pelajaran_id">Mata Pelajaran</label>
                <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
                    <option value="">-- Pilih --</option>
                </select>

                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required>

                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" name="jam_mulai" id="jam_mulai" required>

                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" name="jam_selesai" id="jam_selesai" required>

                <button type="submit" name="add_jadwal_ujian">Tambah Jadwal</button>
            </form>

            <!-- Filter -->
            <label for="filter_jurusan">Filter Jurusan:</label>
            <select id="filter_jurusan">
                <option value="all">Semua</option>
                <?php foreach ($jurusan_list as $j): ?>
                    <option value="<?php echo $j['nama']; ?>"><?php echo $j['nama']; ?></option>
                <?php endforeach; ?>
            </select>

            <div id="jadwal-container">
                <p>Memuat data jadwal ujian...</p>
            </div>
        </section>
    </div>

    <script src="scripts/jadwal_dynamic_filter.js"></script>
</body>
</html>
