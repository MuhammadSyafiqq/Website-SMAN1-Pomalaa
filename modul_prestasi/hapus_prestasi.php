<?php
require_once('../koneksi.php');
session_start();
require_once '../theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

if (!isset($_GET['id'])) {
    header("Location: admin_prestasi.php");
    exit();
}

$id = (int) $_GET['id'];
$connection->query("DELETE FROM prestasi WHERE id_prestasi = $id");
header("Location: admin_prestasi.php");
exit();
?>
