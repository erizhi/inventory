<?PHP

include_once("header.php");


//require_once "http.php";	
//require_once "simple_html_dom.php";
	
/*
http://www.ebay.com/sch/eBay-Motors-/6000/i.html?LH_BIN=1&LH_ItemCondition=3&_sop=15&_nkw=32411095526&LH_PrefLoc=1&_rdc=1

*/

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
	
        
        if($_POST['location']!='' && trim($_POST['location']) != 'URO' && $_POST['location']!='MTC')
        {
            mysql_query('UPDATE inventory SET qty="'.trim($_POST['instock']).'" WHERE location="'.$_POST['location'].'"');
            mysql_query('UPDATE '.$listingTable.' SET instock="'.trim($_POST['instock']).'" WHERE location="'.$_POST['location'].'"');
        }

        
}

if($_POST['sku']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET sku="'.trim($_POST['sku']).'" WHERE listingId="'.$_POST['id'].'"');
}

if($_POST['location']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET location="'.trim($_POST['location']).'" WHERE listingId="'.$_POST['id'].'"');
}

if($_POST['postage']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET postage="'.trim($_POST['postage']).'" WHERE listingId="'.$_POST['id'].'"');
}

if($_POST['algorithm']!='' && $_POST['id'])
{
	mysql_query('UPDATE '.$listingTable.' SET algorithm="'.$_POST['algorithm'].'" WHERE listingId="'.$_POST['id'].'"');
}


// << END OF UPDATE LISTING -----------------------------------------------------------------------------------------


// SELECT ALL LOCATIONS

$query = mysql_query("SELECT location FROM inventory");
while($row = mysql_fetch_array($query))
{
	$locationArray[] = $row['location'];
} 



// END OF SELECT ALL LOCATIONS


	$queryFilter = '';
	$orderBy = ' ORDER BY listingId DESC';
	
if($_REQUEST['filter']!='')
{

	if($_REQUEST['search_itemid']!='')
		$queryFilter.=' listingId='.trim($_REQUEST['search_itemid']);
	
	if($_REQUEST['search_sku']!='')
		$queryFilter.=' and sku LIKE "%'.trim($_REQUEST['search_sku']).'%"';
		
	if($_REQUEST['search_location']!='')
		$queryFilter.=' and location = "'.trim($_REQUEST['search_location']).'"';	
	
	if($_REQUEST['search_qty']!='')
		$queryFilter.=' and instock='. (int) trim($_REQUEST['search_qty']);
	
	if($_REQUEST['search_alg']!='')
		$queryFilter.=' and algorithm="'.$_REQUEST['search_alg'].'"';
		
	if($_REQUEST['search_title']!='')
		$queryFilter.=' and title LIKE "%'.trim($_REQUEST['search_title']).'%"';
		
	if($_REQUEST['search_key']!='')
		$queryFilter.=' and competKey LIKE "%'.trim($_REQUEST['search_key']).'%"';	
		
		if($queryFilter!='')
				{ 
					$queryFilter = trim($queryFilter);
				    $queryFilter = trim($queryFilter, 'and');
				
				 	$queryFilter = ' WHERE '. $queryFilter;
				}
		
}

$qry = mysql_query("SELECT count(*) as records FROM ".$listingTable.$queryFilter.$orderBy);
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

$qry = mysql_query($qString);


