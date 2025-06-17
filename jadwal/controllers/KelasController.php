<?php
require_once '../config/database.php';
require_once '../models/Kelas.php';

$message = "";

// Handle kelas insertion
if (isset($_POST['add_kelas'])) {
    $kelas_nama = strtoupper(trim($_POST['kelas_nama']));
    if ($kelas_nama) {
        // Generate ID otomatis untuk kelas
        $new_id = generateNextId($connection, 'kelas', 'KL-');
        
        $stmt = $connection->prepare("INSERT INTO kelas (id, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_id, $kelas_nama);
        if ($stmt->execute()) {
            $message = "Kelas berhasil ditambahkan dengan ID: $new_id";
        } else {
            $message = "Gagal menambahkan kelas: " . $connection->error;
        }
        $stmt->close();
    } else {
        $message = "Nama kelas harus diisi.";
    }
    // Redirect to prevent form resubmission
    redirectWithMessage($message);
}

// Handle kelas editing
if (isset($_POST['edit_kelas'])) {
    $id = $_POST['edit_id'];
    $nama = strtoupper(trim($_POST['edit_nama']));
    
    $stmt = $connection->prepare("UPDATE kelas SET nama = ? WHERE id = ?");
    $stmt->bind_param("ss", $nama, $id); // Change to "ss" for VARCHAR
    
    if ($stmt->execute()) {
        $message = "Kelas berhasil diupdate.";
    } else {
        $message = "Gagal mengupdate kelas: " . $connection->error;
    }
    $stmt->close();
    redirectWithMessage($message);
}

// Handle kelas deletion
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
