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
				while($row = $avgusdQry->fetch_assoc())
				{
					echo "<tbody><tr>"; 
					echo "<td><a href='stat4.php?year={$row["year"]}'>" . $row["year"] . "</a></td>";					
					echo "<td>" . $row["avgusd"] . "</td>";
					echo "</tr></tbody>";
				}
				echo "</table>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<h3>{$year}</h3>";
				echo "<table class='table table-sm table-hover'>";
				echo "<thead><tr><th>Month</th><th>1 USD in NOK</th></tr></thead>";
				while($row = $getQry->fetch_assoc())
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
 			 
			$conn = openConnection();  	
			$year = getYearUrlparam();			
			$avgusdQry = $conn->query(createYearlySql());		
			$sql = createSql($year);			 
			$getQry = $conn->query($sql);		
			displayData($avgusdQry, $getQry, $year);
			
			//dispose resources
			$avgusdQry->free();
			$getQry->free();			
			mysqli_close($conn);			 
		?>
	</body>
</html>