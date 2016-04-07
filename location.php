<?PHP

require_once('dbConf.php');
	
	
			//$itemId  = '251394146146';
			
			
			$query = mysql_query("SELECT * FROM inventory");
			
			while($row = mysql_fetch_array($query))
			{
			//	echo $row['vendorsId'].'<br>';
				$sub = trim(strstr($row['vendorsId'],'-'),'-');
				
				$vendorId =  trim(strstr($sub,'-', true));
				
				if($vendorId!='')
				{
					mysql_query("UPDATE inventory SET vendorsId = '".$vendorId."' WHERE location =  '".$row['location']."'  ");
				}
			
			}
			exit;
			
	
			$sql = "SELECT * FROM pepauto_active WHERE location is not NULL ORDER BY LOCATION DESC";
			
			$result = mysql_query($sql) or die(mysql_error());
			
			while($row = mysql_fetch_assoc($result))
			{
				$location = $row['location'];	
				$quantity = $row['instock'];
				$cost = $row['cost'];
				$competKey = $row['competKey'];
				
				
		        // echo $row['postage'];
			//exit;
			echo $location.' $'.$cost.' # '.$quantity.' '.$row['competKey'].'<br>';
			
			
			
			
			$query = mysql_query("SELECT COUNT(*) as total FROM inventory WHERE location='".$location."'") or die(mysql_error());
			
			$existrow = mysql_fetch_array($query);

			
			if( $existrow['total'] > 0){
			    echo $row['sku'];
				mysql_query("UPDATE inventory SET pn='".$competKey."', cost=$cost, BRAND='MTC', vendorsId='".$row['sku']."', notes='".$competKey."',  pn='".$competKey."', qty=$quantity WHERE location='".$location."'");
				
				}
			else{
    			@mysql_query("INSERT INTO inventory SET location='".$location."', pn='".$competKey."', vendorsId='".$row['sku']."', BRAND='MTC', notes='".$competKey."', cost=$cost, qty=$quantity ");
			}
		
			
			}
			
	
			
		echo  'asd'; 
		
			
			
	/*
	
	
	$qry = mysql_query("SELECT * FROM pepauto_active ");
	
	while($row = mysql_fetch_array($qry))
	{
			
		$location = explode('-', $row['sku']);
		$location = trim($location[0]);
		
	
		if($location!='')
					mysql_query("UPDATE pepauto_active SET location = '".$location."' WHERE listingId = '".$row['listingId']."'");

	}
	*/
	
	
	
	


?>
