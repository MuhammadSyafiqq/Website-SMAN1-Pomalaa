<?php
require_once '../config/database.php';
session_start();
require_once '../theme.php';


// Cek dan sanitasi ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus data feedback
    $stmt = $connection->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect dengan notifikasi
header("Location: admin_feedback.php?success=delete");
exit();
?>
