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
		</div>
		<?php

		    function openConnection()  
		    {  
				$servername = "localhost";
				$username = "eivind";
				$password = "passord1";
				$dbname = "proj_adb";								
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

		    function displayData($mainQry, $getQry, $year)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";				
				echo "<h3>MUSD by year</h3>";
				echo "<h3>Kbarrels is barrels*1000</h3>";				
				echo "<table class='table table-sm table-hover'>";
					echo "<thead><tr><th>Year</th><th>MNOK</th><th>MUSD</th><th>Kbarrels</th></tr></thead>";
					while($row = $mainQry->fetch_assoc())  
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
					while($row = $getQry->fetch_assoc())
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
			
			$conn = openConnection();  	
			$year = getYearUrlparam();
			
			$sql = createSql($year);
			$mainsql = createSqlByYear();
			$mainQry = $conn->query($mainsql);
			$getQry = $conn->query($sql);			
			displayData($mainQry, $getQry, $year);
						
			//dispose resources
			$mainQry->free();
			$getQry->free();
			mysqli_close($conn);		
		?>
	</body>
</html>