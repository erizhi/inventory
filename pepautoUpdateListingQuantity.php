<?PHP
echo 'asdasd';
require_once("dbConf.php");
require_once('pepautoConf.php');
	echo 'asdasd';
	
global $transactionTable;
	
$transactionTable = TRANSACTIONS_TABLE;

$backupQuantity = 18;



require_once("updateListingQuantity.php");



?>