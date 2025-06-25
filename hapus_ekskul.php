<?php
session_start();
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
$id = $_GET['id'];
$connection->query("DELETE FROM ekstrakurikuler WHERE id_ekskul = $id");
header("Location: admin_ekskul.php");
