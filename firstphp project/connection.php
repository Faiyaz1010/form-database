<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "my_database1";


$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


?>

