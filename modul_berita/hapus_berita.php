<?php
require_once '../config/database.php';
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';

// Pastikan parameter ID valid
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM berita WHERE id_berita = $id";

    if ($connection->query($query)) {
        // Berhasil dihapus, redirect dengan notifikasi
        header("Location: admin_berita.php?deleted=1");
        exit();
    } else {
        // Jika gagal menghapus
        echo "Gagal menghapus berita: " . $connection->error;
    }
} else {
    // Jika ID tidak diberikan, kembali ke halaman berita
    header("Location: admin_berita.php");
    exit();
}
?>
