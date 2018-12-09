<html>
	<head>
		<title>
		IKT446 MongoDB
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export ADB Mongodb</h1>			
			<a href="stat1.php">Home</a>			
		</div>
		<?php
            require 'vendor/autoload.php';		
		    
			function getYearUrlparam()
            {
                $yyyy = $_GET['year'] ?? '';				
				if($yyyy=='') 
				{
                    //set default if not provided.
                    $yyyy = 2017;
                }
                if(!is_numeric($yyyy))
                {
                    echo "<h2>Year parameter must be in integer type.</h2>";
                    $yyyy = 2017;
                }
                return (int) $yyyy;
            }		
 
			function displayData($result1, $result2, $year)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";
				echo "<h1>USD - NOK exchange rate</h1>";				
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Year</th><th>Average currency</th></tr></thead>";
				foreach ($result1 as $row) 			
				{
					echo "<tbody><tr>"; 
					echo "<td><a href='stat4.php?year={$row["year"]}'>" . $row["year"] . "</a></td>";					
					echo "<td>" . round($row["avgCurrency"], 3) . "</td>";
					echo "</tr></tbody>";
				}
				echo "</table>";
				echo "<div id='chartContainer1' style='height: 370px; width: 100%;'></div>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<h3>{$year}</h3>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Month</th><th>1 USD in NOK</th></tr></thead>";
				foreach ($result2 as $row) 							
				{
					echo "<tbody><tr>";
					echo "<td>" . $row["month"] . "</td>";
					echo "<td>" . round($row["currency"], 3) . "</td>";
					echo "</tr></tbody>";
				}
				 echo "</table>";
				 echo "</div>"; //col
				 echo "</div>"; //row
				 echo "</div>"; //container				 
			} 		            

            $year = getYearUrlparam();
            $client = new MongoDB\Client("mongodb://localhost:27017");

            //Find average USD price for each year
            $avgColl = $client->ikt446_adb->currencyAvgByYear;
            $filter2  = [];                    
            $options2 = ["sort" => ["year" => -1]];
            $result1 = $avgColl->find($filter2, $options2);
                                                       
            //List USD for selected year, show value for every month
            $currColl = $client->ikt446_adb->currency;   
			$filter2  = ["year" => $year];                    
			$options2 = ["sort" => ["month" => 1]];
            $result2 = $currColl->find($filter2, $options2);

            displayData($result1, $result2, $year);
					
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
		<script>

		function drawGraph(data){			
			console.log(data);
			var chart = new CanvasJS.Chart("chartContainer1",
				{
					animationEnabled: true,
					title: {
						text: "Currency USD in NOK "
					},
					axisX: {						
						interval: 10,
					},
					data: [
					{
						type: "splineArea",						
						color: "rgba(255,12,32,.3)",
						showInLegend: true,
						legendText: "Date",
						dataPoints: data
					},
					]
				});
			chart.render();
			}
		  $( document ).ready(function() {
			//  $.getJSON("./currencyservice.php", function(result){				 
			// 	 drawGraph(result);
			//  });	
		  });
		</script>
	</body>
</html>