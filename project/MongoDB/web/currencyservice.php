<?php
		
	require 'vendor/autoload.php';		
	$client = new MongoDB\Client("mongodb://localhost:27017");
	$currColl = $client->ikt446_adb->currency;   
	$filter  = [];                    
	$options = ["sort" => ["year" => 1, "month" => 1]];
    $result = $currColl->find($filter, $options);

	$dataPoints = array();            
	foreach ($result as $row) 	
	{
		$time = $row["date"];
		$curr = $row["currency"];			                
		array_push($dataPoints, array("y"=> $curr, "label"=>$time));								
	}		
	echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
			
?>