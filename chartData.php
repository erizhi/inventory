<?PHP
header('Content-type: application/json');

require_once('dbConf.php');

	
	


function getListingStatsByDate($listingId, $sum = 'daily', $date1 = '', $date2='')
{
	$dateRange = '';
	
	$statArray = false;
	
	//print_r($statArray);
	//exit;
	if($date1!='')
		{
			$dateRange = " AND date >= $date1";
			if($date2!='')
				$dateRange .=" AND date <= $date2";
		}
		else
		$dateRange = " AND date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)";
	
	$result = mysql_query("SELECT ItemId,quantity, date FROM transactions WHERE ItemId = '".$listingId."' ".$dateRange." ORDER by date") or die(mysql_error());
	
	
	
	while($row = mysql_fetch_array($result))
	{
		//print_r($row);
		if($row['date']==0)
			continue;
		 $date = strtotime($row['date']).'000' ; //date("Y ,m, d", strtotime());
		 
		 //echo '<br>';
		 
		 @ $statArray[$date] += $row['quantity'];
		  
	}
	$today = time().'000';
	$statArray[$today] = 0;
		

	return $statArray;	
	
}



$itemId = trim($_GET['itemId']);


	$return = getListingStatsByDate($itemId);

	
	 
	  $data_points = array();
	foreach($return as $date => $value)
	{
		if($date == 0)
		    continue;
			
			    $point = array("x" => $date , "y" => $value);
        
         array_push($data_points, $point);  
		
	
	}
	
	
	    echo json_encode($data_points, JSON_NUMERIC_CHECK);
		exit;
	
	
	
	
?>