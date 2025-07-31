<?php
$servername = "localhost";
$username = "u567236312_sman1pomalaa";
$password = "7|Aup4a09";
$dbname = "u567236312_db_sman1pomala";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