?>
<table border=1>
<tr>
<th>Thumb</th>
<th>Item id</th>
<th>SKU</th>
<th>LOCATION</th>
<th>Quantity in stock</th>
<th>Our Cost</th>
<th width="20">Shippgin Cost</th>
<th>our ebay price</th>
<th>lowest 1</th>
<th>lowest 2</th>
<th>lowest 3</th>
<th>lowest 4</th>
<th>lowest 5</th>
<th>lowest 6</th>
<th>lowest 7</th>
<th>Competition Algorithm</th>
<th>Search Compite</th>
<th>Title</th>
<th>Competition Keyword</th>
</tr>
<form action="" method="post">
<tr>
<th>
<input type="submit" name="filter" value="SEARCH">
<br>
<input type="reset" name="reset" value="Reset">
</th>
<th><input type="text" name="search_itemid" style="width:100px"></th>
<th><input type="text" name="search_sku"></th>
<th><input type="text" name="search_location" size="10">
</th>
<th><input type="text" name="search_qty" style="width:60px"></th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>
<select name="search_alg">
<option value="" > </option>
<option value="ignore" <?PHP if($row['algorithm'] == 'ignore') echo 'selected="selected"'; ?> >IGNORE</option>
<option value="bid_lowest" <?PHP if($row['algorithm'] == 'bid_lowest') echo 'selected="selected"'; ?> >BID 1-ST LOWEST</option>
<option value="bid_second_lowest" <?PHP if($row['algorithm'] == 'bid_second_lowest') echo 'selected="selected"'; ?> >BID 2-ND LOWEST</option>
<option value="bid_third_lowest" <?PHP if($row['algorithm'] == 'bid_third_lowest') echo 'selected="selected"'; ?> >BID 3-RD LOWEST</option>
<option value="bid_forth_lowest" <?PHP if($row['algorithm'] == 'bid_forth_lowest') echo 'selected="selected"'; ?> >BID 4-TH LOWEST</option>
<option value="bid_fivth_lowest" <?PHP if($row['algorithm'] == 'bid_fivth_lowest') echo 'selected="selected"'; ?> >BID 5-TH LOWEST</option>
<option value="bid_sixth_lowest" <?PHP if($row['algorithm'] == 'bid_sixth_lowest') echo 'selected="selected"'; ?> >BID 6-TH LOWEST</option>
<option value="bid_seventh_lowest" <?PHP if($row['algorithm'] == 'bid_seventh_lowest') echo 'selected="selected"'; ?> >BID 7-TH LOWEST</option>
<option value="match_lowest" <?PHP if($row['algorithm'] == 'match_lowest') echo 'selected="selected"'; ?> >MATCH 1-ST LOWEST</option>
<option value="match_second_lowest" <?PHP if($row['algorithm'] == 'match_second_lowest') echo 'selected="selected"'; ?> >MATCH 2-ND LOWEST</option>
<option value="match_third_lowest" <?PHP if($row['algorithm'] == 'match_third_lowest') echo 'selected="selected"'; ?> >MATCH 3-RD LOWEST</option>
<option value="match_forth_lowest" <?PHP if($row['algorithm'] == 'match_forth_lowest') echo 'selected="selected"'; ?> >MATCH 4-TH LOWEST</option>
<option value="match_fivth_lowest" <?PHP if($row['algorithm'] == 'match_fivth_lowest') echo 'selected="selected"'; ?> >MATCH 5-TH LOWEST</option>
<option value="match_sixth_lowest" <?PHP if($row['algorithm'] == 'match_sixth_lowest') echo 'selected="selected"'; ?> >MATCH 6-TH LOWEST</option>
<option value="match_seventh_lowest" <?PHP if($row['algorithm'] == 'match_seventh_lowest') echo 'selected="selected"'; ?> >MATCH 7-TH LOWEST</option>





<option value="go_crazy" <?PHP if($row['algorithm'] == 'go_crazy') echo 'selected="selected"'; ?> > GO CRAZY </option>
   
    </select>

