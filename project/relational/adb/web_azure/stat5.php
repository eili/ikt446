<html>
	<head>
		<title>
		IKT446
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export ADB</h1>			
			<a href="stat1.php">Home</a>
		</div>
		<?php
			function OpenConnection()  
			{  
				 $connectionInfo = array(
					 "UID" => "eivind@eivinl16", 
					 "pwd" => "Po68MLrXDD", 
					 "Database" => "ikt446_adb", 
					 "LoginTimeout" => 30, 
					 "Encrypt" => 1, 
					 "TrustServerCertificate" => 0
				 );
				 $serverName = "tcp:eivinl16.database.windows.net,1433";
				 $conn = sqlsrv_connect($serverName, $connectionInfo);
				 
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
				echo "<thead><tr><th>Year</th><th>Average currency</th></tr></thead>";
				while($row = sqlsrv_fetch_array($avgQry, SQLSRV_FETCH_ASSOC))  				
				{
					echo "<tbody><tr>"; 
					echo "<td><a href='stat5.php?year={$row["year"]}'>" . $row["year"] . "</a></td>";					
					echo "<td>" . $row["avgusd"] . "</td>";
					echo "</tr></tbody>";
				}
				echo "</table>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<h3>{$year}</h3>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Month</th><th>Barrel USD</th></tr></thead>";
				while($row = sqlsrv_fetch_array($getQry, SQLSRV_FETCH_ASSOC))  						
				{
					echo "<tbody><tr>";
					echo "<td>" . $row["month"] . "</td>";
					echo "<td>" . $row["barrelprice"] . "</td>";
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
			 $avgQry = ReadSqlQuery($conn, createYearlySql());		
			 $sql = createSql($year);
			 $getQry = ReadSqlQuery($conn, $sql);
			 displayData($avgQry, $getQry, $year);
			 
			 //dispose resources
			 sqlsrv_free_stmt($getQry);  					
			 sqlsrv_close($conn);  				
		?>
	</body>
</html>