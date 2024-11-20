
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
            flex-direction: column;
            background-color: #f4f4f4;
            margin: 60px;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 450px;
            /* display: flex; */
            margin-right: 1100px;
        }
        h1 {
            text-align: center;
            width:700px;
        }
        canvas {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Solar Energy Production and Battery Status.</h1>
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

            // Sample data for solar energy (kWh) over 10 days
            const days = ['Days 1','Days 2','Days 3'];
            const solarData = [40,60,80];
            const batteryData = [25,17,30];
            const dischargeData = [-8,-1,-4];
            const loadData = [2,30,20]; // Load data (kWh) over 10 days
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
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
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
    


    $(document).ready(function() {
    const ctxSolar = document.getElementById('solarChart').getContext('2d');
    const ctxBattery = document.getElementById('batteryChart').getContext('2d');
    const ctxLoad = document.getElementById('loadChart').getContext('2d');
    const ctxGrid = document.getElementById('gridChart').getContext('2d');

    function fetchData() {
        $.ajax({
            url: 'fetch_data.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const days = data.days;
                const solarData = data.solarData;
                const batteryData = data.batteryData;
                const dischargeData = data.dischargeData;
                const loadData = data.loadData;
                const gridData = solarData.map((s, index) => s - batteryData[index] + dischargeData[index]);

                updateCharts(days, solarData, batteryData, dischargeData, loadData, gridData);
            },
            error: function() {
                console.error('Error fetching data');
            }
        });
    }

    function updateCharts(days, solarData, batteryData, dischargeData, loadData, gridData) {
        // Update Solar Chart
        solarChart.data.labels = days;
        solarChart.data.datasets[0].data = solarData;
        solarChart.update();

        // Update Battery Chart
        batteryChart.data.labels = days;
        batteryChart.data.datasets[0].data = batteryData;
        batteryChart.data.datasets[1].data = dischargeData;
        batteryChart.update();

        // Update Load Chart
        loadChart.data.labels = days;
        loadChart.data.datasets[0].data = loadData;
        loadChart.update();

        // Update Grid Chart
        gridChart.data.labels = days;
        gridChart.data.datasets[0].data = gridData;
        gridChart.update();
    } 
    // Initial fetch
    fetchData();
});
</script>
    
</body>
</html>

