    <?php

    
    session_start();
    
    $timeout_duration = 900; 
    
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=true");
    }
    $_SESSION['LAST_ACTIVITY'] = time(); 
    
    require_once 'theme.php';
    
    // Cek jika belum login
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
 
    require_once '../config/database.php';
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


    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Panel - Jadwal Ujian</title>
        <link rel="stylesheet" href="assets/css/styles.css?v=2">
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
            #hasil-jadwal-ujian table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

#hasil-jadwal-ujian th, #hasil-jadwal-ujian td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: center;
}

#hasil-jadwal-ujian h3 {
  margin-top: 20px;
}

            @keyframes fadeOut {
                0% { opacity: 1; }
                80% { opacity: 1; }
                100% { opacity: 0; display: none; }
            }
        </style>
    </head>
    <body>

    <div id="loading-overlay"></div>

    <?php if (isset($_SESSION['success'])): ?>
    <div id="flash-message" class="flash-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div id="flash-message" class="flash-error"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); endif; ?>
        <h1>Admin Panel</h1>
        <div class ="container"> 

            <!-- Kelas Section -->
            <section class="form-section" aria-label="Form Input Kelas">
            <h2>Manajemen Kelas</h2>
            <form method="POST">
                <input type="text" name="kelas_nama" placeholder="Nama Kelas">
                <button type="submit" name="add_kelas">Tambah</button>
            </form>

            <button type="button" class="toggle-table" data-target="kelas-table">Lihat Data</button>

            <table id="kelas-table" style="display: none">
                <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
                <?php foreach ($dataKelas as $kelas): ?>
                    <tr>
                        <td><?= $kelas['id'] ?></td>
                        <td><?= $kelas['nama'] ?></td>
                        <td style="text-align: right;">
                        <div class="action-menu-wrapper">
                            <button type="button" class="action-menu-btn">⋮</button>
                            <div class="action-menu-content">
                            <button onclick="editKelas('<?= $kelas['id'] ?>', '<?= htmlspecialchars($kelas['nama']) ?>')">Edit</button>
                            <button class="delete" onclick="confirmDelete('kelas', '<?= $kelas['id'] ?>', '<?= htmlspecialchars($kelas['nama']) ?>')">Hapus</button>
                            </div>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </section>

            <!-- Jurusan Section -->
            <section class="form-section " aria-label="Form Input Jurusan">
                <h2>Manajemen Jurusan</h2>
                <form method="POST">
                    <input type="text" name="jurusan_nama" placeholder="Nama Jurusan">
                    <button type="submit" name="add_jurusan">Tambah</button>
                </form>

                <button type="button" class="toggle-table" data-target="jurusan-table">Lihat Data</button>

                <table id="jurusan-table" style="display: none">
                    <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
                    <?php foreach ($dataJurusan as $jurusan): ?>
                        <tr>
                            <td><?= $jurusan['id'] ?></td>
                            <td><?= $jurusan['nama'] ?></td>
                            <td style="text-align: right;">
                            <div class="action-menu-wrapper">
                                <button type="button" class="action-menu-btn">⋮</button>
                                <div class="action-menu-content">
                                <button class="edit" onclick="editJurusan('<?= $jurusan['id'] ?>', '<?= htmlspecialchars($jurusan['nama']) ?>')">Edit</button>
                                <button class="delete" onclick="confirmDelete('jurusan', '<?= $jurusan['id'] ?>', '<?= htmlspecialchars($jurusan['nama']) ?>')">Hapus</button>
                                </div>
                            </div>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </section>

            <!-- Mata Pelajaran Section -->
            <section class="form-section" aria-label="Form Input Mata Pelajaran">
                <h2>Manajemen Mata Pelajaran</h2>
                <form method="POST">
                    <input type="text" name="nama" placeholder="Nama Mata Pelajaran">
                    <select name="kategori">
                        <option value="Umum">Umum</option>
                        <option value="IPA">IPA</option>
                        <option value="IPS">IPS</option>
                    </select>
                    <button type="submit" name="add_mata_pelajaran">Tambah</button>
                </form>

                <button type="button" class="toggle-table" data-target="mata-pelajaran-table">Lihat Data</button>

                <table id="mata-pelajaran-table" style="display: none">
                    <tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Aksi</th></tr>
                    <?php foreach ($dataMapel as $mapel): ?>
                        <tr>
                            <td><?= $mapel['id'] ?></td>
                            <td><?= $mapel['nama'] ?></td>
                            <td><?= $mapel['kategori'] ?></td>
                            <td style="position: relative;">
                            <div class="action-menu-wrapper">
                                <button type="button" class="action-menu-btn">⋮</button>
                                <div class="action-menu-content">
                                    <button class="edit" onclick="editMataPelajaran('<?php echo $mapel['id']; ?>', '<?php echo htmlspecialchars($mapel['nama']); ?>', '<?php echo htmlspecialchars($mapel['kategori']); ?>')">Edit</button>
                                    <button class="delete" onclick="confirmDelete('mata_pelajaran', '<?php echo $mapel['id']; ?>', '<?php echo htmlspecialchars($mapel['nama']); ?>')">Hapus</button>
                                </div>
                            </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </section>

            <!-- Jadwal Ujian Section --> 
            <section class="form-section">
                <h2>Manajemen Jadwal Ujian</h2>
                <form method="POST">
                    <select name="kelas_id" id="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($dataKelas as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
                    <?php endforeach; ?>
                    </select>

                    <select name="jurusan_id" id="jurusan_id" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($dataJurusan as $j): ?>
                        <option value="<?= $j['id'] ?>"><?= $j['nama'] ?></option>
                    <?php endforeach; ?>
                    </select>

                    <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    <?php foreach ($dataMapel as $mapel): ?>
                        <option value="<?= $mapel['id'] ?>">
                            <?= $mapel['nama'] ?> (<?= ucfirst(strtolower($mapel['kategori'])) ?>)
                        </option>
                    <?php endforeach; ?>
                    </select>


                    <input type="date" name="tanggal" id="tanggal_input">
                    <input type='time' class="time" name="jam_mulai" placeholder="Jam Mulai">
                    <input type="time" name="jam_selesai" placeholder="Jam Selesai">
                    <input type="hidden" name="hari" id="hari_input">
                    <button type="submit" name="add_jadwal">Tambah</button>
                </form>

                <button type="button" class="toggle-table" data-target="jadwal-table">Lihat Data</button>

                <table id="jadwal-table" style="display: none">
                    <tr><th>Kelas</th><th>Jurusan</th><th>Mata Pelajaran</th><th>Hari</th><th>Tanggal</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Aksi</th></tr>
                    <?php foreach ($dataJadwal as $jadwal): ?>
                        <tr>
                            <td><?= $jadwal['kelas_nama'] ?></td>
                            <td><?= $jadwal['jurusan_nama'] ?></td>
                            <td><?= $jadwal['mata_pelajaran_nama'] ?></td>
                            <td><?= $jadwal['hari'] ?></td>
                            <td><?= $jadwal['tanggal'] ?></td>
                            <td><?= $jadwal['jam_mulai'] ?></td>
                            <td><?= $jadwal['jam_selesai'] ?></td>
                            <td style="text-align: right;">
                            <div class="action-menu-wrapper">
                                <button type="button" class="action-menu-btn">⋮</button>
                                <div class="action-menu-content">
                                <button class="edit" onclick="editJadwal('<?= $jadwal['id'] ?>')">Edit</button>
                                <button class="delete" onclick="confirmDelete('jadwal', '<?= $jadwal['id'] ?>', 'Jadwal <?= htmlspecialchars($jadwal['mata_pelajaran_nama']) ?>')">Hapus</button>
                                </div>
                            </div
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </section>

    <section id="filter-jadwal-ujian" class="form-section">
    <h2>📅 Filter Jadwal Ujian</h2>
    
    <div class="filter-container">
        <div class="filter-form">
            <div class="form-group">
                <label for="filter_kelas_id">Kelas:</label>
                <select name="kelas_id" id="filter_kelas_id">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelasModel->getAll() as $kelas): ?>
                        <option value="<?= $kelas['id'] ?>"><?= $kelas['nama'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="filter_jurusan_id">Jurusan:</label>
                <select name="jurusan_id" id="filter_jurusan_id">
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($jurusanModel->getAll() as $jurusan): ?>
                        <option value="<?= $jurusan['id'] ?>"><?= $jurusan['nama'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="filter-buttons">
            <button type="button" class="btn btn-primary" onclick="filterJadwalEnhanced()">
                🔍 Filter Jadwal
            </button>
            <button type="button" class="btn btn-secondary" onclick="showAllSchedulesGrouped()">
                📋 Tampilkan Semua (Dikelompokkan)
            </button>
            <button type="button" class="btn btn-reset" onclick="resetFilter()">
                🔄 Reset Filter
            </button>
        </div>
    </div>
    
    <div id="hasil-jadwal-ujian" class="results-container"></div>
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

    <!-- Modal Edit Jadwal Ujian - PERBAIKAN -->
<div id="editJadwalUjianModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editJadwalUjianModal')">&times;</span>
        <h2>Edit Jadwal Ujian</h2>
        
        <!-- PERBAIKAN: Form action dan method -->
        <form method="POST">
            <input type="hidden" id="edit_jadwal_id" name="edit_id">
            
           
                <input type="hidden" id="edit_kelas_nama" name="edit_kelas_nama" readonly 
                       style="background-color: #f5f5f5; cursor: not-allowed;">
          
                <input type="hidden" id="edit_jurusan_nama" name="edit_jurusan_nama" readonly 
                       style="background-color: #f5f5f5; cursor: not-allowed;">
          
                <input type="hidden" id="edit_mata_pelajaran_id" name="edit_mata_pelajaran_id" readonly 
                       style="background-color: #f5f5f5; cursor: not-allowed;">
         
     
                <label for="edit_tanggal">Tanggal:</label>
                <input type="date" id="edit_tanggal" name="edit_tanggal" required>
        
            
       
                <label for="edit_jam_mulai">Jam Mulai:</label>
                <input type="time" id="edit_jam_mulai" name="edit_jam_mulai" required>
       
            
          
                <label for="edit_jam_selesai">Jam Selesai:</label>
                <input type="time" id="edit_jam_selesai" name="edit_jam_selesai" required>
  

        
                <!-- PERBAIKAN: Nama button submit -->
                <button type="submit" name="edit_jadwal" class="btn btn-primary">Update Jadwal</button>
                <button type="button" onclick="closeModal('editJadwalUjianModal')" class="btn btn-secondary">Batal</button>
        </form>
    </div>
</div>
        <script src="../assets/js/scripts.js"></script>

        
           

    </body>
    </html>