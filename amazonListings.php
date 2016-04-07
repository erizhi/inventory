<?PHP

require_once("dbConf.php");
require_once('ams_part4motor_conf.php');
	
	
$listingTable = ACTIVE_LISTING_TABLE;
        
include_once("header.php");



// UPDATE //////////////////////////////////////////////////////////////////////////////////////////////////////////
if($_POST['competKey']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET competKey="'.$_POST['competKey'].'" WHERE listingId="'.$_POST['id'].'"');
}

if($_POST['cost']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET cost="'.$_POST['cost'].'" WHERE listingId="'.$_POST['id'].'"');
}


if($_POST['instock']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET instock="'.trim($_POST['instock']).'" WHERE listingId="'.$_POST['id'].'"');
	
	mysql_query('UPDATE inventory SET qty="'.trim($_POST['instock']).'" WHERE location="'.$_POST['location'].'"');
}

if($_POST['sellerSku']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET sellerSku="'.trim($_POST['sku']).'" WHERE listingId="'.$_POST['id'].'"');
}

if($_POST['location']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET location="'.trim($_POST['location']).'" WHERE listingId="'.$_POST['id'].'"');
}

if($_POST['postage']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET postage="'.trim($_POST['postage']).'" WHERE listingId="'.$_POST['id'].'"');
}

if($_POST['vendorId']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET vendorId="'.$_POST['vendorId'].'" WHERE listingId="'.$_POST['id'].'"');
}


// SELECT ALL LOCATIONS

$query = mysql_query("SELECT location FROM inventory");
while($row = mysql_fetch_array($query))
{
	$locationArray[] = $row['location'];
} 


$queryFilter = '';
	$orderBy = ' ORDER BY listingId DESC';
	
if($_REQUEST['filter']!='')
{

	if($_REQUEST['search_itemid']!='')
		$queryFilter.=' listingId='.trim($_REQUEST['search_itemid']);
	
	if($_REQUEST['search_sku']!='')
		$queryFilter.=' and sellerSku LIKE "%'.trim($_REQUEST['search_sku']).'%"';
		
	if($_REQUEST['search_location']!='')
		$queryFilter.=' and location = "'.trim($_REQUEST['search_location']).'"';	
	
	if($_REQUEST['search_qty']!='')
		$queryFilter.=' and quantity='. (int) trim($_REQUEST['search_qty']);
	
        
        if($_REQUEST['search_offers']!='')
		$queryFilter.=' and newOffers='. (int) trim($_REQUEST['search_offers']);
        
	if($_REQUEST['search_asin1']!='')
		$queryFilter.=' and asin1="'.$_REQUEST['search_asin1'].'"';
		
	if($_REQUEST['search_title']!='')
		$queryFilter.=' and itemName LIKE "%'.trim($_REQUEST['search_title']).'%"';
		
	if($_REQUEST['search_listing_id']!='')
		$queryFilter.=' and listingId = "'.trim($_REQUEST['search_listing_id']).'"';	
		
		if($queryFilter!='')
				{ 
					$queryFilter = trim($queryFilter);
				    $queryFilter = trim($queryFilter, 'and');
				
				 	$queryFilter = ' WHERE '. $queryFilter;
				}
		
}

$qry = mysql_query("SELECT count(*) as records FROM ".$listingTable.$queryFilter.$orderBy) or die(mysql_error());
$num_rows = mysql_fetch_assoc($qry);


//echo $num_rows['records'];

$itemPerPage = 20;
$j = 0;
while ($j * $itemPerPage < $num_rows['records'])
{
	echo '<a href="?page='.$j.'&search_itemid='.$_REQUEST['search_itemid'].'&search_sku='.$_REQUEST['search_sku']
	.'&search_qty='.$_REQUEST['search_qty'].'&search_alg='.$_REQUEST['search_alg']
	.'&search_title='.$_REQUEST['search_title'].'&search_key='.$_REQUEST['search_key'].'&filter=search">'.$j.'</a> | ';
	$j++;
}

