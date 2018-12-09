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

			function getCountryCodeparam()
			{
				$cc = $_GET['cc'] ?? '';
				if(strlen($cc) > 2) 
				{
					echo "<h2>Countrycode parameter must be only two characters.</h2>";
					return "";
				}
				return $cc;
			}
			
			function DisplayData($result1, $result2, $year)  
			{  	
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";
				echo "<div><h1>Oil export for {$year}</h1>";
					
				echo "<h3>MUSD and K barrels</h3></div>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Year</th><th>MUSD</th><th>KBarr</th></tr></thead>";
				foreach ($result1 as $row) 
				{
					echo "<tbody><tr>";					
					echo "<td><a href='stat3.php?year={$row["year"]}&cc={$row["countrycode"]}'>{$row["year"]}</a></td>";					
					echo "<td>" . round($row["amountMusd"]) . "</td>";
					echo "<td>" . round($row["kbarrels"]) . "</td>";
					echo "</tr></tbody>";
				}
				echo "</table>";
				echo "<div id='chartContainer1' style='height: 370px; width: 100%;'></div>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Month</th><th><a href='stat4.php?year={$year}'>MUSD</a></th><th><a href='stat5.php?year={$year}'>Kbarrels</a></th></tr></thead>";
				foreach ($result2 as $row) 				
				{
					echo "<tbody><tr>";					
					echo "<td><a href='stat6.php?year={$row["year"]}&month={$row["month"]}&cc={$row["cc"]}'>{$row["month"]}</a></td>";										
					echo "<td>" . round($row["amountMusd"]) . "</td>";
					echo "<td>" . round($row["kbarrels"]) . "</td>";
					echo "</tr></tbody>";
				}
				echo "</table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container							
			}  		

            $cc = getCountryCodeparam();	
            $year = getYearUrlparam();	
            $client = new MongoDB\Client("mongodb://localhost:27017");
			$aggrByCcColl = $client->ikt446_adb->factAggrByCountry;                        
			$filter1  = ["countrycode" => $cc]; 
			$options1 = ["sort" => ["year" => -1]];
            $result1 = $aggrByCcColl->find($filter1, $options1);
            
			$factColl = $client->ikt446_adb->fact;   
			$filter2  = ["year" => $year, "cc" => $cc];                    
			$options2 = ["sort" => ["month" => 1]];
            $result2 = $factColl->find($filter2, $options2);

            displayData($result1, $result2, $year, $cc);

		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
		<script>

		function getUrlParameter(name) {
			name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(location.search);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		};

		function drawGraph(data){						
			var chart = new CanvasJS.Chart("chartContainer1",
				{
					animationEnabled: true,
					title: {
						text: "Oil Export"
					},
					axisX: {						
						interval: 10,
					},
					data: [
					{
						type: "column",						
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
			//   var cc=getUrlParameter("cc");
			//  $.getJSON("./exportservice.php?&cc="+cc, function(result){				 
			// 	 drawGraph(result);
			//  });	
		  });
		</script>
	</body>
</html>