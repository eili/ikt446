<html>
	<head>
		<title>
			IKT446
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">		
	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export Mongodb original values</h1>			
			<a href="stat1.php">Home</a>	
		</div>
		<?php
			require 'vendor/autoload.php';			   

		   function displayData($result1)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
                echo "<div class='col'>";
                echo "<h1>Export in million NOK</h1>";									                
                echo "<table class='table table-sm table-hover'>";
					echo "<thead><tr><th>Year</th><th>Month</th><th>Country</th><th>MNOK</th></tr></thead><tbody>";
					foreach ($result1 as $row) 		
					{ 
						echo "<tr>";                                                
                        echo "<td>" . $row["year"] . "</td>";
						echo "<td>" . $row["month"] . "</td>";
						echo "<td>" . $row["cc"] . "</td>";
						echo "<td>" . round($row["amount"]) . "</td>";						
						echo "</tr>";
					} 
				echo "</tbody></table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container					
			}  				
            $client = new MongoDB\Client("mongodb://localhost:27017");

            //Find average oil price for each year
            $exportColl = $client->ikt446_adb->export;
            $filter  = [];                    
            $options = ["sort" => ["year" => 1]];
			$result1 = $exportColl->find($filter, $options);
			displayData($result1);		
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>