echo '<br><br>';

$page = ($_GET['page']!='') ?  $_GET['page'] : 0;


$limit = " LIMIT ".$page*$itemPerPage  . ", ". $itemPerPage ;

$qString = "SELECT * FROM ".$listingTable.$queryFilter.$orderBy.$limit;

$qry = mysql_query($qString) or die(mysql_error());

?>


<table width="900" border="1">
<tr>
    <th># </th>
    <th>TITLE </th>
    <th>ASIN </th>
    <th>LISTING ID </th>
    <th>SKU </th>
    <th>LOCATION </th>
    <th width="10">Offers # </th>
    <th>Lowest Landing Price </th>
    <th>Lowest Price  </th>
    <th>Lowest Shipping  </th>
    <th>VENDORs ID </th>
    <th>PRICE </th>
    <th>QUANTITY </th>
    <th>PRODUCT ID </th>
    <th>OPEN DATE</th>
</tr>
<form action="" method="post">
<tr>
    <th>
        <input type="submit" name="filter" value="SEARCH">
<br>
<input type="reset" name="reset" value="Reset">
    </th>
    <td><input type="text" name="search_title"></td>
    <td><input type="text" name="search_asin1"></td>
    <td><input type="text" name="search_listing_id"></td>
    <td><input type="text" name="search_sku"></td>
    <td><input type="text" name="search_location" style="width:40px"></td>
     <td><input type="text" name="search_offers"  style="width:20px"></td>
      <td><input type="text" name="search_landingprice"style="width:20px"></td>
       <td><input type="text" name="search_lowestprice" style="width:20px"></td>
        <td><input type="text" name="search_lowestShipping" style="width:20px"></td>
    <td><input type="text" name="search_vendor_id" style="width:60px"></td>
    <td>&nbsp;</td>
    <td><input type="text" name="search_qty" style="width:60px"></td>
    <td><input type="text" name="search_product_id"></td>
    <td>OPEN DATE</td>
</tr>
</form>
<?PHP while($row = mysql_fetch_array($qry)){ ?>
<tr>
    <td><?=$row['id']?></td>
    <td><?=$row['itemName']?></td>
    <td><?=$row['asin1']?></td>
    <td><?=$row['listingId']?></td>
    <td><?=$row['sellerSku']?></td>
    <td>
    
    <form action="" method="post">
    <input type="hidden" name="id" value="<?=$row['listingId']?>">
    <select name="location">
    	<option value="">NO INVENTORY</option>
        <?PHP foreach($locationArray as $location) { ?>
    		<option value="<?=$location?>" <?PHP if($row['location']==$location) { ?> selected= "selected"<?PHP } ?> style="width:20px"><?=$location?></option>
            
		<?PHP } ?>
    </select>
    <input type="submit" value="Save">
    </form>
    </td>
    <td><?=$row['newOffers']?></td>
    <td><?=$row['lowestLandedPrice']?></td>
    <td <?PHP if($row['lowestListingPrice']==$row['price']) echo 'bgcolor="#7FFFD4"'; elseif($row['lowestListingPrice'] < $row['price']) echo 'bgcolor="#FFC0CB"'; ?>><?=$row['lowestListingPrice']?></td>
    <td><?=$row['lowestShippingPrice']?></td>
    <td> 
       <form action="" method="post">
    <input type="hidden" name="id" value="<?=$row['listingId']?>"> <input style="width:100px" type="text" name="vendorId" value="<?=$row['vendorId']?>">
    <input type="submit" value="Save">
    </form>
    </td>
    <td><?=$row['price']?></td>
    <td><?=$row['quantity']?></td>
    <td><?=$row['productId']?></td>
    <td><?=$row['openDate']?></td>
</tr>
<?PHP } ?>

</table>


    
<?PHP

include_once("footer.php");


?>
