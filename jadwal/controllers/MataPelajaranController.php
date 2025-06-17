<?php
require_once '../config/database.php';
require_once '../models/MataPelajaran.php';

$message = "";

// Handle mata_pelajaran insertion
if (isset($_POST['add_mata_pelajaran'])) {
    $mp_nama = trim($_POST['mata_pelajaran_nama']);
    $kategori = $_POST['kategori'];
    
    if ($mp_nama && $kategori) {
        // Generate ID otomatis
        $new_id = generateNextId($connection, 'mata_pelajaran', 'MP-');
        
        $stmt = $connection->prepare("INSERT INTO mata_pelajaran (id, nama, kategori) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $new_id, $mp_nama, $kategori);
        
        if ($stmt->execute()) {
            $message = "Mata Pelajaran berhasil ditambahkan dengan ID: $new_id";
        } else {
            $message = "Gagal menambahkan Mata Pelajaran: " . $connection->error;
        }
        $stmt->close();
    } else {
        $message = "Nama mata pelajaran dan kategori harus diisi.";
    }
    // Redirect to prevent form resubmission
    redirectWithMessage($message);
}

// Handle mata_pelajaran editing
if (isset($_POST['edit_mata_pelajaran'])) {
    $id = $_POST['edit_id'];
    $nama = trim($_POST['edit_nama']);
    $kategori = $_POST['edit_kategori'];
    
    $stmt = $connection->prepare("UPDATE mata_pelajaran SET nama = ?, kategori = ? WHERE id = ?");
    $stmt->bind_param("sss", $nama, $kategori, $id);
    
    if ($stmt->execute()) {
        $message = "Mata Pelajaran berhasil diupdate.";
    } else {
        $message = "Gagal mengupdate mata pelajaran: " . $connection->error;
    }
    $stmt->close();
}

// Handle mata_pelajaran deletion
if (isset($_GET['delete'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];
    
    if (in_array($table, ['kelas', 'jurusan', 'mata_pelajaran', 'jadwal_ujian'])) {
        $stmt = $connection->prepare("DELETE FROM $table WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            $message = ucfirst($table) . " berhasil dihapus.";
        } else {
            $message = "Gagal menghapus " . $table . ": " . $connection->error;
        }
        $stmt->close();
    }
     redirectWithMessage($message);
}

// Redirect with message function
function redirectWithMessage($message) {
    session_start();
    $_SESSION['message'] = $message;
    header("Location: ../views/index.php");
    exit();
}
?>
