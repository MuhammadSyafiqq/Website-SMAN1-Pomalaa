<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MataPelajaranModel.php';
require_once __DIR__ . '/../helpers/functions.php';

$model = new MataPelajaranModel($connection);

if (isset($_POST['add_mapel'])) {
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

if (isset($_POST['edit_mapel'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));
    $kategori = $_POST['edit_kategori'];
    $model->update($id, $nama, $kategori);
    redirectWithMessage("Mata pelajaran berhasil diperbarui.");
}

if (isset($_GET['delete'])) {
    $model->delete($_GET['delete']);
    redirectWithMessage("Mata pelajaran berhasil dihapus.");
}
if (isset($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
    $data = $model->getByKategori($kategori);
} else {
    $data = $model->getAll();
}