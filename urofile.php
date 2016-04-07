<?PHP

	
require_once('dbConf.php');

	
	
			//$itemId  = '251394146146';
	
	include_once("header.php");
	
		// DELETE LOCATION
	if($_POST['clearnotice'] != '')
	{
		echo '<h2>NOTICE CLEARED</h2>';		
		mysql_query("UPDATE settings SET value=''  WHERE name='urofileNotice'");
	}
	
	if($_POST['clearnewitems'] != '')
	{
		echo '<h2>NOTICE NEW ITEMS CLEARED</h2>';		
		mysql_query("UPDATE settings SET value=''  WHERE name='urofileNewItemNotice'");
	}
	
	if($_POST['clearinstock'] != '')
	{
		echo '<h2>NOTICE IN STOCK CLEARED</h2>';		
		mysql_query("UPDATE settings SET value=''  WHERE name='urofileInstock'");
	}
	
	if($_POST['clearbackorder'] != '')
	{
		echo '<h2>NOTICE BACK ORDERS CLEARED</h2>';		
		mysql_query("UPDATE settings SET value=''  WHERE name='urofileBackOrder'");
	}
	
	
	
	
	
	$result = mysql_query("SELECT value FROM settings WHERE name='urofileNotice'");
	$row = mysql_fetch_assoc($result);
	
	$result = mysql_query("SELECT value FROM settings WHERE name='urofileNewItemNotice'");
	$newItem = mysql_fetch_assoc($result);
	
	$result = mysql_query("SELECT value FROM settings WHERE name='urofileInstock'");
	$inStock = mysql_fetch_assoc($result);
	
	$result = mysql_query("SELECT value FROM settings WHERE name='urofileBackOrder'");
	$backOrder = mysql_fetch_assoc($result);
	
	
	?>
    <br>
    <script  type="text/javascript">


function delete_notice()
{
	r = confirm('ARE YOYU SURE ABOUT CLEARING NOTICE?');
if (r==true)
  {
	  document.getElementById("clearNoticeForm").submit();
	  x="CLEAR NOTICE";
  }
else
  {
	  x="Cancel clean!";	  
  } 
}



function delete_newItem()
{
	r = confirm('ARE YOYU SURE ABOUT CLEARING NEW ITEM NOTICE?');
if (r==true)
  {
	  document.getElementById("clearNewItemForm").submit();
	  x="CLEAR NOTICE";
  }
else
  {
	  x="Cancel clean!";	  
  } 
}

function delete_instock()
{
	r = confirm('ARE YOYU SURE ABOUT CLEARING IN STOCK NOTICE?');
if (r==true)
  {
	  document.getElementById("clearInstock").submit();
	  x="CLEAR NOTICE";
  }
else
  {
	  x="Cancel clean!";	  
  } 
}

function delete_backorder()
{
	r = confirm('ARE YOYU SURE ABOUT CLEARING BACKORDER NOTICE?');
if (r==true)
  {
	  document.getElementById("clearBackOrderForm").submit();
	  x="CLEAR NOTICE";
  }
else
  {
	  x="Cancel clean!";	  
  } 
}

