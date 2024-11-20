<?php


$dbhost = 'machinedatanglobal.c4sty2dpq6yv.ap-northeast-1.rds.amazonaws.com';
$dbuser = 'Nglobal_root_NIW';
$dbpass = 'Niw_Machinedata_11089694';
$dbname = "NIW_GRID_SPV_machine_data_space_NG";

// Create connection
$conn = new mysqli($dbhost,$dbuser,$dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM daily_data ORDER BY date DESC LIMIT 30"; // Last 30 days
$result = $conn->query($sql);

// Prepare data for the charts
$dates = [];
$solar_panel_v = [];
$solar_panel_i = [];
$solar_panel_p = [];
$grid_v = [];
$grid_i = [];
$grid_p = [];
$today_kwh = [];
$total_kwh = [];

if ($result->num_rows > 1) {
    while($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
        $solar_voltage[] = $row['solar_panel_v'];
        $solar_current[] = $row['solar_panel_i'];
        $solar_power[] = $row['solar_panel_p'];
        $grid_voltage[] = $row['grid_v'];
        $grid_current[] = $row['grid_i'];
        $grid_power[] = $row['grid_p'];
        $daily_kwh[] = $row['today_kwh'];
        $total_kwh[] = $row['total_kwh'];
    }
} else {
    echo "0 results";
}
$conn->close();
?>
