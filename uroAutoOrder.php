<?php
include("src/Crawler.php");




require_once("dbConf.php");
require_once('part4engineConf.php');

$ordersTable = ORDERS_TABLE;
$transactionsTable = TRANSACTIONS_TABLE;
$activeTable = ACTIVE_LISTING_TABLE;


 date_default_timezone_set("America/Los_Angeles");

 
 
$sql = "SELECT orders.OrderID,tr.ItemID, tr.quantity , li.thumb, li.title, uro.partNo FROM ".$ordersTable
        ." as orders RIGHT JOIN ".$transactionsTable." as tr ON tr.orderId = orders.OrderID "
        ." LEFT JOIN $activeTable as li ON li.listingId = tr.ItemID INNER JOIN uro_inventory AS uro ON uro.upc = li.sku  WHERE (orders.ShippedTime is null or orders.ShippedTime = '0000-00-00 00:00:00') "
        ."and orders.date > DATE_SUB( NOW(), INTERVAL 100 HOUR) and tr.ordered <> 'yes' AND li.location = 'URO' "
        ;
$result = mysql_query($sql) or die(mysql_error());

$j = 1;

 $markOrders = '"';
while($row = mysql_fetch_assoc($result))
{
    
    $iId = $j++; 
    $markOrders .= $row['OrderID'].'","';
    
   
      if(isset($itemList[$row['partNo']])){
        $itemList[$row['partNo']] += $row['quantity'];
    }
    else {
        $itemList[$row['partNo']] = $row['quantity'];
    }
     
     
    
//    echo '<tr><td>'.$iId.'</td><td><input name="addItem['.$iId.']" type="checkbox" checked="checked"></td>'
//            .'<td><img src="'.$row['thumb'].'"></td><td> <input type="hidden" name="OrderID['.$iId.']" value="'.$row['OrderID'].'"> <input type="hidden" name="partNo['.$iId.']" value="'.$row['partNo'].'"> '.$row['partNo']
//            .'</td><td width="25" align="center"> <input type="hidden" name="qty['.$iId.']" value="'.$row['quantity'].'">'.$row['quantity']
//            .'</td><td>  <a href="http://www.ebay.com/itm/'.$row['ItemID'].'" target="_blank">'.$row['title'].'</a>'
//            .'</td><td>'.$row['ItemID'].'</td><td>'.$row['OrderID'].'</td></tr>';
    
}
$markOrders = rtrim($markOrders,'"');
$markOrders = rtrim($markOrders,',');

//print_r($itemList);

 
 $bulkPo = "purchaseOrder\tpartNo\tqty".PHP_EOL;


$poNumber = 'p4e'.date('mdyH');

foreach($itemList as $partNo => $qty)
{
    $bulkPo .= $poNumber."\t".$partNo."\t".$qty.PHP_EOL;
}


echo $markOrders;


echo "<br>";


$mycrawler=new Crawler();
//Some site Takes too many Hidden Parameters to Login.
//So it is hard to Login complex Login
$siteloginurl='http://uroparts.com/uro/customer/authenticate';//The url of Form when action perform
$parametes='username=iboyparts@gmail.com&password=canoga';//The parameters the action script need
include_once('src/dom/simple_html_dom.php');
$mycrawler->logIn($siteloginurl,$parametes);


//for($i=1;$i<140; $i++){
	
$row = 1;

  
$url = 'http://uroparts.com/uro/invoice/list/invoices';
   
	
//$url = 'http://www.stage32.com/profile/14918/david-navarro';

//$parametes = 'location=Los+Angeles&';

$content=$mycrawler->getContent($url, $parametes);

//echo $content;

$html = str_get_html($content);


$orders = $bulkPo;

$parametes = array('orders'=>$orders);

$content=$mycrawler->getContent('http://uroparts.com/uro/cart/upload', $parametes);

//echo $content;

$html = str_get_html($content);


    
    
$parametes = '';
$content=$mycrawler->getContent('http://uroparts.com/uro/cart/show', $parametes);

//echo $content;

$html = str_get_html($content);
//echo $html ;

$orderId = $html->find('input[@name=id]',0);
$orderId = $orderId->value;


echo "ORDER ID: ".$orderId;
echo "<br>";



//$parametes = array('cartupdate'=>2, 
//    'id' =>$orderId, '_action_edit'=>'Submit Order', 
//    'version' => 5, 'shipMethod'=>151,
//    'purchaseOrder' =>'p4e11121421');
//
//$content=$mycrawler->getContent('http://uroparts.com/uro/cart/show/'.$orderId, $parametes);
//    
//    

// submit order

$parametes = array('cartupdate'=>2, 
    'id' =>$orderId, '_action_invoice'=>'Submit Order', 
     'shipMethod'=>151,
    'purchaseOrder' =>$poNumber);

$content=$mycrawler->getContent('http://uroparts.com/uro/cart/index'.$orderId, $parametes);


$html = str_get_html($content);
echo $html ;


// 
    


// 
// MARK US ORDERED
$sql = "UPDATE ".$transactionsTable." SET ordered='yes' WHERE orderId IN (".$markOrders.")";
mysql_query($sql) or die(mysql_error());



?>