<?PHP


	
require_once "eBaySOAP/ReviseItem.php";
// set the default timezone to use. Available since PHP 5.1

date_default_timezone_set('GMT');

$timestamp = strtotime(" - 1 hour 10 minutes");

$dateFrom = date('Y-m-d H:i:s', $timestamp);

//echo $sql;	


$sql = "SELECT itemID, quantity, date FROM ".$transactionTable." WHERE date>='".$dateFrom."'";		

$result = mysql_query($sql);


$soldArray = array();

while($row = mysql_fetch_assoc($result))
{
	echo $row['itemID']."<br>";
	
	if(array_key_exists($row['itemID'],$soldArray) )
		$soldArray[$row['itemID']] +=$row['quantity'];
	else
		$soldArray[$row['itemID']] = $row['quantity']; 
	 
}


print_r($soldArray);

foreach($soldArray as $itemID => $qty)
{
	$qty += $backupQuantity;
	setQuantity($qty, $itemID);
}


?>