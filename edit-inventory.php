<?PHP

	
	include_once(dbConf.php);
	
	include_once("header.php");
	
	$orderBy = 'ORDER BY LOCATION ASC';
	
	
	// DELETE LOCATION
	if($_POST['deletelocation']!='')
	{
		echo '<h2>location: '.$_POST['deletelocation'].' has been removed</h2>';
		
		mysql_query("DELETE FROM inventory WHERE location='".trim($_POST['deletelocation'])."'");
	}
	

	
	if($_POST['UPDATE']=='UPDATE')
	{
		$sql = "UPDATE inventory SET pn='".trim($_POST['newPN'])."', opn='".trim($_POST['newOPN'])."', qty='"
                        .trim($_POST['newQty'])."', cost='".trim($_POST['newCost'])."', brand='".trim($_POST['newBrand'])."', vendor='".trim($_POST['newVendor'])."', vendorsId='"
                        .trim($_POST['newVendorsId'])."', setof='".trim($_POST['setof'])."', notes='".trim($_POST['newNotes'])."' WHERE location = '".trim($_POST['location'])."'";
		
		mysql_query($sql) or die(mysql_error());
	}
	
	if($_REQUEST['location']!='')
	{
		$sql = "SELECT * FROM inventory WHERE location='".$_REQUEST['location']."'";
			
			
		$result = mysql_query($sql) or die(mysql_error());
		
		$location = mysql_fetch_array($result);
		
		
	}
	
	
	
	?>
    

<script language="javascript">

function delete_location(location)
{
	r = confirm(location);
if (r==true)
  {
	  document.getElementById("delete"+location).submit();
	  x="Location "+location+" has been removed";
  }
else
  {
	  alert("Cancel removal!");	  
  } 
  

}
</script>



<form action="" method="post" name="updateLocation">
<input type="hidden" name="location" value="<?=$location['location']?>">
<table>
<tr><td>Location:</td><td><?=$location['location']?></td></tr>
<tr><td>Cost:</td><td><input type="text" name="newCost" value="<?=$location['cost']?>"></td></tr>
<tr><td>QUANTITY: </td><td><input type="text" name="newQty" value="<?=$location['qty']?>"></td></tr>
<tr><td>PART #:</td><td><input type="text" name="newPN" value="<?=$location['pn']?>"></td></tr>
<tr><td>OTHER PART #:</td><td><input type="text" name="newOPN" value="<?=$location['opn']?>"></td></tr>
<tr><td>BRAND:</td><td><input type="text" name="newBrand" value="<?=$location['brand']?>"></td></tr>
<tr><td>Vendors ID:</td><td> 
    <input type="text" name="newVendorsId" value="<?=$location['vendorsId']?>">
     SET OF <input type="text" name="setof" value="<?=$location['setof']?>">
    </td></tr>

<tr><td>VENDOR:</td><td>
<select name="newVendor">
	<option value="MTC" <?PHP echo ($location['vendor'] == 'MTC') ? 'selected="selected"' : ''; ?> >MTC PARTS</option>
	<option value="APA" <?PHP echo ($location['vendor'] == 'APA') ? 'selected="selected"' : 'URO PARTS'; ?>>URO PARTS</option>
	<option value="IMC" <?PHP echo ($location['vendor'] == 'IMC') ? 'selected="selected"' : 'IMC'; ?>>IMC</option>
</select></td></tr>

<tr><td>NOTES:</td><td>	<textarea name="newNotes"><?=$location['notes']?></textarea></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" value="UPDATE" name="UPDATE"></td></tr>

</table>



</form>

