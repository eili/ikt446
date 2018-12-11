<html>
	<head>
		<title>
			IKT446
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">		
	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export ODB original values</h1>			
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
		   
		   function createSql()
		   {
			   return "select p.pid, p.pname, c.cname, f.month, f.year, f.amountMNOK " .
               "from facttable f, product_dim p, country_dim c " .
               "where p.pid=f.pid and c.cid=f.cid and f.id=1 " .			   
			   "order by f.year, f.month, f.cname";
		   }

		   function displayData($getQry)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
                echo "<div class='col'>";
                echo "<h1>Export in million NOK</h1>";									                
                echo "<table class='table table-sm table-hover'>";
					echo "<thead><tr><th>Year</th><th>Month</th><th>Country</th><th>MNOK</th></tr></thead><tbody>";
						while($row = $mainQry->fetch_assoc())  
					{ 
						echo "<tr>";                                                
                        echo "<td>" . $row["year"] . "</td>";
						echo "<td>" . $row["month"] . "</td>";
						echo "<td>" . $row["cname"] . "</td>";
						echo "<td>" . round($row["amountMNOK"]) . "</td>";						
						echo "</tr>";
					} 
				echo "</tbody></table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container					
			}  					
			$conn = openConnection();  										
			$getQry = $conn->query(createSql());
			displayData($getQry);
			
			//dispose resources			
			$getQry->free();
			mysqli_close($conn);			
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>