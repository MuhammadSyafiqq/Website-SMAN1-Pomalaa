<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $tampil = isset($_POST['tampil']) && $_POST['tampil'] === '1' ? 1 : 0;

    $stmt = $connection->prepare("UPDATE feedback SET tampil = ? WHERE id = ?");
    if (!$stmt) {
        header("Location: admin_feedback.php?error=db_prepare_failed");
        exit();
    }

    $stmt->bind_param("ii", $tampil, $id);
    if ($stmt->execute()) {
        header("Location: admin_feedback.php?success=toggle");
    } else {
        header("Location: admin_feedback.php?error=db_execute_failed");
    }

    $stmt->close();
    $connection->close();
} else {
    header("Location: admin_feedback.php?error=invalid_request");
    exit();
}
?>
