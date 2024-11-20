<!-- 
<center id="scanner">Scanning....</center>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $("#scanner").css("display", "block");
    $.post("index1.php", {}, function(data, result){
        $("#scanner").css("display", "red");
        var imp_arr = Object.values(data);
         //console.log(data.MHPP_NIW0JET1);
        // console.log(imp_arr[0]);
        console.log(imp_arr[1]);
    }); 
</script> -->



























<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>localhost/analysis/qi.php</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 7px solid #3498db;
            border-top: 7px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #scanner {
            display: none; 
        }
    </style>
</head>
<body>
    <div class="spinner" id="spinner"></div>
    <center id="scanner">Scanning....</center>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
        
            $("#spinner").show();
            $("#scanner").css("display", "block");
            $.post("index1.php", {}, function(data, result) {
                $("#spinner").show();
                $("#scanner").css("display","red");
                var imp_arr = Object.values(data);
                console.log(imp_arr[1]);
                // console.log([0]);
            
            });
        });
</script>
</body>
</html>

