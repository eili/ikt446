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
			<a href="index.php">Index</a>
		</div>
		<?php

			$dataPoints = array();			
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

		   function createSqlByYear()
		   {
			   return "select f.year, sum(f.amountMNOK) as MNOK, sum(f.amountMUSD) as MUSD, sum(f.kbarrels) as KBarr " .
			   "from fact_aggregated f " . 
			   "where f.pid=1 " .				
			   "group by f.year " .
			   "order by f.year desc";				
		   }

		   function createSql($year)
		   {
			   return "select f.cid, f.year, sum(f.amountMNOK) as MNOK, sum(f.amountMUSD) as MUSD, sum(f.kbarrels) as KBarr  " .
			   "from facttable_USDP_Barrels f, product_dim p, country_dim c " . 
			   "where f.pid=p.pid and f.cid=c.cid " .
			   "and f.year={$year} " .
			   "and f.pid=1 " .
			   "group by f.year, f.cid " .
			   "order by MUSD desc";
		   }

		   function DisplayData($mainQry, $getQry, $year)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";				
				echo "<h3>MUSD by year</h3>";
				echo "<h3>Kbarrels is barrels*1000</h3>";				
				echo "<table class='table table-sm table-hover'>";
					echo "<thead><tr><th>Year</th><th>MNOK</th><th>MUSD</th><th>Kbarrels</th></tr></thead>";
					while($row = sqlsrv_fetch_array($mainQry, SQLSRV_FETCH_ASSOC))  
					{  						
						echo "<tbody><tr>";
						echo "<td><a href='stat1.php?year={$row["year"]}'>{$row["year"]}</a></td>";																			
						echo "<td>" . round($row["MNOK"]) . "</td>";
						echo "<td>" . round($row["MUSD"]) . "</td>";
						echo "<td>" . round($row["KBarr"]) . "</td>";
						echo "</tr></tbody>";
					}  
					echo "</table>";
					echo "<div id='chartContainer1' style='height: 370px; width: 100%;'></div>";
					echo "</div>"; //col
					echo "<div class='col-md-6'>";
					echo "<h3>{$year}</h3>";
					echo "<table class='table table-sm table-hover'>";
						echo "<thead><tr><th>Country</th><th>MNOK</th><th><a href='stat4.php?year={$year}'>MUSD</a></th><th><a href='stat5.php?year={$year}'>Kbarrels</a></th></tr></thead>";
					while($row = sqlsrv_fetch_array($getQry, SQLSRV_FETCH_ASSOC)) 
					{ 
						echo "<tbody><tr>";
						echo "<td><a href='stat3.php?year={$row["year"]}&cc={$row["cid"]}'>" . $row["cid"] . "</a></td>";
						echo "<td>" . round($row["MNOK"]) . "</td>";
						echo "<td>" . round($row["MUSD"]) . "</td>";
						echo "<td>" . round($row["KBarr"]) . "</td>";
						echo "</tr></tbody>";
					} 
				echo "</table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container					
			}  		

			sqlsrv_configure("WarningsReturnAsErrors", 1);
			$conn = OpenConnection();  	
			$year = getYearUrlparam();
			
			$sql = createSql($year);
			$mainsql = createSqlByYear();						
	
			$getQry = ReadSqlQuery($conn, $sql);
			$mainQry = ReadSqlQuery($conn, $mainsql);			
			DisplayData($mainQry, $getQry, $year);
			
			//dispose resources
			sqlsrv_free_stmt($mainQry);  
			sqlsrv_free_stmt($getQry);  					
			sqlsrv_close($conn);  			
		?>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
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