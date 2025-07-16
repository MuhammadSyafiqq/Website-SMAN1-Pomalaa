<?php
require_once '../config/database.php';
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../theme.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $connection->query("DELETE FROM ekstrakurikuler WHERE id_ekskul = $id");
}

// Redirect kembali ke halaman admin dengan notifikasi sukses
header("Location: admin_ekskul.php?success=hapus");
exit();
