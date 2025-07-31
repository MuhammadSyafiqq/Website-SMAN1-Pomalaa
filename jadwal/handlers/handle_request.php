<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




require_once(__DIR__ . '/../../config/database.php');
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/JurusanModel.php';
require_once __DIR__ . '/../models/MataPelajaranModel.php';
require_once __DIR__ . '/../models/JadwalUjianModel.php';

require_once __DIR__ . '/../controllers/JadwalUjianController.php';
require_once __DIR__ . '/../controllers/MataPelajaranController.php';

$kelasModel = new KelasModel($connection);
$jurusanModel = new JurusanModel($connection);
$mataPelajaranModel = new MataPelajaranModel($connection);
$jadwalModel = new JadwalUjianModel($connection);



// ========== KELAS ==========
if (isset($_POST['add_kelas'])) {
    $nama = strtoupper(trim($_POST['kelas_nama']));
    if (!empty($nama)) {
        $id = generateNextId($connection, 'kelas', 'KL-');
        if ($kelasModel->create($id, $nama)) {
            $_SESSION['success'] = "Kelas berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan kelas.";
        }
    } else {
        $_SESSION['error'] = "Nama kelas tidak boleh kosong.";
    }

    // Kembali ke halaman sebelumnya
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['edit_kelas'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));

    if (!empty($id) && !empty($nama)) {
        if ($kelasModel->update($id, $nama)) {
            $_SESSION['success'] = "Kelas berhasil diperbarui.";
        } else {
            $_SESSION['error'] = "Gagal memperbarui kelas.";
        }
    } else {
        $_SESSION['error'] = "ID dan Nama kelas tidak boleh kosong.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_GET['delete']) && $_GET['table'] === 'kelas' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (!empty($id)) {
        if ($kelasModel->delete($id)) {
            $_SESSION['success'] = "Kelas berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus kelas.";
        }
    } else {
        $_SESSION['error'] = "ID kelas tidak valid.";
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}


