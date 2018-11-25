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

			function getCountryCodeparam()
			{
				$cc = $_GET['cc'];
				if(strlen($cc) > 2) {
					echo "<h2>Countrycode parameter must be only two characters.</h2>";
					return "";
				}
				return $cc;
			}

			function getCountryByCC($cc)
			{
				return "select cname from country_dim " .
				"where cid='{$cc}' ";				
			}

			function getCountrystatByYear($cc)
			{
				return "select f.year, f.cid, f.amountMUSD as MUSD, f.kbarrels as KBarr " .
				"from fact_aggregated f, country_dim c " .
				"where f.cid = c.cid and f.cid='{$cc}' " .
				"and f.pid = 1 order by f.year desc";
			}

			function createSql($year, $cc)
			{
				return "select f.year, f.month, f.amountMUSD as MUSD, f.kbarrels as KBarr, f.cid  " .
				"from facttable_USDP_Barrels f, product_dim p " . 
				"where f.pid=p.pid " .
				"and f.year={$year} " .
				"and f.pid=1 " .
				"and f.cid='{$cc}'";				
			}

			function displayData($countryQry, $byYearQry, $getQry, $year, $cc)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";
				echo "<div><h1>Oil export for {$year}</h1>";
				while($row = $countryQry->fetch_assoc())
				{
					echo "<h3>" . $row["cname"] . "</h3>";
				}				
				echo "<h3>MUSD and K barrels</h3></div>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Year</th><th>MUSD</th><th>KBarr</th></tr></thead>";
				while($row = $byYearQry->fetch_assoc())
				{
					echo "<tbody><tr>";					
					echo "<td><a href='stat3.php?year={$row["year"]}&cc={$row["cid"]}'>{$row["year"]}</a></td>";						
					echo "<td>" . round($row["MUSD"]) . "</td>";
					echo "<td>" . round($row["KBarr"]) . "</td>";
					echo "</tr></tbody>";
				}
				echo "</table>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Month</th><th><a href='stat4.php?year={$year}'>MUSD</a></th><th><a href='stat5.php?year={$year}'>Kbarrels</a></th></tr></thead>";
				while($row = $getQry->fetch_assoc())
				{
					echo "<tbody><tr>";					
					echo "<td><a href='stat6.php?year={$row["year"]}&month={$row["month"]}&cc={$row["cid"]}'>{$row["month"]}</a></td>";										
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
			$cc = getCountryCodeparam();			
			$countrySql = getCountryByCC($cc);
			$countryQry = $conn->query($countrySql);	
			$byYearSql = getCountrystatByYear($cc);
			$byYearQry = $conn->query($byYearSql);		
			$sql = createSql($year, $cc);
			$getQry = $conn->query($sql);		
			displayData($countryQry, $byYearQry, $getQry, $year, $cc);
			
			//dispose resources
			$countryQry->free();	
			$byYearQry->free();	
			$getQry->free();			
			mysqli_close($conn);
		?>
	</body>
</html>