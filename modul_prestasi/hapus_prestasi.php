<?php
require_once '../config/database.php';
session_start();
require_once '../theme.php';


// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_prestasi.php");
    exit();
}

$id = (int) $_GET['id'];

// Eksekusi DELETE
$connection->query("DELETE FROM prestasi WHERE id_prestasi = $id");

// Redirect ke admin dengan notifikasi sukses hapus
header("Location: admin_prestasi.php?success=delete");
exit();
?>
