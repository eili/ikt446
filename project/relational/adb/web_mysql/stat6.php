<html>
	<head>
		<title>
        IKT446
		</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
	<body>
	    <div class='jumbotron'>
			<h1>Oil Export ADB MySQL</h1>			
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

			function getYearUrlparam()
		    {
				$yyyy = $_GET['year'] ?? '';				
				if($yyyy=='') 
				{
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

			function createSql($year, $month, $cc)
			{                
				return "select f.month, f.amountMUSD / DAY(LAST_DAY(DATE_ADD(MAKEDATE($year, 1), INTERVAL $month-1 MONTH))) as MUSD, f.kbarrels / DAY(LAST_DAY(DATE_ADD(MAKEDATE($year, 1), INTERVAL $month-1 MONTH)))  as KBarr " .
				"from facttable_USDP_Barrels f, product_dim p " . 
				"where f.pid=p.pid  " .
                "and f.year={$year} " .
                "and f.month={$month} " .
				"and f.pid=1 " .
				"and f.cid='{$cc}' ";				
			}		

			function displayData($getQry, $year, $month, $cc)  
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
				while($row = $getQry->fetch_assoc())  				
				{
					echo "<tr>";					
					echo "<td>" . round($row["MUSD"]) . "</td>";
					echo "<td>" . round($row["KBarr"]) . "</td>";
					echo "</tr>";
				}
				echo "</tbody></table>";
				echo "</div>"; //col
				echo "</div>"; //row
				echo "</div>"; //container				
			}  	

			$conn = openConnection();  	
            $year = getYearUrlparam();
            $month = getMonthUrlparam();						
			$cc = getCountryCodeparam();			
			$sql = createSql($year, $month, $cc);
			$getQry = $conn->query($sql);		
			displayData($getQry, $year, $month, $cc);
			
			//dispose resources	
			$getQry->free();			
			mysqli_close($conn);  	
		?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>