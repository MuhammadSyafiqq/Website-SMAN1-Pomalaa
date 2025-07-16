<?php

require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../models/MataPelajaranModel.php');
require_once(__DIR__ . '/../helpers/functions.php');

$mataPelajaranModel = new MataPelajaranModel($connection);

if (isset($_POST['add_mata_pelajaran'])) {
    $nama = strtoupper(trim($_POST['nama'])); // FIXED
    $kategori = $_POST['kategori'];           // FIXED

    if (!empty($nama) && !empty($kategori)) {
        if (!isValidNamaPelajaran($nama)) {
            $_SESSION['error'] = "Nama mata pelajaran hanya boleh berisi huruf dan spasi.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if ($mataPelajaranModel->isDuplicate($nama, $kategori)) {
            $_SESSION['error'] = "Mata pelajaran dengan nama dan kategori yang sama sudah ada.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
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
    $kategori = $_POST['edit_kategori'] ?? '';

    if (!$id || !$nama || !$kategori) {
        $_SESSION['error'] = "Data mata pelajaran tidak valid.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    if (!isValidNamaPelajaran($nama)) {
        $_SESSION['error'] = "Nama mata pelajaran hanya boleh berisi huruf dan spasi.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    if ($mataPelajaranModel->isDuplicate($nama, $kategori, $id)) {
        $_SESSION['error'] = "Mata pelajaran dengan nama dan kategori yang sama sudah ada.";
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

