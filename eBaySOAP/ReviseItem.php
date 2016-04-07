<?php
// be sure include path contains current directory
// to make sure samples work
ini_set('include_path', ini_get('include_path') . ':.');
//error_reporting(E_ALL);


//require_once('../get-common/keys.php') ;
//require_once('../get-common/eBaySession.php');

// Load general helper classes for eBay SOAP API

function setPrice($price, $ItemID)
{
	require_once 'eBaySOAP.php';
	
	$listingTable = ACTIVE_LISTING_TABLE;
	
// Load developer-specific configuration data from ini file
$config = parse_ini_file(EBAY_INI_FILE, true);
$site = $config['settings']['site'];
$compatibilityLevel = $config['settings']['compatibilityLevel'];

$dev = $config[$site]['devId'];
$app = $config[$site]['appId'];
$cert = $config[$site]['cert'];
$token = $config[$site]['authToken'];
$location = $config[$site]['gatewaySOAP'];

// Create and configure session
$session = new eBaySession($dev, $app, $cert);
$session->token = $token;
$session->site = 0; // 0 = US;
$session->location = $location;



//$ItemID = '251422036418';

// Make AddItem, ReviseItem, and GetItem API calls to demonstate how to modify
// and item listing

try {
	$client = new eBaySOAP($session);
            
      
        $qty = 12;
        if($price<30)
                $qty = 6;
        elseif($price >30 && $price<70)
                $qty = 5;
        elseif($price >70 && $price<100)
                $qty = 4;
        elseif($price >100 && $price<140)
                $qty = 3;
        elseif($price >140 && $price<140)
                $qty = 2;
        
        
	// Revise it and change the Title and raise the BuyItNowPrice
	$Item = array('ItemID' => $ItemID,
				  //'Title' => 'The revised item title',
				  'StartPrice' => $price,
                 // 'Quantity' => $qty,
				 );
				 //BuyItNowPrice StartingPrice

                           
	$params = array('Version' => $compatibilityLevel, 
	                'Item' => $Item
	               );

	$results = $client->ReviseItem($params);

	 

	
	mysql_query("UPDATE ".$listingTable." SET activePrice = ".$price." WHERE listingId=".$ItemID);

	print "<hr>Revised Item ID: $ItemID <br>\n";

	// Get it to confirm
	//$params = array('Version' => $compatibilityLevel, 'ItemID' =>  $ItemID);
	//$results = $client->GetItem($params);

	//print "Got Item ID: $ItemID <br>\n";
	//print "It has a title of: " . $results->Item->Title . " <br>\n";
    //print "It has a BIN Price of: " . $results->Item->StartPrice->_ . ' ' . $results->Item->StartPrice->currencyID . " <br> \n";

} catch (SOAPFault $f) {
	print $f; // error handling
}
}

// Uncomment below to view SOAP envelopes
// print "Request: \n".$client->__getLastRequest() ."\n";
// print "Response: \n".$client->__getLastResponse()."\n";



function setQuantity($qty, $ItemID)
{
	require_once 'eBaySOAP.php';
	
	$listingTable = ACTIVE_LISTING_TABLE;
	
// Load developer-specific configuration data from ini file
$config = parse_ini_file(EBAY_INI_FILE, true);
$site = $config['settings']['site'];
$compatibilityLevel = $config['settings']['compatibilityLevel'];

$dev = $config[$site]['devId'];
$app = $config[$site]['appId'];
$cert = $config[$site]['cert'];
$token = $config[$site]['authToken'];
$location = $config[$site]['gatewaySOAP'];

// Create and configure session
$session = new eBaySession($dev, $app, $cert);
$session->token = $token;
$session->site = 0; // 0 = US;
$session->location = $location;


//$ItemID = '251422036418';

// Make AddItem, ReviseItem, and GetItem API calls to demonstate how to modify
// and item listing

try {
	$client = new eBaySOAP($session);
            
	// Revise it and change the Title and raise the BuyItNowPrice
	$Item = array('ItemID' => $ItemID,
                       'Quantity' => $qty,
				 );
				 //BuyItNowPrice StartingPrice
                           
	$params = array('Version' => $compatibilityLevel, 
	                'Item' => $Item
	               );

	$results = $client->ReviseItem($params);

} catch (SOAPFault $f) {
	print $f; // error handling
}
}




