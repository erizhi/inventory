<html>
<?PHP


//	mysql_connect('pepauto.com', 'pepauto_iboy', ')CBcsBH=xvDM');
	
	require_once("dbConf.php");
	require_once('part4engineConf.php');
	
	
	$listingTable = ACTIVE_LISTING_TABLE;


	require_once('loadActiveListings.php');
include_once('footer.php');
?>

