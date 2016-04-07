<?PHP

require_once "http.php";	
require_once "simple_html_dom.php";

date_default_timezone_set('America/Los_Angeles');

require_once('dbConf.php');




function is_connected()
{
    $connected = @fsockopen("www.google.com", 80); //website and port
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;

}


			$kk = 0;
			
		//while(1!=2){	
	
	
	$request = mysql_query("SELECT listingid FROM pepauto_active order by listingId DESC") or die(mysql_error());

                //echo mysql_num_rows($request);
                echo PHP_EOL;
                
                $index =0;
				
				$browsers = array('ie7','ie8','ie9','ie6','firefox','opera','safari','chrome');
				$myBrowser = $browsers[rand(0,7)];
				
             while($row = mysql_fetch_array($request))
                
		
				{
					$itemId = $row['listingid'];
					
					       $html = new simple_html_dom();		
		$url = 'http://www.ebay.com/itm/'.$itemId;
		
		
		
		
		
                //17
	$options = array(
		"headers" => array(
			"User-Agent" => GetWebUserAgent($myBrowser),
			"Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Language" => "en-us,en;q=0.5",
			"Accept-Charset" => "ISO-8859-1,utf-8;q=0.7,*;q=0.7",
			"Cache-Control" => "max-age=0"
		)
	);
	
	
	

	while(is_connected()==false)
		{
			;
		}        
	$result = RetrieveWebpage($url, $options);
	
        if (!$result["success"])  echo "Error retrieving URL.  " . $result["error"] . "\n";
	
	else 
            if ($result["response"]["code"] != 200) 
            echo "Error retrieving URL.  Server returned:  " 
            . $result["response"]["code"] . " " . $result["response"]["meaning"] . "\n";
        
	else
	{
		//@$html->load($result["body"]);
		echo 'BROWSING '.$itemId.'<br>'.PHP_EOL;
		
	}
					
				}
				
				
				
		echo '--------------------------------  '.$kk++.PHP_EOL;		
		//}
?>