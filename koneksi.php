<?php
$connection = new mysqli("localhost", "root", "", "db_sman1pomalaa");
if ($connection->connect_error) die("Connection failed: " . $connection->connect_error);
?>