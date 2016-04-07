<html>
<?PHP
require_once('dbConf.php');

	
	
$queryFilter = '';

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

$qry = mysql_query("SELECT count(*) as records FROM pepauto_active" .$queryFilter);
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


$page = ($_GET['page']!='') ?  $_GET['page'] : 0;


$limit = " LIMIT ".$page*$itemPerPage  . ", ".$itemPerPage ;

$qry = mysql_query("SELECT * FROM pepauto_active ".$queryFilter.$limit);

include_once("header.php");
?>



  
  
<table border="1" >
<tr>
<th>Thumb</th>
<th>Item id</th>
<th>SKU</th>
<th>LOCATION</th>
<th>QTY</th>
<th width="600">Stats</th>
<th>Com Algorithm</th>
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
<th><input type="text" name="search_location" size="5"></th>
<th><input type="text" name="search_qty" style="width:60px"></th>
<th>&nbsp;</th>
<th>&nbsp;


</th>
<th>&nbsp;</th>
<th><input type="text" name="search_title"></th>
<th><input type="text" name="search_key"></th>
</tr>
</form>


<?
$totalInventoryCost = 0;
$totalSalePrice = 0;
while($row = mysql_fetch_array($qry)){
	
	$itemArray[] = $row['listingId'];
	
   $totalInventoryCost += ($row['cost'] * $row['instock']); 
   
   if($row['instock']>0)
           $totalSalePrice += ($row['instock'] * $row['activePrice']);
   
   
	 if($row['activePrice']<=$row['eprice1'])
             $bgCol =  '#00FF00';
         elseif($row['activePrice'])
             $bgCol = '#FF0000';
	
	if($row['eprice2']-$row['activePrice']>0.1)
	    {
			$bgCol = '#00BFFF';
		}
		
	if($row['instock']!=null && $row['instock']<3)
	    {
			$stockbgCol = '#FFFF00';
		}
		else
		 $stockbgCol = '';	
	
	
	echo '<tr><td><a href="'.$row['itemUrl'].'" target="_blank"><img src="'.$row['thumb'].'"></a></td><td>'
	.$row['listingId'].'</td><td>'.$row['sku'].'</td><td>'.$row['location'].'</td><td  bgcolor="'.$stockbgCol .'" >'
	.$row['instock'].'</td><td><div data="" id="chart'.$row['listingId'].'" style="height: 300px; width: 100%;">
  </div> </td>';
		?>
	<td>
    
    &nbsp;
    <?=$row['algorithm']?>
    
    </td>
    <?PHP
    echo '<td><a href="http://www.ebay.com/sch/eBay-Motors-/6000/i.html?LH_BIN=1&LH_ItemCondition=3&_sop=15&_nkw='
	.$row['competKey'].'&LH_PrefLoc=1&LH_TitleDesc=1&_rdc=1" target="_blank">Search / Compite</a></td><td>'
	.$row['title'].'</td><td>'	.$row['competKey'].'</td></tr>';
	
	//$row['algorithm']
	
	}

echo '</table>';


?>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" ></script>
<script type="text/javascript" src="canvasjs.min.js"></script>

  <script type="text/javascript">
		$(document).ready(function () {
		<?PHP foreach($itemArray as $itemId) {?>
				plotItemChart(<?=$itemId?>);
		<?PHP } ?>
		});





function plotItemChart(itemId) { 


  $.getJSON (   "chartData.php?itemId="+itemId,    
        function ( returnData ) {
	   
	  // var plotdata = eval ("(" + returnData + ")"); 
	       
		   
		   

		   
		   
	$("#chart"+itemId).attr( "data", returnData);
		   
 var chart = new CanvasJS.Chart("chart"+itemId,
  {
	  zoomEnabled: true, 
      theme: "theme2",
      title:{
        text: "Sales"
      },
      axisX: {
   	
		interval: 7,
		intervalType: "day",
   		valueFormatString: "MMM DD",
		labelAngle: -30
		
		
        
      },
      axisY:{
        includeZero: true
        
      },
      data: [
      {        
         type: "spline",
		 markerColor: "#1E90FF",
         xValueType: "dateTime",
	     lineThickness: 2,
         dataPoints: returnData,
      }
      
      
      ]
    });
	

chart.render();
			   
		   //console.log(data);
       }
    );
   
   


}
</script>


 </script>

<?PHP

include_once("footer.php");	
?>

   

