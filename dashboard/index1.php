<?php
// INIT OF DATA POINTS
$dataPoints1 = array();
$dataPoints2 = array();
$dataPoints3 = array();
$dataPoints4 = array();

// db connection
$servername = "localhost"; // or your server
$username = "root";
$password = "";
$dbname = "solar1_energy1_data1";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }else{echo "";}
$sql = "SELECT * FROM my_solardata order by id asc";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $datetime_manip = strtotime($row['date']." - 8 hours")*1000;

        $solar_energy = (float)str_replace(" ", "", $row['solar_energy']);
        $battery_charge = (float)str_replace(" ", "", $row['battery_charge']);
        $battery_discharge = (float)str_replace(" ", "", $row['battery_discharge']);
        $load1 = (float)str_replace(" ", "", $row['load1']);
        array_push($dataPoints1, array("x"=>$datetime_manip, "y"=>$solar_energy));
        array_push($dataPoints2, array("x"=>$datetime_manip, "y"=>$battery_charge));
        array_push($dataPoints3, array("x"=>$datetime_manip, "y"=>$battery_discharge));
        array_push($dataPoints4, array("x"=>$datetime_manip, "y"=>$load1));
    }
}else{
    exit;
}
?>
<script>
window.onload = function () {
 
    var chart1 = new CanvasJS.Chart("chartContainer1", {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light1",
        title:{
            text: "one"
        },
        axisY:{
            includeZero: true
        },
        data: [{
                type: "area",
                xValueType: "dateTime",
                indexLabelFontColor: "#5A5757",
                xValueFormatString: "DD-MM-YYYY HH:mm TT",
                indexLabelPlacement: "outside",   
                toolTipContent: "DateTime:{x}<br>Data:{y}",
                dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
            }]
    });

    var chart2 = new CanvasJS.Chart("chartContainer2", {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light2",
        title:{
            text: "two"
        },
        axisY:{
            includeZero: true
        },
        data: [{
                type: "column",
                indexLabelFontColor: "#5B5757",
                xValueType: "dateTime",
                indexLabelPlacement: "outside",   
                dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
            }]
    });

    var chart3 = new CanvasJS.Chart("chartContainer3", {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light3",
        title:{
            text: "three"
        },
        axisY:{
            includeZero: true
        },
        data: [{
                type: "column",
                indexLabelFontColor: "#5C5757",
                xValueType: "dateTime",
                indexLabelPlacement: "outside",   
                dataPoints: <?php echo json_encode($dataPoints3, JSON_NUMERIC_CHECK); ?>
            }]
    });

    var chart4 = new CanvasJS.Chart("chartContainer4", {
        animationEnabled: true,
        exportEnabled: true,
        theme: "light4",
        title:{
            text: "four"
        },
        axisY:{
            includeZero: true
        },
        data: [{
                type: "column",
                xValueType: "dateTime",
                indexLabelFontColor: "#5D5757",
                indexLabelPlacement: "outside",   
                dataPoints: <?php echo json_encode($dataPoints4, JSON_NUMERIC_CHECK); ?>
            }]
    });

    chart1.render();
    chart2.render();
    chart3.render();
    chart4.render();
  
 }
</script>




<div id="chartContainer1" style="height: 370px; width: 100%;"></div>
<div id="chartContainer2" style="height: 370px; width: 100%;"></div>
<div id="chartContainer3" style="height: 370px; width: 100%;"></div>
<div id="chartContainer4" style="height: 370px; width: 100%;"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>