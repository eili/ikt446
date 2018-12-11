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


		   function createSqlByYear()
		   {
			   return "select f.year, f.amountMNOK as MNOK, f.amountMUSD as MUSD, f.kbarrels as KBarr " .
			   "from fact_aggregated_year f " . 			   			   
			   "order by f.year";				
		   }
           $conn = openConnection();  	
			
	    	$mainsql = createSqlByYear();						
			$qry = $conn->query($mainsql);
			
            $dataPoints = array();
            $volumedata = array();
			while($row = $qry->fetch_assoc()) {
				$ye = strval($row["year"]);
                $mu = $row["MUSD"];			
                $vol = $row["KBarr"];			
                array_push($dataPoints, array("y"=> $mu, "label"=>$ye));								
                array_push($volumedata, array("y"=> $vol, "label"=>$ye));								
            }		
            $container = array();
            $container["MUSD"] = $dataPoints;
            $container["KBarr"] = $volumedata;
            echo json_encode($container, JSON_NUMERIC_CHECK);
		
			$qry->free();
			mysqli_close($conn);				
		?>