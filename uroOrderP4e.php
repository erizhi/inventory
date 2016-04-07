<?PHP include('header.php');?>
<?php

require_once("dbConf.php");
require_once('part4engineConf.php');

$ordersTable = ORDERS_TABLE;
$transactionsTable = TRANSACTIONS_TABLE;
$activeTable = ACTIVE_LISTING_TABLE;


$addItem = '';
if(isset($_POST['addItem']))
    $addItem = $_POST['addItem'];

//print_r($addItem);

//print_r($_POST['partNo']);
 date_default_timezone_set("America/Los_Angeles");
 

 
 if(isset($_POST['addItem'])) {
     
     if( $_POST['markOrdered'] == 'MARK AS ORDERED')
     {
         $markOrders = '"';
         foreach($addItem as $key => $value)
            {
                    $markOrders .= $_POST['OrderID'][$key].'","';
            }
            
            $markOrders = rtrim($markOrders,'"');
            $markOrders = rtrim($markOrders,',');
     
            

     $sql = "UPDATE ".$transactionsTable." SET ordered='yes' WHERE orderId IN (".$markOrders.")";

     mysql_query($sql) or die(mysql_error());
     
     echo '<h2>TRANSACTIONS ARE NOW MARKED AS ORDERED. <b>LAST CHANCE TO COPY ORDER FROM BOX BELOW</b> </h2>';
     
     }
     
     
     
echo '<textarea cols=50 rows=10 >';
echo "purchaseOrder\tpartNo\tqty".PHP_EOL;



$combinedPo = array();

foreach($addItem as $key => $value)
{
   // echo 'p4e'.date('mdyH')."\t".$_POST['partNo'][$key]."\t".$_POST['qty'][$key].PHP_EOL;
    
    if(isset($combinedPo[$_POST['partNo'][$key]])){
        $combinedPo[$_POST['partNo'][$key]] += $_POST['qty'][$key];
    }
    else {
        $combinedPo[$_POST['partNo'][$key]] = $_POST['qty'][$key];
    }
        
}

foreach($combinedPo as $partNo => $qty)
{
    echo 'p4e'.date('mdyH')."\t".$partNo."\t".$qty.PHP_EOL;
}

echo '</textarea>';

 }
 
 
$sql = "SELECT orders.OrderID,tr.ItemID, tr.quantity , li.thumb, li.title, uro.partNo FROM ".$ordersTable
        ." as orders RIGHT JOIN ".$transactionsTable." as tr ON tr.orderId = orders.OrderID "
        ." LEFT JOIN $activeTable as li ON li.listingId = tr.ItemID INNER JOIN uro_inventory AS uro ON uro.upc = li.sku  WHERE (orders.ShippedTime is null or orders.ShippedTime = '0000-00-00 00:00:00') "
        ."and orders.date > DATE_SUB( NOW(), INTERVAL 100 HOUR) and tr.ordered <> 'yes' AND li.location = 'URO' "
        ;
$result = mysql_query($sql) or die(mysql_error());

$j = 1;

?>

<form action="" method="post">
<table border="1">
    <tr>
    <th>#</th>
    <th>add</th>
    <th>Thumb</th>
    <th>partNO</th>
    <th>qty</th>
    <th>Title</th>
    <th>Listing ID</th>
    <th>OrderID</th>
    </tr>

<?PHP
while($row = mysql_fetch_assoc($result))
{
    
    $iId = $j++; 
    echo '<tr><td>'.$iId.'</td><td><input name="addItem['.$iId.']" type="checkbox" checked="checked"></td>'
            .'<td><img src="'.$row['thumb'].'"></td><td> <input type="hidden" name="OrderID['.$iId.']" value="'.$row['OrderID'].'"> <input type="hidden" name="partNo['.$iId.']" value="'.$row['partNo'].'"> '.$row['partNo']
            .'</td><td width="25" align="center"> <input type="hidden" name="qty['.$iId.']" value="'.$row['quantity'].'">'.$row['quantity']
            .'</td><td>  <a href="http://www.ebay.com/itm/'.$row['ItemID'].'" target="_blank">'.$row['title'].'</a>'
            .'</td><td>'.$row['ItemID'].'</td><td>'.$row['OrderID'].'</td></tr>';
    
}

?>
</table>
    <br>
<input type="submit" value="GENERATE PO" name="generate" >
&nbsp;&nbsp;&nbsp;
<input type="submit" value="MARK AS ORDERED" name="markOrdered" >
</form>

<?PHP include('footer.php');?>