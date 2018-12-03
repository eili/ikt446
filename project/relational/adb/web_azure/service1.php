<?php

		
		   function OpenConnection()  
		   {  
            $config = parse_ini_file('./config.ini');
            $connectionInfo = array(
                "UID" => $config['username'], 
                "pwd" => $config['password'], 
                "Database" => $config['dbname'], 
                "LoginTimeout" => 30, 
                "Encrypt" => 1, 
                "TrustServerCertificate" => 0
            );					
				$conn = sqlsrv_connect($config['servername'], $connectionInfo);
				
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


		   function createSqlByYear()
		   {
			   return "select f.year, sum(f.amountMNOK) as MNOK, sum(f.amountMUSD) as MUSD, sum(f.kbarrels) as KBarr " .
			   "from fact_aggregated f " . 
			   "where f.pid=1 " .				
			   "group by f.year " .
			   "order by f.year desc";				
		   }
           $conn = OpenConnection();  	
			
	    	$mainsql = createSqlByYear();						
				
			$mainQry = ReadSqlQuery($conn, $mainsql);
            $dataPoints = array();
			while($row = sqlsrv_fetch_array($mainQry, SQLSRV_FETCH_ASSOC))  {
				$ye = $row["year"];
				$mu = $row["MUSD"];			
				array_push($dataPoints, array("x"=> $ye, "y"=> $mu, label=>$ye));								
            }		
            echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
		
			sqlsrv_free_stmt($mainQry);  			
			sqlsrv_close($conn);  			
		?>