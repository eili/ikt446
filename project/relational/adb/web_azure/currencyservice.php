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


		   function createSql($year)
			{
				return "select c.month, c.year, c.usdprice " .
				"from currency_dim c " . 				
				"order by c.year, c.month";				 
			}		
           $conn = OpenConnection();  	
			
	    	$mainsql = createSql();						
				
			$mainQry = ReadSqlQuery($conn, $mainsql);

            $dataPoints = array();
            $x=0;
			while($row = sqlsrv_fetch_array($mainQry, SQLSRV_FETCH_ASSOC))  {
				$time = $row["year"] . "-" . $row["month"];
                $curr = $row["usdprice"];			
                $x++;
				array_push($dataPoints, array("x"=> $x, "y"=> $curr, label=>$time));								
            }		
            echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
		
			sqlsrv_free_stmt($mainQry);  			
			sqlsrv_close($conn);  			
		?>