// ========== JURUSAN ==========
if (isset($_POST['add_jurusan'])) {
    $nama = strtoupper(trim($_POST['jurusan_nama']));
    if (!empty($nama)) {
        $id = generateNextId($connection, 'jurusan', 'JR-');
        if ($jurusanModel->create($id, $nama)) {
            $_SESSION['success'] = "Jurusan berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan jurusan.";
        }
    } else {
        $_SESSION['error'] = "Nama jurusan tidak boleh kosong.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}   

if (isset($_POST['edit_jurusan'])) {
    $id = $_POST['edit_id'] ?? '';
    $nama = strtoupper(trim($_POST['edit_nama'] ?? ''));

    if (!$id || !$nama) {
        $_SESSION['error'] = "Jurusan invalid.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    if ($jurusanModel->update($id, $nama)) {
        $_SESSION['success'] = "Jurusan berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui jurusan.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_GET['delete']) && $_GET['table'] === 'jurusan' && isset($_GET['id'])) {
    if ($jurusanModel->delete($_GET['id'])) {
        $_SESSION['success'] = "Jurusan berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus jurusan.";
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_POST['add_mata_pelajaran'])) {
    $nama = strtoupper(trim($_POST['nama'])); // FIXED
    $kategori = $_POST['kategori'];           // FIXED

    if (!empty($nama) && !empty($kategori)) {
        if (!isValidNamaPelajaran($nama)) {
            $_SESSION['error'] = "Nama mata pelajaran hanya boleh berisi huruf dan spasi.";
            exit;
        }

        if ($mataPelajaranModel->isDuplicate($nama, $kategori)) {
            $_SESSION['error'] = "Mata pelajaran dengan nama dan kategori yang sama sudah ada.";
            exit;
        }

        $id = generateNextId($connection, 'mata_pelajaran', 'MP-');
        $mataPelajaranModel->create($id, $nama, $kategori);
        $_SESSION['success'] = "Mata pelajaran berhasil ditambahkan.";
    } else {
        $_SESSION['error'] = "Semua field harus diisi.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}


// Edit Mata Pelajaran
if (isset($_POST['edit_mata_pelajaran'])) {
    $id = $_POST['edit_id'] ?? '';
    $nama = strtoupper(trim($_POST['edit_nama'] ?? ''));
    $kategori = strtoupper(trim($_POST['edit_kategori'] ?? ''));
    if (!$id || !$nama || !$kategori) {
        $_SESSION['error'] = "Data mata pelajaran tidak valid.";
    }

    if (!isValidNamaPelajaran($nama)) {
        $_SESSION['error'] = "Nama mata pelajaran hanya boleh berisi huruf dan spasi.";
    }

    if ($mataPelajaranModel->isDuplicate($nama, $kategori, $id)) {
        $_SESSION['error'] = "Mata pelajaran dengan nama dan kategori yang sama sudah ada.";
    }

    if ($mataPelajaranModel->update($id, $nama, $kategori)) {
        $_SESSION['success'] = "Mata pelajaran berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui mata pelajaran.";
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isset($_GET['delete']) && $_GET['table'] === 'mata_pelajaran' && isset($_GET['id'])) {
    if ($mataPelajaranModel->delete($_GET['id'])) {
        $_SESSION['success'] = "Mata pelajaran berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus mata pelajaran.";
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
// Hapus Mata Pelajaran


// Ambil data (bisa berdasarkan kategori)
if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
    $data = $mataPelajaranModel->getByKategori($kategori);
} else {
    $data = $mataPelajaranModel->getAll();
}

// ========== JADWAL UJIAN ==========

if (isset($_POST['add_jadwal'])) {
    $kelas_id = trim($_POST['kelas_id']);
    $jurusan_id = trim($_POST['jurusan_id']);
    $mata_pelajaran_id = trim($_POST['mata_pelajaran_id']);
    $tanggal = trim($_POST['tanggal']);
    $jam_mulai = trim($_POST['jam_mulai']);
    $jam_selesai = trim($_POST['jam_selesai']);
    
    // Initialize error flag
    $hasError = false;
    
    // Validasi field kosong
    if (empty($kelas_id) || empty($jurusan_id) || empty($mata_pelajaran_id) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
        $_SESSION['error'] = "Semua field jadwal ujian harus diisi.";
        $hasError = true;
    }
    
    // Validasi tahun (hanya jika belum ada error)
    if (!$hasError) {
        $tahun = (int)date('Y', strtotime($tanggal));
        if ($tahun < 2000 || $tahun > 2100) {
            $_SESSION['error'] = "Tahun pada tanggal tidak valid";
            $hasError = true;
        }
    }
    
    // Validasi urutan waktu (hanya jika belum ada error)
    if (!$hasError) {
        if (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
            $_SESSION['error'] = "Jam Mulai harus lebih awal dari jam selesai.";
            $hasError = true;
        }
    }
    
    // Validasi durasi (hanya jika belum ada error)
    if (!$hasError) {
        $durasi_menit = (strtotime($jam_selesai) - strtotime($jam_mulai)) / 60;
        
        // Validasi durasi minimal
        if ($durasi_menit < 30) {
            $_SESSION['error'] = "Durasi ujian minimal adalah 30 menit.";
            $hasError = true;
        }
        
        // Validasi durasi maksimal (maksimal 4 jam = 240 menit)
        if (!$hasError && $durasi_menit > 240) {
            $_SESSION['error'] = "Durasi ujian maksimal adalah 4 jam.";
            $hasError = true;
        }
    }
    
    // Hanya lanjutkan jika tidak ada error
    if (!$hasError) {
        $id = generateNextId($connection, 'jadwal_ujian', 'JU-');
        
        // Generate hari otomatis dari tanggal
        $hari = date('l', strtotime($tanggal));
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $hari = $hariIndonesia[$hari];
        
        // Coba tambahkan ke database
        if ($jadwalModel->add($id, $kelas_id, $jurusan_id, $mata_pelajaran_id, $tanggal, $hari, $jam_mulai, $jam_selesai)) {
            $_SESSION['success'] = "Jadwal ujian berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan jadwal ujian.";
        }
    }
    
    // Redirect kembali ke halaman sebelumnya
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}


if (isset($_POST['edit_jadwal'])) {
    $id = $_POST['edit_id'];
    $tanggal = $_POST['edit_tanggal'];
    $jam_mulai = $_POST['edit_jam_mulai'];
    $jam_selesai = $_POST['edit_jam_selesai'];

    // Validasi input kosong
    if (empty($id) || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
        $_SESSION['error'] = "Semua field harus diisi.";

    }

    // Validasi tahun
    $tahun = (int)date('Y', strtotime($tanggal));
    if ($tahun < 2000 || $tahun > 2100) {
        $_SESSION['error'] = "Tahun pada tanggal tidak valid. Gunakan tahun antara 2000 hingga 2100.";
    }

    // Validasi urutan waktu
    if (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
        $_SESSION['error'] = "Jam mulai harus lebih awal dari jam selesai.";
    }

    // Validasi durasi minimal
    $durasi_menit = (strtotime($jam_selesai) - strtotime($jam_mulai)) / 60;
    if ($durasi_menit < 30) {
        $_SESSION['error'] = "Durasi ujian minimal adalah 30 menit.";
    }

    // Validasi durasi maksimal (maks 4 jam = 240 menit)
    if ($durasi_menit > 240) {
        $_SESSION['error'] = "Durasi ujian maksimal adalah 4 jam.";

    }

    // Hitung hari dari tanggal
    $hariList = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $dayOfWeek = date('w', strtotime($tanggal));
    $hari = $hariList[$dayOfWeek];

    // Simpan ke database
    if ($jadwalModel->update($id, $tanggal, $hari, $jam_mulai, $jam_selesai)) {
        $_SESSION['success'] = "Jadwal ujian berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui jadwal ujian.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}


// Handler untuk mendapatkan data jadwal via AJAX (untuk mengisi form edit)
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_jadwal' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    
    try {
        $id = $_GET['id'];
        
        if (empty($id)) {
            echo json_encode(['error' => 'ID jadwal tidak valid']);
            exit;
        }
        
        $jadwal = $jadwalModel->getById($id);
        
        if (!$jadwal) {
            echo json_encode(['error' => 'Data jadwal tidak ditemukan']);
            exit;
        }
        
        echo json_encode($jadwal);
        exit;
        
    } catch (Exception $e) {
        error_log("Error get_jadwal: " . $e->getMessage());
        echo json_encode(['error' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
        exit;
    }
}

if (isset($_GET['delete']) && $_GET['table'] === 'jadwal' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (!empty($id)) {
        if ($jadwalModel->delete($id)) {
            $_SESSION['success'] = "Jadwal ujian berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus jadwal ujian.";
        }
    } else {
        $_SESSION['error'] = "ID jadwal tidak valid.";
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// ========== FILTER MATA PELAJARAN ==========
// Filter jadwal dengan grouping yang lebih baik
if (isset($_GET['ajax']) && $_GET['ajax'] === 'filter_jadwal_grouped') {
    header('Content-Type: application/json');
    
    try {
        $kelas_id = $_GET['kelas_id'] ?? '';
        $jurusan_id = $_GET['jurusan_id'] ?? '';
        
        if (empty($kelas_id) || empty($jurusan_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Kelas dan Jurusan harus dipilih'
            ]);
            exit;
        }
        
        $result = $jadwalModel->getByKelasJurusan($kelas_id, $jurusan_id);
        $jadwalData = [];
        
        while ($row = $result->fetch_assoc()) {
            $jadwalData[] = $row;
        }
        
        // Sort berdasarkan tanggal dan jam
        usort($jadwalData, function($a, $b) {
            $dateCompare = strtotime($a['tanggal']) - strtotime($b['tanggal']);
            if ($dateCompare === 0) {
                return strtotime($a['jam_mulai']) - strtotime($b['jam_mulai']);
            }
            return $dateCompare;
        });
        
        echo json_encode([
            'success' => true,
            'data' => $jadwalData,
            'total' => count($jadwalData)
        ]);
        
    } catch (Exception $e) {
        error_log("Filter jadwal error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem'
        ]);
    }
    exit;
}

// Filter mata pelajaran berdasarkan kelas dan jurusan
if (isset($_GET['ajax']) && $_GET['ajax'] === 'filter_mata_pelajaran') {
    header('Content-Type: application/json');

    $kelasId = $_GET['kelas_id'] ?? '';
    $jurusanId = $_GET['jurusan_id'] ?? '';

    if ($kelasId && $jurusanId) {
        $mataPelajaranModel = new MataPelajaranModel($connection);
        $filtered = $mataPelajaranModel->getFilteredMataPelajaran($kelasId, $jurusanId);
        echo json_encode($filtered, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([]);
    }
    exit;
}

// Get semua jadwal digroup berdasarkan kelas dan jurusan
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_all_schedules_grouped') {
    header('Content-Type: application/json');
    
    try {
        $result = $jadwalModel->getAll();
        $groupedData = [];
        
        while ($row = $result->fetch_assoc()) {
            $key = $row['kelas_nama'] . ' - ' . $row['jurusan_nama'];
            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [];
            }
            $groupedData[$key][] = $row;
        }
        
        // Sort setiap group berdasarkan tanggal dan jam
        foreach ($groupedData as &$group) {
            usort($group, function($a, $b) {
                $dateCompare = strtotime($a['tanggal']) - strtotime($b['tanggal']);
                if ($dateCompare === 0) {
                    return strtotime($a['jam_mulai']) - strtotime($b['jam_mulai']);
                }
                return $dateCompare;
            });
        }
        
        echo json_encode([
            'success' => true,
            'data' => $groupedData
        ]);
        
    } catch (Exception $e) {
        error_log("Get all schedules error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem'
        ]);
    }
    exit;
}



?>


