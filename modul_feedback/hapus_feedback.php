<?php
require_once('../koneksi.php');
session_start();
require_once '../theme.php';
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");

$id = intval($_GET['id']);
$connection->query("DELETE FROM feedback WHERE id = $id");

header("Location: admin_feedback.php");
?>
