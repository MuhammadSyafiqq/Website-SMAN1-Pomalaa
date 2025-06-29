<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_sman1pomalaa";

$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