function changeDescription($ItemID, $find='', $replace='')
{
	require_once 'eBaySOAP.php';
	
	$listingTable = ACTIVE_LISTING_TABLE;
	
// Load developer-specific configuration data from ini file
$config = parse_ini_file(EBAY_INI_FILE, true);
$site = $config['settings']['site'];
$compatibilityLevel = $config['settings']['compatibilityLevel'];

$dev = $config[$site]['devId'];
$app = $config[$site]['appId'];
$cert = $config[$site]['cert'];
$token = $config[$site]['authToken'];
$location = $config[$site]['gatewaySOAP'];

// Create and configure session
$session = new eBaySession($dev, $app, $cert);
$session->token = $token;
$session->site = 0; // 0 = US;
$session->location = $location;


//$ItemID = '251422036418';

// Make AddItem, ReviseItem, and GetItem API calls to demonstate how to modify
// and item listing

try {
	$client = new eBaySOAP($session);
            
			

	// Get it to confirm
$params = array('Version' => $compatibilityLevel, 'ItemID' => $ItemID, 'DetailLevel' => 'ReturnAll' );
$results = $client->GetItem($params);

print "Got Item ID: $ItemID <br>\n";
print "It has a title of: " . $results->Item->Title . " <br>\n";
//print "It has a BIN Price of: " . $results->Item->BuyItNowPrice->_ . ' ' . $results->Item->BuyItNowPrice->currencyID . " <br> \n";




$htmlDesc  = $results->Item->Description;

$htmlDesc = str_ireplace("<IMG SRC='http://pics.ebay.com/aw/pics/sell/templates/images/k2/tagline.gif' BORDER='0' />",'', $htmlDesc );
$htmlDesc = str_ireplace("Powered by <A HREF='http://pages.ebay.com/turbo_lister/'>eBay Turbo Lister</A>",'', $htmlDesc );
$htmlDesc = str_ireplace("<BR />The free listing tool. List your items fast and easy and manage your active items.",'', $htmlDesc );		
			
$htmlDesc = str_ireplace('"http://cgi1.ebay.com/ws/eBayISAPI.dll?MakeTrack&rt=nc&item=">','"http://cgi1.ebay.com/ws/eBayISAPI.dll?MakeTrack&rt=nc&item='.$ItemID.'">', $htmlDesc );
			
//	echo $htmlDesc ;	
			
			
		
	// Revise it and change the Title and raise the BuyItNowPrice
	$Item = array('ItemID' => $ItemID,
				  'Description' => $htmlDesc,
				 );
				 //BuyItNowPrice StartingPrice
                           
	$params = array('Version' => $compatibilityLevel, 
	                'Item' => $Item
	               );

	$results = $client->ReviseItem($params);

} catch (SOAPFault $f) {
	print $f; // error handling
}
}




function setDescription($ItemID, $description)
{
	require_once 'eBaySOAP.php';
	
	$listingTable = ACTIVE_LISTING_TABLE;
	
// Load developer-specific configuration data from ini file
$config = parse_ini_file(EBAY_INI_FILE, true);
$site = $config['settings']['site'];
$compatibilityLevel = $config['settings']['compatibilityLevel'];

$dev = $config[$site]['devId'];
$app = $config[$site]['appId'];
$cert = $config[$site]['cert'];
$token = $config[$site]['authToken'];
$location = $config[$site]['gatewaySOAP'];

// Create and configure session
$session = new eBaySession($dev, $app, $cert);
$session->token = $token;
$session->site = 0; // 0 = US;
$session->location = $location;


//$ItemID = '251422036418';

// Make AddItem, ReviseItem, and GetItem API calls to demonstate how to modify
// and item listing

try {
	$client = new eBaySOAP($session);
            
	// Get it to confirm
$params = array('Version' => $compatibilityLevel, 'ItemID' => $ItemID, 'DetailLevel' => 'ReturnAll' );
$results = $client->GetItem($params);

print "Got Item ID: $ItemID <br>\n";
print "It has a title of: " . $results->Item->Title . " <br>\n";
//print "It has a BIN Price of: " . $results->Item->BuyItNowPrice->_ . ' ' . $results->Item->BuyItNowPrice->currencyID . " <br> \n";




	// Revise it and change the Title and raise the BuyItNowPrice
	$Item = array('ItemID' => $ItemID,
				  'Description' => $description,
				 );
				 //BuyItNowPrice StartingPrice
                           
	$params = array('Version' => $compatibilityLevel, 
	                'Item' => $Item
	               );

	$results = $client->ReviseItem($params);

} catch (SOAPFault $f) {
	print $f; // error handling
}
}



function setUpc($ItemID, $upc)
{
	require_once 'eBaySOAP.php';
	
	$listingTable = ACTIVE_LISTING_TABLE;
	
// Load developer-specific configuration data from ini file
$config = parse_ini_file(EBAY_INI_FILE, true);
$site = $config['settings']['site'];
$compatibilityLevel = $config['settings']['compatibilityLevel'];

$dev = $config[$site]['devId'];
$app = $config[$site]['appId'];
$cert = $config[$site]['cert'];
$token = $config[$site]['authToken'];
$location = $config[$site]['gatewaySOAP'];

// Create and configure session
$session = new eBaySession($dev, $app, $cert);
$session->token = $token;
$session->site = 0; // 0 = US;
$session->location = $location;


//$ItemID = '251422036418';

// Make AddItem, ReviseItem, and GetItem API calls to demonstate how to modify
// and item listing

try {
	$client = new eBaySOAP($session);
            
	// Revise it and change the Title and raise the BuyItNowPrice
	$Item = array('ItemID' => $ItemID,
                       'ProductListingDetails' => array('UPC'=>$upc),
				 );
				 //BuyItNowPrice StartingPrice
                           
	$params = array('Version' => $compatibilityLevel, 
	                'Item' => $Item
	               );

	$results = $client->ReviseItem($params);

} catch (SOAPFault $f) {
	print $f; // error handling
}
}



