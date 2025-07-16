<?php
$servername = "srv526.hstgr.io";
$username = "u567236312_manuss";
$password = "0F~tuBCg$+v";
$dbname = "u567236312_manuss";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
