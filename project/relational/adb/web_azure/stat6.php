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
				$year = $_GET['year'];
				if(!$year || $year=='') {
					//set default if not provided.
					$year = "2017";
				}
				if(!is_numeric($year))
				{
					echo "<h2>Year parameter must be in integer type.</h2>";
					$year = "2017";
				}
				return $year;
            }
            function getMonthUrlparam()
			{
				$month = $_GET['month'];
				if(!$month || $month=='') {
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
				$cc = $_GET['cc'];
				if(strlen($cc) > 2) {
					echo "<h2>Countrycode parameter must be only two characters.</h2>";
					return "";
				}
				return $cc;
			}

			function createSql($year, $month, $cc)
			{                
				return "select f.month, f.amountMUSD / DAY(EOMONTH(DATEFROMPARTS($year, $month, 1))) as MUSD, 
				f.kbarrels / DAY(EOMONTH(DATEFROMPARTS($year, $month, 1)))  as KBarr " .
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
				echo "<thead><tr><th><a href='stat4.php?year={$year}'>MUSD</a></th><th><a href='stat5.php?year={$year}'>Kbarrels</a></th></tr></thead>";
				while($row = sqlsrv_fetch_array($getQry, SQLSRV_FETCH_ASSOC))  				
				{
					echo "<tbody><tr>";					
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
            $month = getMonthUrlparam();						
			$cc = getCountryCodeparam();			
			$sql = createSql($year, $month, $cc);
			$getQry = ReadSqlQuery($conn, $sql);		
			displayData($getQry, $year, $month, $cc);
			
			//dispose resources
			sqlsrv_free_stmt($getQry);  					
			sqlsrv_close($conn);  	
		?>
	</body>
</html>