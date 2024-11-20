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


//..........
function saveData(day, solar, batteryCharge, batteryDischarge, load, grid) {
    $.ajax({
        url: 'http://localhost:3000/api/energy',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            day: day,
            solar_energy: solar,
            battery_charge: batteryCharge,
            battery_discharge: batteryDischarge,
            load: load,
            net_grid_energy: grid
        }),
        success: function(response) {
            console.log('Data saved:', response);
        },
        error: function(err) {
            console.error('Error saving data:', err);
        }
    });
}

// Example of saving data after chart initialization
$(document).ready(function() {
    // Your existing chart code...

    // After the charts are created
    for (let i = 0; i < days.length; i++) {
        saveData(days[i], solarData[i], batteryData[i], dischargeData[i], loadData[i], gridData[i]);
    }
});

