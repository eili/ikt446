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
			<a href="index.php">Index</a>
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
				echo "<h3>MUSD by year</h3>";
				echo "<h3>Kbarrels is barrels*1000</h3>";				
				echo "<table class='table table-sm table-hover'>";
					echo "<thead><tr><th>Year</th><th>MNOK</th><th>MUSD</th><th>Kbarrels</th></tr></thead><tbody>";
					foreach ($result1 as $row) 
					{  						
						echo "<tr>";
						echo "<td><a href='stat1.php?year={$row["year"]}'>{$row["year"]}</a></td>";																			
						echo "<td>" . round($row["amountMNOK"]) . "</td>";
						echo "<td>" . round($row["amountMusd"]) . "</td>";
						echo "<td>" . round($row["kbarrels"]) . "</td>";
						echo "</tr>";
					}  
					echo "</tbody></table>";
					echo "<div id='chartContainer1' style='height: 370px; width: 100%;'></div>";
					echo "</div>"; //col
					echo "<div class='col-md-6'>";
					echo "<h3>{$year}</h3>";
					echo "<table class='table table-sm table-hover'>";
						echo "<thead><tr><th>Country</th><th>MNOK</th><th><a href='stat4.php?year={$year}'>MUSD</a></th><th><a href='stat5.php?year={$year}'>Kbarrels</a></th></tr></thead><tbody>";
                    foreach ($result2 as $row) 
					{ 
						echo "<tr>";
						echo "<td><a href='stat3.php?year={$row["year"]}&cc={$row["countrycode"]}'>" . $row["countrycode"] . "</a></td>";
						echo "<td>" . round($row["amountMNOK"]) . "</td>";
						echo "<td>" . round($row["amountMusd"]) . "</td>";
						echo "<td>" . round($row["kbarrels"]) . "</td>";
						echo "</tr>";
					} 
				echo "</tbody></table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container					
            }  		
            
            $client = new MongoDB\Client("mongodb://localhost:27017");
			$aggrByYearColl = $client->ikt446_adb->factAggrByYear;                        
			$filter1  = [];  
			$options1 = ["sort" => ["year" => -1]];
            $result1 = $aggrByYearColl->find($filter1, $options1);

            $year = getYearUrlparam();
			$aggrByCcColl = $client->ikt446_adb->factAggrByCountry;   
			$filter2  = ["year" => $year];                    
			$options2 = ["sort" => ["amountMusd" => -1]];
            $result2 = $aggrByCcColl->find($filter2, $options2);
                        
            displayData($result1, $result2, $year)
            
        ?>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>		
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
		<script>

		function drawGraph(data1, data2){						
			var chart = new CanvasJS.Chart("chartContainer1",
				{
					animationEnabled: true,
					title: {
						text: "Oil export"
					},
					axisY: {
						title: "Million USD",
						titleFontColor: "#4F81BC",
						lineColor: "#4F81BC",
						labelFontColor: "#4F81BC",
						tickColor: "#4F81BC"
					},
					axisY2: {
						title: "Barrels*1000",
						titleFontColor: "#C0504E",
						lineColor: "#C0504E",
						labelFontColor: "#C0504E",
						tickColor: "#C0504E"
					},	
					toolTip: {
						shared: true
					},
					data: [
					{
						type: "column",
						name: "Million USD",
						legendText: "Million USD",						
						color: "rgba(255,12,32,.3)",
						showInLegend: true,
						legendText: "Year",
						dataPoints: data1
					},
					{
						type: "column",
						name: "Barrels*1000",
						legendText: "Barrels*1000",						
						color: "rgba(32,12,200,.3)",
						showInLegend: true,
						legendText: "Year",
						axisYType: "secondary",
						dataPoints: data2
					},
					]
				});
			chart.render();
			}
		  $( document ).ready(function() {
			 $.getJSON("service1.php", function(result){			
				 drawGraph(result["MUSD"], result["KBarr"]);
			 });	
		  });
		</script>
	</body>
</html>