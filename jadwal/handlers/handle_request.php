<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../models/JurusanModel.php';
require_once __DIR__ . '/../models/MataPelajaranModel.php';
require_once __DIR__ . '/../models/JadwalUjianModel.php';

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

if (isset($_GET['delete_kelas'])) {
    $id = $_GET['delete_kelas'];

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
        if ($jurusanModel->add($id, $nama)) {
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
    $nama = strtoupper(trim($_POST['nama']));
    $kategori = $_POST['kategori'];
    if ($nama && $kategori) {
        $id = generateNextId($connection, 'mata_pelajaran', 'MP-');
        $model->create($id, $nama, $kategori);
        redirectWithMessage("Mata pelajaran berhasil ditambahkan.");
    } else {
        redirectWithMessage("Semua field harus diisi.");
    }
}

// Edit Mata Pelajaran
if (isset($_POST['edit_mata_pelajaran'])) {
    $id = $_POST['edit_id'] ?? '';
    $nama = strtoupper(trim($_POST['edit_nama'] ?? ''));
    $kategori = $_POST['edit_kategori'] ?? '';

    if (!$id || !$nama || !$kategori) {
        $_SESSION['error'] = "Data mata pelajaran tidak valid.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
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
    $id = generateNextId($connection, 'jadwal_ujian', 'JD-');
    $kelas_id = $_POST['kelas_id'];
    $jurusan_id = $_POST['jurusan_id'];
    $mapel_id = $_POST['mapel_id'];
    $tanggal = $_POST['tanggal'];
    $hari = date('l', strtotime($tanggal)); // otomatis ambil nama hari
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    $jadwalModel->create($id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai);
    redirectWithMessage("Jadwal ujian berhasil ditambahkan.");
}
if (isset($_POST['edit_jadwal'])) {
    $id = $_POST['edit_id'];
    $kelas_id = $_POST['edit_kelas_id'];
    $jurusan_id = $_POST['edit_jurusan_id'];
    $mapel_id = $_POST['edit_mapel_id'];
    $tanggal = $_POST['edit_tanggal'];
    $hari = date('l', strtotime($tanggal));
    $jam_mulai = $_POST['edit_jam_mulai'];
    $jam_selesai = $_POST['edit_jam_selesai'];

    $jadwalModel->update($id, $kelas_id, $jurusan_id, $mapel_id, $tanggal, $hari, $jam_mulai, $jam_selesai);
    redirectWithMessage("Jadwal ujian berhasil diupdate.");
}
if (isset($_GET['delete_jadwal'])) {
    $jadwalModel->delete($_GET['delete_jadwal']);
    redirectWithMessage("Jadwal ujian berhasil dihapus.");
}

