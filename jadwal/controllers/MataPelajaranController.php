<?php
require_once __DIR__ . '/../models/MataPelajaranModel.php';

<<<<<<< Updated upstream
$mataPelajaranModel = new MataPelajaranModel($connection);

if (isset($_POST['add_mata_pelajaran'])) {
    $nama = strtoupper(trim($_POST['nama']));
    $kategori = $_POST['kategori'];
    if ($nama && $kategori) {
        $id = generateNextId($connection, 'mata_pelajaran', 'MP-');
        $mataPelajaranModel->create($id, $nama, $kategori);
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

// Hapus Mata Pelajaran
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    if ($mataPelajaranModel->delete($id)) {
        redirectWithMessage("Mata pelajaran berhasil dihapus.", "../views/mata_pelajaran/index.php");
    } else {
        redirectWithMessage("Gagal menghapus mata pelajaran.", "../views/mata_pelajaran/index.php");
    }
}

// Ambil data (bisa berdasarkan kategori)
if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
    $data = $mataPelajaranModel->getByKategori($kategori);
} else {
    $data = $mataPelajaranModel->getAll();
}
if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
    $data = $mataPelajaranModel->getByKategori($kategori);
} else {
    $data = $mataPelajaranModel->getAll();
}
=======
class MataPelajaranController {
    private $model;

    public function __construct($connection) {
        $this->model = new MataPelajaranModel($connection);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function store($nama, $kategori) {
        return $this->model->tambahMataPelajaran($nama, $kategori);
    }

    public function update($id, $nama, $kategori) {
        return $this->model->updateMataPelajaran($id, $nama, $kategori);
    }

    public function delete($id) {
        return $this->model->hapusMataPelajaran($id);
    }

    public function show($id) {
        return $this->model->getById($id);
    }

    public function getByKategori($kategori) {
        return $this->model->getByKategori($kategori);
    }
}
>>>>>>> Stashed changes
