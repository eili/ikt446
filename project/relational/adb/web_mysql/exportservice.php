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


		   function createSql($cc)
			{
				return "select f.year, f.month, f.amountMUSD as MUSD, f.kbarrels as KBarr, f.cid  " .
				"from facttable_USDP_Barrels f, product_dim p " . 
				"where f.pid=p.pid " .				
				"and f.pid=1 " .
                "and f.cid='{$cc}' " .
                "order by f.year, f.month";					
			}		
           $conn = OpenConnection();  			
           $cc = getCountryCodeparam();		    	
			$qry = $conn->query(createSql($cc));		
			

            $dataPoints = array();            
			while($row = $qry->fetch_assoc()) 
			{
				$time = $row["year"] . "-" . $row["month"];
                $musd = $row["MUSD"];			                
				array_push($dataPoints, array("y"=> $musd, "label"=>$time));								
            }		
            echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
		
			$qry->free();
			mysqli_close($conn);  			
		?>