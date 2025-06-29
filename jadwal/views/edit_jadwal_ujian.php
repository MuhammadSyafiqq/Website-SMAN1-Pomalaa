<?php
session_start();

require_once '../config/database.php';
require_once '../models/JadwalUjianModel.php';
require_once '../models/KelasModel.php';
require_once '../models/JurusanModel.php';
require_once '../models/MataPelajaranModel.php';

// Instantiate models
$jadwalUjianModel = new JadwalUjian($connection);
$kelasModel = new Kelas($connection);
$jurusanModel = new Jurusan($connection);
$mataPelajaranModel = new MataPelajaran($connection);

$message = '';
// Handle form submission for edit jadwal ujian
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_jadwal_ujian'])) {
    $edit_id = $_POST['edit_id'] ?? '';
    $kelas_id = $_POST['edit_kelas_id'] ?? '';
    $jurusan_id = $_POST['edit_jurusan_id'] ?? '';
    $mata_pelajaran_id = $_POST['edit_mata_pelajaran_id'] ?? '';
    $date = $_POST['edit_date'] ?? '';
    $hari = date('l', strtotime($date));
    $jam_mulai = $_POST['edit_jam_mulai'] ?? '';
    $jam_selesai = $_POST['edit_jam_selesai'] ?? '';

    if ($edit_id && $kelas_id && $jurusan_id && $mata_pelajaran_id && $date && $jam_mulai && $jam_selesai) {
        // Validate with model's method
        $data = [
            'kelas_id' => $kelas_id,
            'jurusan_id' => $jurusan_id,
            'mata_pelajaran_id' => $mata_pelajaran_id,
            'date' => $date,
            'hari' => $hari,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai
        ];

        $validationErrors = $jadwalUjianModel->validate($data, $edit_id);
        if (!empty($validationErrors)) {
            $_SESSION['message'] = "Gagal mengupdate jadwal ujian: " . implode(' ', $validationErrors);
        } else {
            $success = $jadwalUjianModel->update($edit_id, $data);
            if ($success) {
                $_SESSION['message'] = "Jadwal Ujian berhasil diupdate.";
            } else {
                $_SESSION['message'] = "Gagal mengupdate jadwal ujian.";
            }
        }
    } else {
        $_SESSION['message'] = "Data tidak lengkap.";
    }
    header("Location: index.php");
    exit;
}

// Load jadwal ujian data to edit based on GET id
$jadwalToEdit = null;
if (isset($_GET['id'])) {
    $listing = $jadwalUjianModel->getAll();
    foreach ($listing as $item) {
        if ($item['id'] === $_GET['id']) {
            $jadwalToEdit = $item;
            break;
        }
    }
}

if (!$jadwalToEdit) {
    $_SESSION['message'] = "Jadwal Ujian tidak ditemukan.";
    header("Location: index.php");
    exit;
}

// Load related data lists for select dropdowns
$kelas_list = $kelasModel->getAll();
$jurusan_list = $jurusanModel->getAll();
$mata_pelajaran_list = $mataPelajaranModel->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Jadwal Ujian</title>
    <link rel="stylesheet" href="../assets/css/styles.css" />
</head>
<body>
    <div class="container">
        <h1>Edit Jadwal Ujian</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <p class="message <?php echo (strpos($_SESSION['message'], 'Gagal') !== false) ? 'error' : ''; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($jadwalToEdit['id']); ?>">

            <label for="edit_ju_kelas">Kelas:</label>
            <select name="edit_kelas_id" id="edit_ju_kelas" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_list as $k): ?>
                    <option value="<?php echo htmlspecialchars($k['id']); ?>" <?php echo ($k['id'] === $jadwalToEdit['kelas_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($k['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="edit_ju_jurusan">Jurusan:</label>
            <select name="edit_jurusan_id" id="edit_ju_jurusan" required>
                <option value="">-- Pilih Jurusan --</option>
                <?php foreach ($jurusan_list as $j): ?>
                    <option value="<?php echo htmlspecialchars($j['id']); ?>" <?php echo ($j['id'] === $jadwalToEdit['jurusan_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($j['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="edit_ju_mata_pelajaran">Mata Pelajaran:</label>
            <select name="edit_mata_pelajaran_id" id="edit_ju_mata_pelajaran" required>
                <option value="">-- Pilih Mata Pelajaran --</option>
                <?php foreach ($mata_pelajaran_list as $mp): ?>
                    <option value="<?php echo htmlspecialchars($mp['id']); ?>" <?php echo ($mp['id'] === $jadwalToEdit['mata_pelajaran_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($mp['nama'] . " (" . $mp['kategori'] . ")"); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="edit_ju_date">Tanggal:</label>
            <input type="date" name="edit_date" id="edit_ju_date" required value="<?php echo htmlspecialchars($jadwalToEdit['date']); ?>" />

            <label for="edit_ju_jam_mulai">Jam Mulai:</label>
            <input type="time" name="edit_jam_mulai" id="edit_ju_jam_mulai" required value="<?php echo htmlspecialchars($jadwalToEdit['jam_mulai']); ?>" />

            <label for="edit_ju_jam_selesai">Jam Selesai:</label>
            <input type="time" name="edit_jam_selesai" id="edit_ju_jam_selesai" required value="<?php echo htmlspecialchars($jadwalToEdit['jam_selesai']); ?>" />

            <button type="submit" name="edit_jadwal_ujian">Update Jadwal Ujian</button>
            <a href="index.php" style="margin-left: 1rem;">Batal</a>
        </form>
    </div>
</body>
</html>
