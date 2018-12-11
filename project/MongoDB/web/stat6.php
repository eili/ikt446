<html>
	<head>
		<title>
        IKT446 MongoDB
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export ADB Mongodb</h1>			
			<a href="stat1.php">Home</a>			
		</div>
		<?php
            require 'vendor/autoload.php';			    

			function getYearUrlparam()
            {
                $yyyy = $_GET['year'] ?? '';				
                if($yyyy=='') 
                {
                    //set default if not provided.
                    $yyyy = 2017;
                }
                if(!is_numeric($yyyy))
                {
                    echo "<h2>Year parameter must be in integer type.</h2>";
                    $yyyy = 2017;
                }
                return (int) $yyyy;
            }		

            function getMonthUrlparam()
			{
				$month = $_GET['month'] ?? '';
                if($month=='') 
                {
					//set default if not provided.
					$month = 1;
				}
				if(!is_numeric($month))
				{
					echo "<h2>Month parameter must be in integer type.</h2>";
					$month = 1;
                }
                $month = intval($month);
                if($month>12)
                    $month = 12;
                if(($month < 1))    
                    $month = 1;
                
				return $month;
			}

			function getCountryCodeparam()
			{
				$cc = $_GET['cc'] ?? '';
                if(strlen($cc) > 2) 
                {
					echo "<h2>Countrycode parameter must be only two characters.</h2>";
					return "";
				}
				return $cc;
			}	

			function displayData($result1, $year, $month, $cc)  
			{  																																		
				echo "<div class='container'>";
				echo "<div class='row'>";
				echo "<div class='col-md-6'>";
                echo "<div><h1>Oil export per day for  {$cc}</h1>";
                echo "<h2>{$year} - {$month} </h2>";                                
				echo "<h3>MUSD and K barrels</h3></div>";
				echo "</div>"; //col
				echo "<div class='col-md-6'>";
				echo "<table class='table table-sm table-hover'>";
                echo "<thead><tr><th><a href='stat4.php?year={$year}'>MUSD</a></th><th><a href='stat5.php?year={$year}'>Kbarrels</a></th></tr></thead><tbody>";
                $daysInMonth = cal_days_in_month(1, $month, $year);
				foreach ($result1 as $row) 			
				{
					echo "<tr>";					
					echo "<td>" . round($row["amountMusd"] / $daysInMonth, 1) . "</td>";
					echo "<td>" . round($row["kbarrels"] / $daysInMonth, 1) . "</td>";
					echo "</tr>";
				}
				echo "</tbody></table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container				
			}  	
			
            $year = getYearUrlparam();
            $month = getMonthUrlparam();						
            $cc = getCountryCodeparam();			
            
            $client = new MongoDB\Client("mongodb://localhost:27017");

            //Convert values to daily summary
            $factColl = $client->ikt446_adb->fact;
            $filter2  = ["year" => $year, "month" => $month, "cc" => $cc];                    
            $options2 = ["sort" => ["year" => -1]];
            $result1 = $factColl->find($filter2, $options2);

            displayData($result1, $year, $month, $cc);
				
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>