</th>
<th>&nbsp;</th>
<th><input type="text" name="search_title"></th>
<th><input type="text" name="search_key"></th>
</tr>
</form>
<?PHP
$totalInventoryCost = 0;
$totalSalePrice = 0;
while($row = mysql_fetch_array($qry)) {
	
   $totalInventoryCost += ($row['cost'] * $row['instock']); 
   
   if($row['instock']>0)
           $totalSalePrice += ($row['instock'] * $row['activePrice']);
   
   
	 if($row['activePrice']<=$row['eprice1'])
             $bgCol =  '#00FF00';
         elseif($row['activePrice'])
             $bgCol = '#FF0000';
	
	
		
	if($row['instock']!=null && $row['instock']<3)
	    {
			$stockbgCol = '#FFFF00';
		}
		else
		 $stockbgCol = '';	
	
	
	echo '<tr><td><a href="'.$row['itemUrl'].'" target="_blank"><img src="'.$row['thumb'].'"></a></td><td>'
	.$row['listingId'].'</td><td><form action="" method="post">SKU: <input type="text" name="sku" value="'
	.$row['sku'].'"><input type="hidden" name="id" value="'
	.$row['listingId'].'"><input type="submit" value="Save"></form><td>LOCATION:';
	?>
    <form action="" method="post">
    <input type="hidden" name="id" value="<?=$row['listingId']?>">
    <select name="location" style="width:80px" >
    	<option value="" >NO INVENTORY</option>
        <?PHP foreach($locationArray as $location) { ?>
    		<option value="<?=$location?>" <?PHP if($row['location']==$location) { ?> selected= "selected"<?PHP } ?>><?=$location?></option>
            
		<?PHP } ?>
    </select><br>
    <input type="submit" value="Save">
    </form>
	<?PHP
	echo '</td><td  bgcolor="'.$stockbgCol .'" ><form action="" method="post">Qty: <input type="text" name="instock" style="width:60px" value="'
	.$row['instock'].'"><input type="hidden" name="location"  value="'
	.$row['location'].'"><input type="hidden" name="id" value="'
	.$row['listingId'].'"><br><input type="submit"  value="Save"></form></td><td><form action=""  method="post"> $<input style="width:60px" type="text" name="cost" value="'
	.$row['cost'].'"><input type="hidden" name="id" value="'
	.$row['listingId'].'"><input type="submit" value="Save"></form></td><td><form action="" method="post"> $<input type="text" style="width:60px" name="postage" value="'
	.$row['postage'].'"><input type="hidden" name="id" value="'
	.$row['listingId'].'"><br><input type="submit" value="Save"></form></td><td bgcolor="'.$bgCol .'">$'
	.$row['activePrice'].'</td><td><a target="_blank" href="'.$row['compet1'].'">$'.$row['eprice1']
	.'</a></td><td><a target="_blank" href="'.$row['compet2'].'">$'.$row['eprice2'].'</a></td><td><a href="'
	.$row['compet3'].'">$'.$row['eprice3'].'</a></td><td>$'.$row['eprice4'].'</td><td>$'.$row['eprice5'].'</td><td>$'.$row['eprice6'].'</td><td>$'.$row['eprice7'].'</td>';
		?>
	<td>
    <form action="" method="post">
    <select name="algorithm" onChange="this.form.submit()">
    	<option value="ignore">Ignore</option>
	<option value="bid_lowest" <?PHP if($row['algorithm'] == 'bid_lowest') echo 'selected="selected"'; ?> >BID 1-ST LOWEST</option>
        <option value="bid_second_lowest" <?PHP if($row['algorithm'] == 'bid_second_lowest') echo 'selected="selected"'; ?> >BID 2-ND LOWEST</option>
        <option value="bid_third_lowest" <?PHP if($row['algorithm'] == 'bid_third_lowest') echo 'selected="selected"'; ?> >BID 3-RD LOWEST</option>
        <option value="bid_forth_lowest" <?PHP if($row['algorithm'] == 'bid_forth_lowest') echo 'selected="selected"'; ?> >BID 4-TH LOWEST</option>
        <option value="bid_fifth_lowest" <?PHP if($row['algorithm'] == 'bid_fifth_lowest') echo 'selected="selected"'; ?> >BID 5-TH LOWEST</option>
        <option value="bid_sixth_lowest" <?PHP if($row['algorithm'] == 'bid_sixth_lowest') echo 'selected="selected"'; ?> >BID 6-TH LOWEST</option>
        <option value="bid_seventh_lowest" <?PHP if($row['algorithm'] == 'bid_seventh_lowest') echo 'selected="selected"'; ?> >BID 7-TH LOWEST</option>
        <option value="match_lowest" <?PHP if($row['algorithm'] == 'match_lowest') echo 'selected="selected"'; ?> >MATCH 1-ST LOWEST</option>
        <option value="match_second_lowest" <?PHP if($row['algorithm'] == 'match_second_lowest') echo 'selected="selected"'; ?> >MATCH 2-ND LOWEST</option>
        <option value="match_third_lowest" <?PHP if($row['algorithm'] == 'match_third_lowest') echo 'selected="selected"'; ?> >MATCH 3-RD LOWEST</option>
    	<option value="match_forth_lowest" <?PHP if($row['algorithm'] == 'match_forth_lowest') echo 'selected="selected"'; ?> >MATCH 4-TH LOWEST</option>
    	<option value="match_fifth_lowest" <?PHP if($row['algorithm'] == 'match_fifth_lowest') echo 'selected="selected"'; ?> >MATCH 5-TH LOWEST</option>
    	<option value="match_sixth_lowest" <?PHP if($row['algorithm'] == 'match_sixth_lowest') echo 'selected="selected"'; ?> >MATCH 6-TH LOWEST</option>
    	<option value="match_seventh_lowest" <?PHP if($row['algorithm'] == 'match_seventh_lowest') echo 'selected="selected"'; ?> >MATCH 7-TH LOWEST</option>
        <option value="go_crazy" <?PHP if($row['algorithm'] == 'go_crazy') echo 'selected="selected"'; ?> > GO CRAZY </option>
    </select>
    <input type="hidden" name="id" value="<?=$row['listingId']?>">
    </form>
    </td>
    <?PHP
    echo '<td><a href="http://www.ebay.com/sch/eBay-Motors-/6000/i.html?LH_BIN=1&LH_ItemCondition=3&_sop=15&_nkw='
	.$row['competKey'].'&LH_PrefLoc=1&LH_TitleDesc=1&_rdc=1&_udlo='.$row['cost'].'&_udhi=" target="_blank">Search / Compite</a></td><td>'
	.$row['title'].'</td><td><form action="" method="post"><input type="text" name="competKey" value="'
	.$row['competKey'].'"><input type="hidden" name="id" value="'
	.$row['listingId'].'"><input type="submit" value="Save"></form></td></tr>';
	
	//$row['algorithm']
	
	}

echo '</table>';
	
?>
