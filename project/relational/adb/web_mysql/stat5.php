<html>
	<head>
		<title>
		IKT446
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">	
	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export ADB MySQL</h1>			
			<a href="stat1.php">Home</a>
		</div>
		<?php
		    function openConnection()  
		    {  
				$config = parse_ini_file('./config.ini');
				$servername = $config['servername'];
				$username = $config['username'];
				$password = $config['password'];
				$dbname = $config['dbname'];							
				// Create connection
				$conn = new mysqli($servername, $username, $password, $dbname);

				// Check connection
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				} 			
				return $conn;	
		    }  			
 
			function getYearUrlparam()
		    {
				$yyyy = $_GET['year'] ?? '';				
				if($yyyy=='') 
				{
					//set default if not provided.
					$yyyy = "2017";
				}
				if(!is_numeric($yyyy))
				{
					echo "<h2>Year parameter must be in integer type.</h2>";
					$yyyy = "2017";
				}
				return $yyyy;
		    }

			function createYearlySql()
			{
				return "select year, AVG(barrelprice) as avgusd from oilprice_dim group by year order by year desc";
			}
			function createSql($year)
			{
				return "select c.month, c.year, c.barrelprice " .
				"from oilprice_dim c " . 
				"where c.year={$year} " .
				"order by c.month";				 
			}			

			function displayData($avgQry, $getQry, $year)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";
				echo "<h1>Oil price pr barrel</h1>";				
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Year</th><th>Average currency</th></tr></thead><tbody>";
				while($row = $avgQry->fetch_assoc())		
				{
					echo "<tr>"; 
					echo "<td><a href='stat5.php?year={$row["year"]}'>" . $row["year"] . "</a></td>";					
					echo "<td>" . $row["avgusd"] . "</td>";
					echo "</tr>";
				}
				echo "</tbody></table>";
				echo "<div id='chartContainer1' style='height: 370px; width: 100%;'></div>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<h3>{$year}</h3>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Month</th><th>Barrel USD</th></tr></thead><tbody>";
				while($row = $getQry->fetch_assoc())						
				{
					echo "<tr>";
					echo "<td>" . $row["month"] . "</td>";
					echo "<td>" . $row["barrelprice"] . "</td>";
					echo "</tr>";
				}
				 echo "</tbody></table>";
				 echo "</div>"; //col
				 echo "</div>"; //row
				 echo "</div>"; //container				 
			}  		  		
 
			$conn = openConnection();  	
			$year = getYearUrlparam();
			$avgQry = $conn->query(createYearlySql());		 
			$sql = createSql($year);
			$getQry = $conn->query($sql);		
			displayData($avgQry, $getQry, $year);
			
			//dispose resources
			$avgQry->free();	
			$getQry->free();			
			mysqli_close($conn);			
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
		<script>

		function drawGraph(data){			
			var chart = new CanvasJS.Chart("chartContainer1",
				{
					animationEnabled: true,
					title: {
						text: "Oilprice in USD "
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
			 $.getJSON("./oilpriceservice.php", function(result){				 
				 drawGraph(result);
			 });	
		  });
		</script>
	</body>
</html>