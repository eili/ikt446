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
				
			$mainQry = ReadSqlQuery($conn, createSql($cc));

            $dataPoints = array();
            $x=0;
			while($row = sqlsrv_fetch_array($mainQry, SQLSRV_FETCH_ASSOC))  {
				$time = $row["year"] . "-" . $row["month"];
                $musd = $row["MUSD"];			
                $x++;
				array_push($dataPoints, array("y"=> $musd, label=>$time));								
            }		
            echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
		
			sqlsrv_free_stmt($mainQry);  			
			sqlsrv_close($conn);  			
		?>