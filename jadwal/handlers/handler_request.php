<?php
require_once __DIR__ . '/../config/database.php';

// Import semua controller
require_once __DIR__ . '/../controllers/KelasController.php';
require_once __DIR__ . '/../controllers/JurusanController.php';
require_once __DIR__ . '/../controllers/MataPelajaranController.php';
require_once __DIR__ . '/../controllers/JadwalUjianController.php';

// Inisialisasi controller
$kelasController = new KelasController($connection);
$jurusanController = new JurusanController($connection);
$mapelController = new MataPelajaranController($connection);
$jadwalController = new JadwalUjianController($connection);

// Tangani request POST
require_once __DIR__ . '/../config/database.php';

// Import semua controller
require_once __DIR__ . '/../controllers/KelasController.php';
require_once __DIR__ . '/../controllers/JurusanController.php';
require_once __DIR__ . '/../controllers/MataPelajaranController.php';
require_once __DIR__ . '/../controllers/JadwalUjianController.php';

// Inisialisasi controller
$kelasController = new KelasController($connection);
$jurusanController = new JurusanController($connection);
$mataPelajaranController = new MataPelajaranController($connection);
$jadwalUjianController = new JadwalUjianController($connection);

// Tangani request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        // === KELAS ===
        case 'add_kelas':
            $nama = trim($_POST['nama']);
            $success = $kelasController->store($nama);
            break;
        case 'edit_kelas':
            $id = $_POST['id'];
            $nama = trim($_POST['nama']);
            $success = $kelasController->update($id, $nama);
            break;
        case 'delete_kelas':
            $id = $_POST['id'];
            $success = $kelasController->delete($id);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Kelas berhasil dihapus.' : 'Gagal menghapus kelas.'
            ]);
            exit;

        // === JURUSAN ===
        case 'add_jurusan':
            $nama = trim($_POST['nama']);
            $success = $jurusanController->create($nama);
            break;
        case 'edit_jurusan':
            $id = $_POST['id'];
            $nama = trim($_POST['nama']);
            $success = $jurusanController->update($id, $nama);
            break;
        case 'delete_jurusan':
            $id = $_POST['id'];
            $success = $jurusanController->delete($id);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Jurusan berhasil dihapus.' : 'Gagal menghapus jurusan.'
            ]);
            exit;

        // === MATA PELAJARAN ===
        case 'add_mapel':
            $nama = trim($_POST['nama']);
            $kategori = $_POST['kategori'];
            $success = $mataPelajaranController->create($nama, $kategori);
            break;
        case 'edit_mapel':
            $id = $_POST['id'];
            $nama = trim($_POST['nama']);
            $kategori = $_POST['kategori'];
            $success = $mataPelajaranController->update($id, $nama, $kategori);
            break;
        case 'delete_mapel':
            $id = $_POST['id'];
            $success = $mataPelajaranController->delete($id);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Mata pelajaran berhasil dihapus.' : 'Gagal menghapus mata pelajaran.'
            ]);
            exit;

        // === JADWAL UJIAN ===
        case 'add_jadwal':
            $kelas_id = $_POST['kelas_id'];
            $jurusan_id = $_POST['jurusan_id'];
            $mapel_id = $_POST['mata_pelajaran_id'];
            $tanggal = $_POST['tanggal'];
            $jam_mulai = $_POST['jam_mulai'];
            $jam_selesai = $_POST['jam_selesai'];
            $success = $jadwalUjianController->create($kelas_id, $jurusan_id, $mapel_id, $tanggal, $jam_mulai, $jam_selesai);
            break;
        case 'edit_jadwal':
            $id = $_POST['id'];
            $kelas_id = $_POST['kelas_id'];
            $jurusan_id = $_POST['jurusan_id'];
            $mapel_id = $_POST['mata_pelajaran_id'];
            $tanggal = $_POST['tanggal'];
            $jam_mulai = $_POST['jam_mulai'];
            $jam_selesai = $_POST['jam_selesai'];
            $success = $jadwalUjianController->update($id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $jam_mulai, $jam_selesai);
            break;
        case 'delete_jadwal':
            $id = $_POST['id'];
            $success = $jadwalUjianController->delete($id);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Jadwal ujian berhasil dihapus.' : 'Gagal menghapus jadwal ujian.'
            ]);
            exit;
    }

    // Jika bukan delete, redirect (untuk add/edit yang pakai form biasa)
    header("Location: ../admin-panel.php");
    exit;
}

function getHariFromTanggal($tanggal)
{
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    return $days[date('w', strtotime($tanggal))];
}


// ========== AJAX: get mata pelajaran by kategori ==========
if (isset($_GET['get_mapel_by_kategori'])) {
    $kategori = $_GET['get_mapel_by_kategori'];
    $mapel = $mapelController->getByKategori($kategori);
    header('Content-Type: application/json');
    echo json_encode($mapel);
    exit;
}

echo json_encode([
    'success' => $success,
    'message' => $success ? 'Kelas berhasil dihapus.' : 'Gagal menghapus kelas.'
  ]);
  exit;
  
?>






