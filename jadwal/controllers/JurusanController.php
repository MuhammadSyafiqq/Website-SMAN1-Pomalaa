<?php
require_once '../config/database.php';
require_once '../models/Jurusan.php';

$message = "";

// Handle jurusan insertion
if (isset($_POST['add_jurusan'])) {
    $jurusan_nama = strtoupper(trim($_POST['jurusan_nama']));
    if ($jurusan_nama) {
        // Generate ID otomatis untuk jurusan
        $new_id = generateNextId($connection, 'jurusan', 'JR-');
        
        $stmt = $connection->prepare("INSERT INTO jurusan (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_id, $jurusan_nama);
        if ($stmt->execute()) {
            $message = "Jurusan berhasil ditambahkan dengan ID: $new_id";
        } else {
            $message = "Gagal menambahkan jurusan: " . $connection->error;
        }
        $stmt->close();
    } else {
        $message = "Nama jurusan harus diisi.";
    }
    // Redirect to prevent form resubmission
    redirectWithMessage($message);
}

// Handle jurusan editing
if (isset($_POST['edit_jurusan'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));
    
    $stmt = $connection->prepare("UPDATE jurusan SET nama = ? WHERE id = ?");
    $stmt->bind_param("ss", $nama, $id); // Change to "ss" for VARCHAR
    
    if ($stmt->execute()) {
        $message = "Jurusan berhasil diupdate.";
    } else {
        $message = "Gagal mengupdate jurusan: " . $connection->error;
    }
    $stmt->close();
    redirectWithMessage($message);
}

// Handle jurusan deletion
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