function changePicture($ItemID, $pictureUrl='' )
{
	require_once 'eBaySOAP.php';
	
	$listingTable = ACTIVE_LISTING_TABLE;
	
// Load developer-specific configuration data from ini file
$config = parse_ini_file(EBAY_INI_FILE, true);
$site = $config['settings']['site'];
$compatibilityLevel = $config['settings']['compatibilityLevel'];

$dev = $config[$site]['devId'];
$app = $config[$site]['appId'];
$cert = $config[$site]['cert'];
$token = $config[$site]['authToken'];
$location = $config[$site]['gatewaySOAP'];

// Create and configure session
$session = new eBaySession($dev, $app, $cert);
$session->token = $token;
$session->site = 0; // 0 = US;
$session->location = $location;


//$ItemID = '251422036418';

// Make AddItem, ReviseItem, and GetItem API calls to demonstate how to modify
// and item listing

try {
	$client = new eBaySOAP($session);
            
			

	// Get it to confirm
$params = array('Version' => $compatibilityLevel, 'ItemID' => $ItemID, 'DetailLevel' => 'ReturnAll' );
$results = $client->GetItem($params);

print "Got Item ID: $ItemID <br>\n";
print "It has a title of: " . $results->Item->Title . " <br>\n";
//print "It has a BIN Price of: " . $results->Item->BuyItNowPrice->_ . ' ' . $results->Item->BuyItNowPrice->currencyID . " <br> \n";


//if(!file_exists($pictureUrl))
//    return;


$htmlDesc  = $results->Item->Description;

if(strpos($htmlDesc, 's3-us-west-2.amazonaws.com/part4you/ap/') == false)
        return ;


$htmlDesc = str_ireplace('s3-us-west-2.amazonaws.com/part4you/ap/','s3-us-west-2.amazonaws.com/part4you/framed/', $htmlDesc );



		
	// Revise it and change the Title and raise the BuyItNowPrice
	$Item = array('ItemID' => $ItemID,
                      'PictureDetails' => array('PictureURL' => $pictureUrl),
                      'Description' => $htmlDesc,
				 );
				 //BuyItNowPrice StartingPrice
                           
	$params = array('Version' => $compatibilityLevel, 
	                'Item' => $Item
                        
	               );

	$results = $client->ReviseItem($params);
        
        var_dump($results);

} catch (SOAPFault $f) {
	print $f; // error handling
}
}



function setPicture($ItemID, $pictureUrl='' )
{
	require_once 'eBaySOAP.php';
	
	$listingTable = ACTIVE_LISTING_TABLE;
	
// Load developer-specific configuration data from ini file
$config = parse_ini_file(EBAY_INI_FILE, true);
$site = $config['settings']['site'];
$compatibilityLevel = $config['settings']['compatibilityLevel'];

$dev = $config[$site]['devId'];
$app = $config[$site]['appId'];
$cert = $config[$site]['cert'];
$token = $config[$site]['authToken'];
$location = $config[$site]['gatewaySOAP'];

// Create and configure session
$session = new eBaySession($dev, $app, $cert);
$session->token = $token;
$session->site = 0; // 0 = US;
$session->location = $location;


//$ItemID = '251422036418';

// Make AddItem, ReviseItem, and GetItem API calls to demonstate how to modify
// and item listing

try {
	$client = new eBaySOAP($session);
            
			

	// Get it to confirm
$params = array('Version' => $compatibilityLevel, 'ItemID' => $ItemID, 'DetailLevel' => 'ReturnAll' );
$results = $client->GetItem($params);

print "Got Item ID: $ItemID <br>\n";
print "It has a title of: " . $results->Item->Title . " <br>\n";


		
	// Revise it and change the Title and raise the BuyItNowPrice
	$Item = array('ItemID' => $ItemID,
                      'PictureDetails' => array('PictureURL' => $pictureUrl),
				 );
				 //BuyItNowPrice StartingPrice
                           
	$params = array('Version' => $compatibilityLevel, 
	                'Item' => $Item
                        
	               );

	$results = $client->ReviseItem($params);
        

} catch (SOAPFault $f) {
	print $f; // error handling
}
}

