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

   function createSql()
	{
		return "select c.month, c.year, c.barrelprice " .
		"from oilprice_dim c " . 
		"order by c.year, c.month";				 
	}		
   $conn = openConnection();  					    	
	$qry = $conn->query(createSql());					

	$dataPoints = array();            
	while($row = $qry->fetch_assoc()) 
	{
		$time = $row["year"] . "-" . $row["month"];
		$oilprice = $row["barrelprice"];			                
		array_push($dataPoints, array("y"=> $oilprice, "label"=>$time));								
	}		
	echo json_encode($dataPoints, JSON_NUMERIC_CHECK);

	$qry->free();
	mysqli_close($conn);		
?>