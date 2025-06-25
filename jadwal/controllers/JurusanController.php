<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/JurusanModel.php';
require_once __DIR__ . '/../helpers/functions.php';

$jurusanModel = new JurusanModel($connection);

// Tambah
if (isset($_POST['add_jurusan'])) {
    $nama = strtoupper(trim($_POST['jurusan_nama']));
    if ($nama) {
        $id = generateNextId($connection, 'jurusan', 'JR-');
        if ($jurusanModel->add($id, $nama)) {
            redirectWithMessage("Jurusan berhasil ditambahkan.", "../views/jurusan/index.php");
        } else {
            redirectWithMessage("Gagal menambahkan jurusan.", "../views/jurusan/index.php");
        }
    } else {
        redirectWithMessage("Nama jurusan harus diisi.", "../views/jurusan/index.php");
    }
}

// Edit
if (isset($_POST['edit_jurusan'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));

    if (!$nama) {
        redirectWithMessage("Nama jurusan tidak boleh kosong.", "../views/jurusan/index.php");
    }

    if ($jurusanModel->update($id, $nama)) {
        redirectWithMessage("Jurusan berhasil diupdate.", "../views/jurusan/index.php");
    } else {
        redirectWithMessage("Gagal update jurusan.", "../views/jurusan/index.php");
    }
}


// Hapus
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    if ($jurusanModel->delete($id)) {
        redirectWithMessage("Jurusan berhasil dihapus.", "../views/jurusan/index.php");
    } else {
        redirectWithMessage("Gagal menghapus jurusan.", "../views/jurusan/index.php");
    }
}
