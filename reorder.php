<style>
.order {border: #00F solid 5px}
</style>

<script>
function addToOrder(pn, sku)
{
    alert('Order part # '+pn);
}
</script>

<?PHP
$totalIncome = 0;

require_once('dbConf.php');

       
// HARDCODED SCRIPT TO UPDATE SKUs IN ACTIVE ITEMS TABLE

//$sql = "SELECT listingId FROM pepauto_active";
//        
//$result = mysql_query($sql);
//
//while($row = mysql_fetch_assoc($result))
//{
//   // echo $row['listingId'];
//    
//    $sql = "SELECT sku FROM transactions WHERE ItemID = ".$row['listingId']." and sku IS NOT NULL order by date desc limit 1";
//        
//    $res = mysql_query($sql);
//    
//    $sku = mysql_fetch_assoc($res);
//    
//    echo $sku['sku']; 
//    
//    mysql_query("UPDATE pepauto_active SET sku='".$sku['sku']."' WHERE listingId = ".$row['listingId']);
//    
//    echo PHP_EOL;
//}



//mysql_connect('armsms.com', 'pepauto_iboy', ')CBcsBH=xvDM');
//mysql_select_db('pepauto_iboy');

$startFrom = date('Y-m-d H:i:s', strtotime('-1 month'));
//echo $startFrom;

$sql = "SELECT * FROM transactions "
."LEFT JOIN pepauto_active ON transactions.ItemID = pepauto_active.listingId"
." WHERE transactions.date > '".$startFrom."' ";



$result = mysql_query($sql) or die(mysql_error());

$orderQuantity =0;
$itemsQuantity = 0;

while($row = mysql_fetch_assoc($result))
{
    if(isset($orders[$row['ItemID']]['sold']))
    {
        $orders[$row['ItemID']]['sold'] += $row['quantity'];
    }
    else
    {
        $orders[$row['ItemID']]['sold'] = $row['quantity'];
        $orders[$row['ItemID']]['instock'] = $row['instock'];
        $orders[$row['ItemID']]['thumb'] = $row['thumb'];
        $orders[$row['ItemID']]['sku'] = $row['sku'];
        $orders[$row['ItemID']]['cost'] = $row['cost'];
        
    }
    
}

//var_dump($orders);


function cmp($a, $b) {
        return ($b["sold"] - $b["instock"]) - ($a["sold"] - $a["instock"]);
}
usort($orders, "cmp");  

    include_once("header.php");

    
 if($_POST['sku']!='')
 {
     foreach($_POST['sku'] as $sku=>$qty)
     {
         if($qty!='' && $qty>0)
          { 
             $poList[$sku] = $qty;
             $skuEx = explode('-', $sku);
         
             if (array_key_exists($skuEx[0], $locatinoPo)) {
                        $locatinoPo[$skuEx[0]] += $qty ;
                }
                else {
                     $locatinoPo[$skuEx[0]] = $qty ;
                }
            
             
          }   
     }
     
     // pull item infor from inventory DB and create po
     
     $str = "'";
     foreach ($locatinoPo as $location => $qty)
     {
         $str .= $location."','"; 
     }
     $str = rtrim($str,"'");
     $str = rtrim($str,",");
     
     $sql = "SELECT location, vendor,setof, vendorsId, cost, pn FROM inventory"
             ." WHERE vendor='MTC' and location IN (".$str.")";
     
     $result = mysql_query($sql) or die(mysql_error());
     
     echo 'MTC PO <textarea>';
     echo 'Part Number'."\t".'Quantity'."\t".'OE Part #'."\t".'COST'.PHP_EOL;
     
     while($row = mysql_fetch_assoc($result))
     {
         echo $row['vendorsId']."\t".$locatinoPo[$row['location']] * $row['setof'] ."\t".$row['pn']."\t".$row['cost'].PHP_EOL;
     }
     echo '</textarea>'; 

     // print_r($locatinoPo);
     
 }
 echo '<form action="" method="post">    <input type="submit" name="generate" value="GENERATE PO" >
';
 
foreach($orders as $key => $value)
  {      
    $pn = explode('-', $value['sku']);
    $pn = $pn[0];
    
    if($value['sold'] > $value['instock'])
        $stockbgCol = '#FFFF00';
    else
         $stockbgCol = '';
    
    
   
    
	?>

    <div class="order">
    	<table border="1" width="1000">
        <tr>
            <th>Thumb</th>
            <th>Listing #</th>
            <th>SKU</th>
            <th>Available Qty.</th>
            <th>Sold Qty past Month</th>
            <th>Cost</th>
            <th>Reorder</th>
        </tr>
        <tr align="center">
            <td> <img src="<?=$value['thumb']?>"></td>
            <td><?=$key?></td>
            <td><?=$value['sku']?></td>
            <td><?=$value['instock']?></td>
            <td><?=$value['sold']?></td>
            <td><?=$value['cost']?></td>
            <?PHP 
				$reorderQty = ($value['sold']-$value['instock'])+(int)($value['sold']/10);
			?>
            <td bgcolor="<?=$stockbgCol?>">
                <input type="text" size="8" name="sku[<?=$value['sku'].$key?>]"   id="<?=$value['sku']?>" partnumber="<?=$pn?>" <?PHP if($reorderQty>0) { ?>value="<?=$reorderQty?>" <?PHP } ?> >
                <input type="submit" name="" value="Reorder" onclick="addToOrder(<?=$pn?>)">
            </td>
            
        </tr>
        </table>
        
    </div>
   <br> 
	<?PHP
}

echo '</form>';
?>

<h2>Total Income: <?=$totalIncome?></h2>
<p>Orders qnt: <?PHP echo $orderQuantity; ?> </p>
<p>Sold Items qnt: <?PHP echo $itemsQuantity; ?> </p>
<p>Shipping fees are not excluded</p>