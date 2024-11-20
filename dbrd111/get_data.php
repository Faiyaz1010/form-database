<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "solar2_energy2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT date, energy_2 FROM data_solar";
$result = $conn->query($sql);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

// Return data as JSON
echo json_encode($data);
?>


