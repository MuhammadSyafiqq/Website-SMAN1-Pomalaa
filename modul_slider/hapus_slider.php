<?php
// File: hapus_slider.php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$stmt = $connection->prepare("DELETE FROM slider WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: admin_slider.php?success=delete");
    exit();
} else {
    echo "Gagal menghapus data.";
}
?>
