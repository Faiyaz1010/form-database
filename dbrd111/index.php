<?php
include("solar2_energy2");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar Energy Bar Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Solar Energy Generation</h1>
    <canvas id="solarEnergyChart" width="400" height="200"></canvas>

    <script>
        // Fetch data from PHP backend
        $.get('get_data.php', function(data) {
            var chartData = JSON.parse(data);
            
            // Prepare data for chart
            var dates = [];
            var energy = [];

            chartData.forEach(function(item) {
                dates.push(item.date);
                energy.push(item.energy_generated);
            });

            // Create the chart
            var ctx = document.getElementById('solarEnergyChart').getContext('2d');
            var solarEnergyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: dates, // Dates as x-axis labels
                    datasets: [{
                        label: 'Energy Generated (kWh)',
                        data: energy, // Energy generated as y-axis data
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
