<style>
.order {border: #00F solid 5px}
</style>

<?PHP
include_once("header.php");

$totalIncome = 0;

$CreateTimeFrom = ($_POST['start']!='') ? $_POST['start'] : gmdate("Y-m-d\TH:i:s", time()- 60*60*24); //current time minus 24 hours minutes
$CreateTimeTo = ($_POST['end']!='') ? $_POST['end'] : gmdate("Y-m-d\TH:i:s");



?>

<form action="" method="post">
<input type="text" name="start" value="<?=gmdate("Y-m-d\TH:i:s", time()- 60*60*24)?>">
<br />
<input type="text" name="end" value="<?=gmdate("Y-m-d\TH:i:s")?>">
<br>
<input type="submit" value="Search">
</form>

<?PHP
$sql = "SELECT * FROM ".$ordersTable
." AS orders LEFT JOIN ".$transactionsTable." AS transactions ON orders.OrderID = transactions.orderId "
."LEFT JOIN ".$listingTable." AS active ON transactions.ItemID = active.listingId "
." WHERE orders.date >= '".$CreateTimeFrom."' and orders.date < '".$CreateTimeTo."' and orders.status = 'Completed' order by orders.id desc";



$result = mysql_query($sql) or die(mysql_error());

$orderQuantity =0;
$itemsQuantity = 0;

while($row = mysql_fetch_assoc($result))
{
	$orderQuantity++;
	?>
    
    <div class="order">
    	<table border="1" width="1000">
        <tr>
        	<th>Order ID</th>
        	<th>Total</th>
            <th>Sales Tax</th>
            <th width="200">Name /Address</th>       
            <th width="120">Phone</th>
            <th width="200">Note</th>
            <th width="200">Paypal Transaction</th>
            <th>Status</th>
        </tr>
        <tr align="center">
        	<td> <?=$row['OrderID']?></td>
            <td>$<?=$row['AmountPaid']?></td>
            <td>$<?=$row['SalesTax']?></td>
            <td>
            	<?=$row['shipName']?><br>
				<?=$row['street1']?><br>
				<?=$row['street2']?><br>
                <?=$row['city']?>, 
                <?=$row['state']?>
                <?=$row['zip']?><br>
                <?=$row['country']?>
            </td>
            <td><?=$row['phone']?></td>
            <td style="color:#F00">
				<?=$row['note']?>&nbsp;
            </td>
            <td><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=<?=$row['ExternalTransactionID']?>" target="_blank"><?=$row['ExternalTransactionID']?></a></td>
            <td><?=$row['status']?></td>

        </tr>
        </table>
        
        Transactions:<br>
        <table width="1000" border="1">
        <tr>
            <th>TransactionID</th>
            <th>SKU</th>
            <th>Item ID</th>
            <th width="200">Title</th>
            <th>Quantity</th>
            <th>Transaction Price</th>
            <th>Cost</th>
            <th>Margin</th>
            <th>Email</th>
            <th>Variation SKU</th>
        </tr>
        <tr align="center">
            <td><?=$row['TransactionID']?></td>
            <td><?=$row['SKU']?></td>
            <td><?=$row['ItemID']?></td>
            <td><?=$row['title']?></td>
            <td><?=$row['quantity']?></td>
            <td>$<?=$row['TransactionPrice']?></td>
            <td>$<?=$row['cost']?></td>
            <td>$<?PHP $margin =($row['TransactionPrice']-$row['cost']); echo $margin; ?></td>
            <td><?=$row['email']?></td>
            <td><?=$row['VariationSKU']?>&nbsp;</td>
        </tr>
        </table>
        <br>
        <b>Paypal fee:</b> $<?=$row['ExternalFeeOrCredit']?> <br>
        <b>Ebay fee:</b> $<?PHP 
				$ebayFee = $row['AmountPaid']/10; 
				//top rated discount
				$ebayFee = $ebayFee-$ebayFee*0.2 ;
				
				echo $ebayFee;
				
				 ?>
         <br>
        <b>Estimated postage fee:</b> $<?=$row['postage']?> <br>
        
               
        <h3><b>Sales Income:</b> $<?PHP 
		$salesIncome =($row['AmountPaid'] - $ebayFee - $row['ExternalFeeOrCredit'] - $row['quantity'] * $row['cost']-$row['SalesTax'] - $row['postage']);
		echo $salesIncome;
		$itemsQuantity += $row['quantity'];
		?></b></h3> (All fees excluded)
    </div>
   <br> 
	<?PHP
	$totalTotal += $row['AmountPaid'];
	$totalIncome+=$salesIncome;
	$totalPostage+=$row['postage'];
	$totalSalesTax+=$row['SalesTax'];
	$paypalFees+=$row['ExternalFeeOrCredit'];
	$ebayFees += $ebayFee;
	$cog += $row['quantity'] * $row['cost'];
	echo '<br><hr>';
}

?>
<p>Within Seelcted Date Range. </p>
<p>Orders qnt: <?PHP echo $orderQuantity; ?> </p>
<p>Sold Items qnt: <?PHP echo $itemsQuantity; ?> </p>

<h2>Total Invoice: <?=$totalTotal?></h2>
<h3>COGS: <?=$cog?></h3>
<h3>CA SALES TAX: <?=$totalSalesTax?></h3>

<h3>ebay Top Rated Fees:  <?=$ebayFees ?> </h3>
<h3>Paypal Fees: <?=$paypalFees?></h3>
<h3>ESTIMATED SHIPPING FEES <?=$totalPostage?></h3>
<h2>ESTIMATED INCOME AFTER ALL FEES <b><?=($totalTotal-$cog-$ebayFees-$totalSalesTax-$paypalFees-$totalPostage)?></b></h2>
