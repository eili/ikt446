<?php

	require 'vendor/autoload.php';

	$client = new MongoDB\Client("mongodb://localhost:27017");
	$aggrByYearColl = $client->ikt446_adb->factAggrByYear;                        
	$filter  = [];  
	$options = ["sort" => ["year" => 1]];
	$result = $aggrByYearColl->find($filter, $options);

	$dataPoints = array();
	$volumedata = array();          
	foreach ($result as $row) 		
	{
		$ye = $row["year"];
		$musd = $row["amountMusd"];			                
		$vol = $row["kbarrels"];	
		array_push($dataPoints, array("y"=> $musd, "label"=>$ye));								
		array_push($volumedata, array("y"=> $vol, "label"=>$ye));		
	}		
	$container = array();
    $container["MUSD"] = $dataPoints;
    $container["KBarr"] = $volumedata;
    echo json_encode($container, JSON_NUMERIC_CHECK);

?>