<?php
$servername = "localhost"; // or your server
$username = "root";
$password = "";
$dbname = "solar_energy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT day, solar_energy, battery_charge, battery_discharge, load FROM energy_data ORDER BY day ASC";
$result = $conn->query($sql);

$data = [
    'days' => ['Days 1','Days 2','Days 3'],
    'solarData' => [4,6,8],
    'batteryData' => [9,4,7],
    'dischargeData' => [-4,-5,-6],
    'loadData' => [3,5,7],
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data['days'][] = $row['days'];
        $data['solarData'][] = $row['solar_energy'];
        $data['batteryData'][] = $row['battery_charge'];
        $data['dischargeData'][] = $row['battery_discharge'];
        $data['loadData'][] = $row['load'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);

//..................next.....................
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "solar_energy_db";

// // Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// // Assume data is sent via POST request
// $solar_energy = $_POST['solar_energy'];
// $battery_charge = $_POST['battery_charge'];
// $battery_discharge = $_POST['battery_discharge'];
// $load = $_POST['load'];
// $day = $_POST['day'];

// $sql = "INSERT INTO energy_data (day, solar_energy, battery_charge, battery_discharge, load)
// VALUES ('$days', '$solar_energy', '$battery_charge', '$battery_discharge', '$load')";

// if ($conn->query($sql) === TRUE) {
//     echo "New record created successfully";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }

// $conn->close();
?>

