<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/KelasModel.php';
require_once __DIR__ . '/../helpers/functions.php';

$kelasModel = new KelasModel($connection);

// Handle Tambah Kelas
if (isset($_POST['add_kelas'])) {
    $nama = strtoupper(trim($_POST['kelas_nama']));

    if (!empty($nama)) {
        $id = generateNextId($connection, 'kelas', 'KL-');
        if ($kelasModel->create($id, $nama)) {
            redirectWithMessage("Kelas berhasil ditambahkan redirect.");
        } else {
            redirectWithMessage("Gagal menambahkan kelas.");
        }
    } else {
        redirectWithMessage("Nama kelas tidak boleh kosong.");
    }
}

// Handle Edit Kelas
if (isset($_POST['edit_kelas'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));

    if (!empty($nama)) {
        if ($kelasModel->update($id, $nama)) {
            redirectWithMessage("Kelas berhasil diupdate.");
        } else {
            redirectWithMessage("Gagal mengupdate kelas.");
        }
    } else {
        redirectWithMessage("Nama kelas tidak boleh kosong.");
    }
}

// Handle Hapus Kelas
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    if ($kelasModel->delete($id)) {
        redirectWithMessage("Kelas berhasil dihapus.");
    } else {
        redirectWithMessage("Gagal menghapus kelas.");
    }
}
