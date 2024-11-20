<?php
// include("db_connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar Energy Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }

        .container {
            width: 85%;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Layout for charts */
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .chart-box {
            flex: 1;
            min-width: 300px;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        /* Total KWh Box */
        .total-kwh-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            text-align: center;
        }

        .total-kwh-box h3 {
            color: #333;
        }

        #totalKwhValue {
            color: #e91e63;
            font-weight: bold;
            font-size: 24px;
        }

        /* Responsive Styles */
        @media screen and (max-width: 768px) {
            .charts-container {
                flex-direction: column;
                align-items: center;
            }

            .chart-box {
                width: 100%;
            }
        }

        /* Canvas element responsiveness */
        canvas {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>Solar Energy Dashboard</h1>
    <div>
            <form method="get" action="">
                <label for="start_date">Start Date: </label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>" required>
                <label for="end_date">End Date: </label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>" required>
                <button type="submit">Apply Date Range</button>
            </form>
        </div>

    <div class="container">

        <!-- Solar Voltage and Current Chart -->
        <div class="chart-box">
            <h3>Solar Voltage & Current</h3>
            <canvas id="solarVoltageCurrentChart"></canvas>
        </div>

        <!-- Grid Voltage and Current Chart -->
        <div class="chart-box">
            <h3>Grid Voltage & Current</h3>
            <canvas id="gridVoltageCurrentChart"></canvas>
        </div>

        <!-- Charts Container for Solar and Grid Power -->
        <div class="charts-container">
            <!-- Solar Power Chart -->
            <div class="chart-box">
                <h3>Solar Power (W)</h3>
                <canvas id="solarPowerChart"></canvas>
            </div>

            <!-- Grid Power Chart -->
            <div class="chart-box">
                <h3>Grid Power (W)</h3>
                <canvas id="gridPowerChart"></canvas>
            </div>
        </div>

        <!-- Daily KWh Chart -->
        <div class="chart-box">
            <h3>Daily KWh</h3>
            <canvas id="dailyKwhChart"></canvas>
        </div>

        <!-- Total KWh Box -->
        <div class="total-kwh-box">
            <h3>Total KWh: <span id="totalKwhValue">
                <?php 
                if (!empty($total_kwh) && is_array($total_kwh)) {
                    echo end($total_kwh);
                } else {
                    echo "N/A";
                }
                ?>
            </span></h3>
        </div>

    </div>

    <script>
        // Solar Power Chart
        var ctx1 = document.getElementById('solarPowerChart').getContext('2d');
        var solarPowerChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Solar Power (W)',
                    data: <?php echo json_encode($solar_power); ?>,
                    backgroundColor: 'rgba(0, 255, 0, 0.2)',
                    borderColor: 'rgba(0, 255, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                }
            }
        });

        // Grid Power Chart
        var ctx2 = document.getElementById('gridPowerChart').getContext('2d');
        var gridPowerChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Grid Power (W)',
                    data: <?php echo json_encode($grid_power); ?>,
                    backgroundColor: 'rgba(255, 205, 76, 0.4)',
                    borderColor: 'rgba(25, 20, 255, 0.8)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                }
            }
        });

        // Daily KWh Chart
        var ctx3 = document.getElementById('dailyKwhChart').getContext('2d');
        var dailyKwhChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Daily KWh',
                    data: <?php echo json_encode($daily_kwh); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.4)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            }
        });

        // Solar Voltage and Current Chart
        var ctx4 = document.getElementById('solarVoltageCurrentChart').getContext('2d');
        var solarVoltageCurrentChart = new Chart(ctx4, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Solar Voltage (V)',
                    data: <?php echo json_encode($solar_voltage); ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.4)',
                    fill: false,
                    borderWidth: 2
                },
                {
                    label: 'Solar Current (A)',
                    data: <?php echo json_encode($solar_current); ?>,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.4)',
                    fill: false,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true, position: 'left', ticks: { beginAtZero: true }},
                    y2: { position: 'right', grid: { drawOnChartArea: false }, ticks: { beginAtZero: true }}
                },
                plugins: {
                    tooltip: { mode: 'index', intersect: false },
                    legend: { display: true }
                }
            }
        });

        // Grid Voltage and Current Chart
        var ctx5 = document.getElementById('gridVoltageCurrentChart').getContext('2d');
        var gridVoltageCurrentChart = new Chart(ctx5, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Grid Voltage (V)',
                    data: <?php echo json_encode($grid_voltage); ?>,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.4)',
                    fill: false,
                    borderWidth: 2
                },
                {
                    label: 'Grid Current (A)',
                    data: <?php echo json_encode($grid_current); ?>,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.4)',
                    fill: false,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true, position: 'left', ticks: { beginAtZero: true }},
                    y2: { position: 'right', grid: { drawOnChartArea: false }, ticks: { beginAtZero: true }}
                },
                plugins: {
                    tooltip: { mode: 'index', intersect: false },
                    legend: { display: true }
                }
            }
        });
    </script>

</body>
</html>



