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
			<a href="index.php">Home</a>
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
	</body>
</html>