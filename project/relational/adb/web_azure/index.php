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

		   
		   function createSql()
		   {
			   return "select p.pid, p.pname, c.cname, f.month, f.year, f.amountMNOK " .
               "from facttable f, product_dim p, country_dim c " .
               "where p.pid=f.pid and c.cid=f.cid and f.year=2017" .			   
			   "order by p.pid, f.year, f.month";
		   }

		   function DisplayData($getQry)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
                echo "<div class='col'>";
                echo "<h1>All data in fact table</h1>";									                
                echo "<table class='table table-sm table-hover'>";
						echo "<thead><tr><th>Product</th><th>Country</th><th>Year</th><th>Month</th><th>MNOK</th></tr></thead>";
					while($row = sqlsrv_fetch_array($getQry, SQLSRV_FETCH_ASSOC)) 
					{ 
						echo "<tbody><tr>";
                        echo "<td>" . $row["pname"] . "</td>";
                        echo "<td>" . $row["cname"] . "</td>";
                        echo "<td>" . $row["year"] . "</td>";
                        echo "<td>" . $row["month"] . "</td>";
						echo "<td>" . round($row["MNOK"]) . "</td>";						
						echo "</tr></tbody>";
					} 
				echo "</table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container					
			}  		
			sqlsrv_configure("WarningsReturnAsErrors", 1);
			$conn = OpenConnection();  							
			$sql = createSql();			
			$getQry = ReadSqlQuery($conn, $sql);			
			DisplayData($getQry);
			
			//dispose resources			
			sqlsrv_free_stmt($getQry);  					
			sqlsrv_close($conn);  			
		?>
	</body>
</html>