<?php
$servername = "machinedatanglobal.c4sty2dpq6yv.ap-northeast-1.rds.amazonaws.com";
$username = "Nglobal_root_NIW"; 
$password = "Niw_Machinedata_11089694"; 
$dbname = "NIW_GRID_SPV_machine_data_space_NG";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

$sql = "SELECT date_entry, solar_panel_v, solar_panel_i, solar_panel_p, grid_v, grid_i, grid_p, today_kwh
        FROM factory1_site1 WHERE date_entry BETWEEN '$start_date' AND '$end_date' ORDER BY date_entry ASC";

$result = $conn->query($sql);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

$dates = [];
$solar_panel_v = [];
$solar_panel_i = [];
$solar_panel_p = [];
$grid_v = [];
$grid_i = [];
$grid_p = [];
$today_kwh = [];
$data_by_date = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $date = $row['date_entry'];
        if (!isset($data_by_date[$date])) {
            $data_by_date[$date] = [
                'solar_panel_v' => 0,
                'solar_panel_i' => 0,
                'solar_panel_p' => 0,
                'grid_v' => 0,
                'grid_i' => 0,
                'grid_p' => 0,
                'today_kwh' => 0,
                'count' => 0
            ];
        }
        $data_by_date[$date]['solar_panel_v'] += $row['solar_panel_v'];
        $data_by_date[$date]['solar_panel_i'] += $row['solar_panel_i'];
        $data_by_date[$date]['solar_panel_p'] += $row['solar_panel_p'];
        $data_by_date[$date]['grid_v'] += $row['grid_v'];
        $data_by_date[$date]['grid_i'] += $row['grid_i'];
        $data_by_date[$date]['grid_p'] += $row['grid_p'];
        $data_by_date[$date]['today_kwh'] += $row['today_kwh'];
        $data_by_date[$date]['count']++;
    }
}

