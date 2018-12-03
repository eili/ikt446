<html>
	<head>
		<title>
		IKT446
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export ADB</h1>			
			<a href="stat1.php">Home</a>			
		</div>
		<?php
			function OpenConnection()  
			{  
				$config = parse_ini_file('./config.ini');
				$connectionInfo = array(
					"UID" => $config['username'], 
					"pwd" => $config['password'], 
					"Database" => $config['dbname'], 
					"LoginTimeout" => 30, 
					"Encrypt" => 1, 
					"TrustServerCertificate" => 0
				);					 
				$conn = sqlsrv_connect($config['servername'], $connectionInfo);
				 
				 if($conn == false)  
					 die(FormatErrors(sqlsrv_errors()));  
				 return $conn;	
			}  
 
			function ReadSqlQuery($conn, $sql)
			{
				 $getQry = sqlsrv_query($conn, $sql);  
				 if ($getQry == FALSE) { 
					 echo("ReadSqlQuery == FALSE");    
					 die(FormatErrors(sqlsrv_errors()));  
				 }	
				 return $getQry;
			}

			function getYearUrlparam()
			{
				 $yyyy = $_GET['year'];
				 if(!$yyyy || $yyyy=='') {
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
				return "select year, AVG(usdprice) as avgusd from currency_dim group by year order by year desc";
			}
			function createSql($year)
			{
				return "select c.month, c.year, c.usdprice " .
				"from currency_dim c " . 
				"where c.year={$year} " .
				"order by c.month";				 
			}			
 
			function displayData($avgusdQry, $getQry, $year)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";
				echo "<h1>USD - NOK exchange rate</h1>";				
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Year</th><th>Average currency</th></tr></thead>";
				while($row = sqlsrv_fetch_array($avgusdQry, SQLSRV_FETCH_ASSOC))  				
				{
					echo "<tbody><tr>"; 
					echo "<td><a href='stat4.php?year={$row["year"]}'>" . $row["year"] . "</a></td>";					
					echo "<td>" . $row["avgusd"] . "</td>";
					echo "</tr></tbody>";
				}
				echo "</table>";
				echo "<div id='chartContainer1' style='height: 370px; width: 100%;'></div>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<h3>{$year}</h3>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Month</th><th>1 USD in NOK</th></tr></thead>";
				while($row = sqlsrv_fetch_array($getQry, SQLSRV_FETCH_ASSOC))  								
				{
					echo "<tbody><tr>";
					echo "<td>" . $row["month"] . "</td>";
					echo "<td>" . $row["usdprice"] . "</td>";
					echo "</tr></tbody>";
				}
				 echo "</table>";
				 echo "</div>"; //col
				 echo "</div>"; //row
				 echo "</div>"; //container				 
			} 		
 
			 sqlsrv_configure("WarningsReturnAsErrors", 1);
			 $conn = OpenConnection();  	
			 
			 $avgusdQry = ReadSqlQuery($conn, createYearlySql());		
			 $year = getYearUrlparam();			
			 $sql = createSql($year);			 
			 $getQry = ReadSqlQuery($conn, $sql);	
			 displayData($avgusdQry, $getQry, $year);
			 
			 //dispose resources
			 sqlsrv_free_stmt($getQry);  					
			 sqlsrv_close($conn);  				
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
						legendMarkerType: "triangle",
						legendMarkerColor: "green",
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
			 $.getJSON("./currencyservice.php", function(result){				 
				 drawGraph(result);
			 });	
		  });
		</script>
	</body>
</html>