</script>

    NOTICE:
    <textarea cols="80"><?=$row['value']?></textarea>
    <form action="" id="clearNoticeForm" name="clearNoticeForm" method="post">
   	 	<input type="button" onclick="delete_notice()" name="clearNotice" value="CLEAR NOTICE">
        <input type="hidden" name="clearnotice" value="yes">
    </form>
    <br><br>
    NEW ITEMS
    <textarea cols="80"><?=$newItem['value']?></textarea>
    <form action="" id="clearNewItemForm" name="clearNewItemForm" method="post">
   	 	<input type="button" onclick="delete_newItem()" name="clearNewItem" value="CLEAR NOTICE">
        <input type="hidden" name="clearnewitems" value="yes">
    </form>
    <br><br>
    
    IN STOCK AVAILABLE
    <textarea cols="80"><?=$inStock['value']?></textarea>
    <form action="" id="clearInstock" name="clearInstock" method="post">
   	 	<input type="button" onclick="delete_instock()" name="clearinstock" value="CLEAR NOTICE">
        <input type="hidden" name="clearinstock" value="yes">
    </form>
    <br><br>
    
    BACKORDER
    <textarea cols="80"><?=$backOrder['value']?></textarea>
    <form action="" id="clearBackOrderForm" name="clearBackOrderForm" method="post">
   	 	<input type="button" onclick="delete_backorder()" name="clearbackorder" value="CLEAR BACKORDER NOTICE">
        <input type="hidden" name="clearbackorder" value="yes">
    </form>
    <br><br>
    
    <br>
    <?PHP
	
	$orderBy = 'ORDER BY dateTime DESC ';
	
	

	$searchWhere = '';
	
	if($_POST['searchSubmit']!='')
	{
		$searchWhere = ' WHERE ';
		
		if($_POST['upc']!='')
			$searchWhere .=' uro.upc="'.$_POST['upc'].'" and';
		if($_POST['partNo']!='')
			$searchWhere .=' uro.partNo="'.$_POST['partNo'].'" and';
		if($_POST['onHand']!='')
			$searchWhere .=' uro.onHand="'.$_POST['onHand'].'" and';
		if($_POST['title']!='')
			$searchWhere .=' uro.title LIKE "%'.$_POST['title'].'%" and';
		
		if($_POST['costIs'] == 'lowerLawest')	
			$searchWhere .=' uro.price < uro.eprice1 and';
			
		if($_POST['costIs'] == 'markup20')	
			$searchWhere .=' uro.price < (uro.eprice1-uro.eprice1*0.2-3)  and';
			
		if($_POST['costIs'] == 'markup30')	
			$searchWhere .=' uro.price < (uro.eprice1-uro.eprice1*0.3-3)  and';
			
		if($_POST['costIs'] == 'higher50')	
			$searchWhere .=' uro.price > 50 and';	
			
		if($_POST['fitment']=='yes')
			$searchWhere .=' fit.upc IS NOT NULL and';
			
		if($_POST['fitment']=='no')
			$searchWhere .=' fit.upc IS NULL and';		
		
		if($_POST['listed']!='')
			$searchWhere .=' fit.listed = "'.$_POST['listed'].'" and';	
			
		if($_POST['totalSold']!='')	
			$searchWhere .=' (IFNULL(uro.u1sold, 0) + IFNULL(uro.u2sold, 0) + IFNULL(uro.u3sold, 0)) > '.$_POST['totalSold'].' and';
			
			$searchWhere = rtrim($searchWhere,'and');
			
	}
	
	// CREATING NEW LOCATION
	
	if($_POST['newLocation']!='')
	{
		echo '<h2>location: '.$_POST['deletelocation'].' has been removed</h2>';
		
		mysql_query("INSERT INTO uro_inventory SET location='".trim($_POST['newLocation'])."', pn='".trim($_POST['newPN'])."', opn='".trim($_POST['newOPN'])."', qty='".trim($_POST['newQty'])."', cost='".trim($_POST['newCost'])."', brand='".trim($_POST['newBrand'])."', vendor='".trim($_POST['newVendor'])."', vendorsId='".trim($_POST['newVendorsId'])."', notes='".trim($_POST['newNotes'])."'");
		
		
	}
	
	
	$desc = ($_GET['order'] == 'DESC') ? 'DESC' : 'ASC';
	
	
	if($_GET['by']!='')
		$orderBy = ' ORDER BY '.addslashes($_GET['by']).' '.addslashes($desc);
		
	$desc = ($_GET['order'] == 'DESC') ? 'ASC' : 'DESC' ;
	
		
	$totalCost = 0;
			$sql = "SELECT fit.partNo as partFit,fit.listed as isListed, uro.upc, uro.partNo, uro.picture, uro.price, uro.title, uro.shortDesc, uro.onHand, uro.height, uro.width, uro.length, uro.weight, uro.url1, uro.url2, uro.url3, uro.u1sold, uro.u2sold, uro.u3sold, uro.eprice1, uro.eprice2, uro.eprice3  FROM uro_inventory as uro LEFT JOIN urofitment as fit ON uro.upc = fit.upc ".$searchWhere.$orderBy;
			
			//echo $sql.PHP_EOL.'<br>';
			$result = mysql_query($sql) or die(mysql_error());
			
			echo '<table border="1"> <tr align="center"><th><a href="?by=upc&order='.$desc.'">UPC</a></th><th>PART #</th>'
			.'<th><a href="?by=price&order='.$desc.'">COST</a></th><th><a href="?by=onHand&order='.$desc.'">ON Hand</a></th>'
			.'<th><a href="?by=title&order='.$desc.'">TITLE</a></th>'
			.'<th>WEIGHT</th><th>HEIGHT</th><th>WIDTH ID</th><th>LENGTH</th><th>FITMENT AVAILABLE</th><th>IS LISTED?</th><th>SEARCH</th>'
			.'<th>URL 1</th><th>URL 2</th><th>URL 3</th><th>TOTAL SOLD</th></tr>';
			
			?>
           <form action="" method="post" name="searchForm">
            <tr align="center"> <th><input type="text" name="upc"></th><th><input type="text" name="partNo"></th>'
			.'<th><select name="costIs"><option value=""></option><option value="lowerLawest"> < Lowest</option>
            <option value="higher50"> > $50 </option>
            <option value="markup20"> > 20% Markup  </option>
            <option value="markup30"> > 30% Markup  </option>
            </select></th><th><input type="text" name="onHand"></th>'
			.'<th><input type="text" name="title"></th>'
			.'<th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th>
            <th><select name="fitment"><option value=""> &nbsp;</option><option value="no">no</option><option value="yes">yes</option></select></th>
             <th><select name="listed"><option value=""> &nbsp;</option><option value="no">no</option><option value="yes">yes</option></select></th>
             <th>&nbsp;</th>'
			.'<th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th> >= <input type="text" name="totalSold" value="" size="3"> <br><input type="submit" name="searchSubmit" value="SEARCH"></th></tr>
            </form>
            <?PHP
			
			while($row = mysql_fetch_assoc($result))
			{
				$upc = $row['upc'];	
				$quantity = $row['onHand'];
				$price = $row['price'];
				$pn = $row['partNo'];
				
				
			$fitmentAvailable = ($row['partFit']!='') ? 'YES' : 'NO';
			
			echo '<tr><td>'.$upc.'<td>'.$pn.'</td>  <td>$'.$price.'</td> <td>'.$quantity.'</td><td>'
			.$row['title'].'</td><td> '.$row['weight'].'</td><td> '.$row['length'].'</td><td> '
			.$row['width'].'</td><td> '
			.$row['length'].'</td><td>'.$fitmentAvailable.'</td><td>'.$row['isListed']
			.'</td><td><a href="http://www.ebay.com/sch/eBay-Motors-/6000/i.html?LH_BIN=1&LH_ItemCondition=3&_sop=15&_nkw='
	.str_replace(' ', '', $row['partNo']).'&LH_PrefLoc=1&LH_TitleDesc=1&_rdc=1&_udhi=" target="_blank">search</a></td>'
	.'<td>'.$row['eprice1'].' <a href="'.$row['url1'].'">link</a></td><td>'.$row['eprice2'].'<a href="'.$row['url2'].'"> link</a></td><td>'.$row['eprice3'].' <a href="'.$row['url3'].'">link</a></td><td>'
	.($row['u1sold']+$row['u2sold']+$row['u3sold']).'</td></tr>';
			
			
			}
			
			echo '</table>';
			
	
			

	

?>






<?PHP include_once("footer.php"); ?>