foreach ($data_by_date as $date => $data) {
    $dates[] = $date;
    $solar_panel_v[] = $data['solar_panel_v'] / $data['count'];
    $solar_panel_i[] = $data['solar_panel_i'] / $data['count'];
    $solar_panel_p[] = $data['solar_panel_p'] / $data['count'];
    $grid_v[] = $data['grid_v'] / $data['count'];
    $grid_i[] = $data['grid_i'] / $data['count'];
    $grid_p[] = $data['grid_p'] / $data['count'];
    $today_kwh[] = $data['today_kwh'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar Energy Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            margin: 20px 0;
            color: #2c3e50;
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 10px;
        }

        label {
            font-size: 1.1em;
            color: #2c3e50;
        }

        input[type="date"] {
            padding: 8px;
            font-size: 1em;
            border: 2px solid #3498db;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        .container {
            width: 90%;
            margin: 0 auto;
        }

        .chart-box {
            margin: 20px 0;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .table-container {
            margin: 10px 0;
            padding: 20px;
            /* background-color: #fff;
            border-radius: 10px; */
            /* box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); */
            padding: 20px;
        }

        table {
            width: 40%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
        canvas {
            width: 100% !important;
            height: 400px !important;
        }

    </style>
</head>
<body>

    <h1>Solar Energy Dashboard</h1>

    <form method="POST" action="">
        <label for="start_date">Start Date: </label>
        <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
        
        <label for="end_date">End Date: </label>
        <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">

        <input style="height: 38px;" type="submit" value="Filter Date">
    </form>

    <div class="container">
        <div class="chart-box">
            <h3>Solar Voltage & Solar Current</h3>
            <canvas id="solarVoltageCurrentChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Grid Voltage & Grid Current</h3>
            <canvas id="gridVoltageCurrentChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Solar Power (W)</h3>
            <canvas id="solarPowerChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Grid Power (W)</h3>
            <canvas id="gridPowerChart"></canvas>
        </div>

        <div class="table-container">
            <h3>Today's KWh</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Today's kwh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data_by_date as $date => $data): ?>
                        <tr>
                            <td><?php echo $date; ?></td>
                            <td><?php echo number_format($data['today_kwh'], 2); ?> kWh</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        var dates = <?php echo json_encode($dates); ?>;
        var solarPanelV = <?php echo json_encode($solar_panel_v); ?>;
        var solarPanelI = <?php echo json_encode($solar_panel_i); ?>;
        var solarPanelP = <?php echo json_encode($solar_panel_p); ?>;
        var gridV = <?php echo json_encode($grid_v); ?>;
        var gridI = <?php echo json_encode($grid_i); ?>;
        var gridP = <?php echo json_encode($grid_p); ?>;
        var todayKwh = <?php echo json_encode($today_kwh); ?>;
        const formattedDates = dates.map(date => new Date(date).toLocaleDateString());

        var ctx1 = document.getElementById('solarVoltageCurrentChart').getContext('2d');
    var solarVoltageCurrentChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: formattedDates,
            datasets: [
                {
                    label: 'Solar Voltage (V)',
                    data: solarPanelV,
                    fill: false,
                    borderColor: 'rgba(54, 255, 235, 1)',
                    tension: 0.1,
                    pointBackgroundColor: 'rgba(54, 255, 235, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 2,
                    yAxisID: 'y1'
                },
                {
                    label: 'Solar Current (A)',
                    data: solarPanelI,
                    fill: false,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    tension: 0.1, 
                    pointBackgroundColor: 'rgba(255, 159, 64, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 2,
                    yAxisID: 'y2' 
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: { text: 'Date', display: true }
                },
                y1: {
                    title: { text: 'Voltage (V)', display: true },
                    position: 'left',
                    beginAtZero: false,
                    ticks: {
                        max: Math.max(...solarPanelV) + 10,
                        min: Math.min(...solarPanelV) - 10 
                    }
                },
                y2: {
                    title: { text: 'Current (A)', display: true },
                    position: 'right',
                    beginAtZero: false,
                    ticks: {
                        max: Math.max(...solarPanelI) + 5,
                        min: Math.min(...solarPanelI) - 5 
                    }
                }
            },
            plugins: {
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: function(tooltipItem) {
                            return tooltipItem[0].label;
                        },
                        label: function(tooltipItem) {
                            var datasetLabel = tooltipItem.dataset.label || '';
                            return datasetLabel + ': ' + tooltipItem.raw.toFixed(2);
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        fontSize: 14
                    }
                }
            }
        }
    });
    var ctx2 = document.getElementById('gridVoltageCurrentChart').getContext('2d');
    var gridVoltageCurrentChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: formattedDates,
            datasets: [
                {
                    label: 'Grid Voltage (V)',
                    data: gridV,
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.1,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y1'
                },
                {
                    label: 'Grid Current (A)',
                    data: gridI,
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)', 
                    tension: 0.1, 
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y2'  
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: { text: 'Date', display: true }
                },
                y1: { 
                    title: { text: 'Voltage (V)', display: true },
                    position: 'left',
                    beginAtZero: false,
                    ticks: {
                        max: Math.max(...gridV) + 10,
                        min: Math.min(...gridV) - 10 
                    }
                },
                y2: {
                    title: { text: 'Current (A)', display: true },
                    position: 'right',
                    beginAtZero: false,
                    ticks: {
                        max: Math.max(...gridI) + 5,
                        min: Math.min(...gridI) - 5 
                    }
                }
            },
            plugins: {
                tooltip: {
                    enabled: true,
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        title: function(tooltipItem) {
                            return tooltipItem[0].label;
                        },
                        label: function(tooltipItem) {
                            var datasetLabel = tooltipItem.dataset.label || '';
                            return datasetLabel + ': ' + tooltipItem.raw.toFixed(2);
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        fontSize: 14
                    }
                }
            }
        }
    });

        var ctx3 = document.getElementById('solarPowerChart').getContext('2d');
        var solarPowerChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: formattedDates,
                datasets: [{
                    label: 'Solar Power (W)',
                    data: solarPanelP,
                    backgroundColor: 'rgba(75, 192, 192, 0.3)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { text: 'Date', display: true }},
                    y: { title: { text: 'Power (W)', display: true }}
                }
            }
        });

        var ctx4 = document.getElementById('gridPowerChart').getContext('2d');
        var gridPowerChart = new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: formattedDates,
                datasets: [{
                    label: 'Grid Power (W)',
                    data: gridP,
                    backgroundColor: 'rgba(153, 102, 255, 0.3)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { text: 'Date', display: true }},
                    y: { title: { text: 'Power (W)', display: true }}
                }
            }
        });
    </script>
</body>
</html>
