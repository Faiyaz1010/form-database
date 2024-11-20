<?php
include("db_connect.php");

$sql = "SELECT date, solar_energy, battery_charge, battery_discharge, load1, grid_energy FROM energy_data ORDER BY date";
$result = $conn->query($sql);

$days = [];
$solarData = [];
$batteryData = [];
$dischargeData = [];
$loadData = [];
$gridData = [];

if ($result->num_rows > 0) {
    // Process each row
    while($row = $result->fetch_assoc()) {
        $days[] = $row['date'];
        $solarData[] = $row['solar_energy'];
        $batteryData[] = $row['battery_charge'];
        $dischargeData[] = $row['battery_discharge'];
        $loadData[] = $row['load1'];
        $gridData[] = $row['grid_energy'];
    }
} else {
    echo "0 results";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar Energy and Battery Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            /* flex-direction: column; */
            background-color: #f4f4f4;
            /* margin: 60px;
            height: 100vh; */
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin-right:400px;
        }
        h1 {
            text-align: center;
            width:900px;
        }
        canvas {
            margin: 20px 0;
        }
    </style>


</head>
<body>
    <div class="container">
        <h3>Solar Energy Production and Battery Status.</h3>
        <canvas id="solarChart" width="400" height="200"></canvas>
        <canvas id="batteryChart" width="400" height="200"></canvas>
        <canvas id="loadChart" width="400" height="200"></canvas>
        <canvas id="gridChart" width="400" height="200"></canvas>
    </div>

    <script>
        $(document).ready(function() {
            const ctxSolar = document.getElementById('solarChart').getContext('2d');
            const ctxBattery = document.getElementById('batteryChart').getContext('2d');
            const ctxLoad = document.getElementById('loadChart').getContext('2d');
            const ctxGrid = document.getElementById('gridChart').getContext('2d');

            // PHP data passed to JavaScript via JSON
            const days = <?php echo json_encode($days); ?>;
            const solarData = <?php echo json_encode($solarData); ?>;
            const batteryData = <?php echo json_encode($batteryData); ?>;
            const dischargeData = <?php echo json_encode($dischargeData); ?>;
            const loadData = <?php echo json_encode($loadData); ?>;
            const gridData = solarData.map((s, index) => s - batteryData[index] + dischargeData[index]); // Net grid data (kWh)

            // Solar Chart
            const solarChart = new Chart(ctxSolar, {
                type: 'bar',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Solar Energy (kWh)',
                        data: solarData,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Energy (kWh)'
                            }
                        }
                    }
                }
            });

            // Battery Chart
            const batteryChart = new Chart(ctxBattery, {
                type: 'bar',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Battery Charge (kWh)',
                        data: batteryData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    },
                    {
                        label: 'Battery Discharge (kWh)',
                        data: dischargeData,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Energy (kWh)'
                            }
                        }
                    }
                }
            });

            // Load Chart
            const loadChart = new Chart(ctxLoad, {
                type: 'bar',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Load (kWh)',
                        data: loadData,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 140, 0, 0.6)',
                        fill: true,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Energy (kWh)'
                            }
                        }
                    }
                }
            });

            // Grid Chart
            const gridChart = new Chart(ctxGrid, {
                type: 'bar',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Net Grid Energy (kWh)',
                        data: gridData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        fill: true,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Energy (kWh)'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>

</body>
</html>