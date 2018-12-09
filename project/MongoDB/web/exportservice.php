<?php
	require 'vendor/autoload.php';

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

	$client = new MongoDB\Client("mongodb://localhost:27017");
	$cc = getCountryCodeparam();	
	$factColl = $client->ikt446_adb->fact;   
	$filter  = ["cc" => $cc];                    
	$options = ["sort" => ["year" => 1, "month" => 1]];
	$result = $factColl->find($filter, $options);
			
	$dataPoints = array();            
	foreach ($result as $row) 		
	{
		$time = $row["date"];
		$musd = $row["amountMusd"];			                
		array_push($dataPoints, array("y"=> $musd, "label"=>$time));	
	}		
	echo json_encode($dataPoints, JSON_NUMERIC_CHECK);
		
?>