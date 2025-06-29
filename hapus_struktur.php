<?php
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $connection->prepare("DELETE FROM struktur WHERE id_struktur = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin_struktur.php");
exit();
?>
