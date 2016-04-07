<?php
/*******************************************************************************
 * Copyright 2009-2013 Amazon Services. All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License"); 
 *
 * You may not use this file except in compliance with the License. 
 * You may obtain a copy of the License at: http://aws.amazon.com/apache2.0
 * This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR 
 * CONDITIONS OF ANY KIND, either express or implied. See the License for the 
 * specific language governing permissions and limitations under the License.
 *******************************************************************************
 * PHP Version 5
 * @category Amazon
 * @package  Marketplace Web Service Products
 * @version  2011-10-01
 * Library Version: 2013-11-01
 * Generated: Fri Nov 08 21:23:22 GMT 2013
 */

/**
 * Get Competitive Pricing For ASIN Sample
 */

require_once('.config.inc.php');
require_once('dbConf.php');

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceProducts
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// More endpoints are listed in the MWS Developer Guide
// North America:
$serviceUrl = "https://mws.amazonservices.com/Products/2011-10-01";
// Europe
//$serviceUrl = "https://mws-eu.amazonservices.com/Products/2011-10-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Products/2011-10-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Products/2011-10-01";


 $config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'MaxErrorRetry' => 3,
 );

 $service = new MarketplaceWebServiceProducts_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);

/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebServiceProducts
 * responses without calling MarketplaceWebServiceProducts service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebServiceProducts/Mock tree
 *
 ***********************************************************************/
 // $service = new MarketplaceWebServiceProducts_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for Get Competitive Pricing For ASIN Action
 ***********************************************************************/
 
$result = mysql_query("SELECT count(asin1) as count  FROM ams_part4motor_listings");
$row = mysql_fetch_assoc($result);
$listingCount = $row['count'];


 
$result = mysql_query("SELECT value FROM settings WHERE name = 'getAmazonFlag'");

$row = mysql_fetch_assoc($result);
$flag = $row['value'];

 $sql = "SELECT distinct asin1 FROM ams_part4motor_listings LIMIT $flag, 10";
 
 $result = mysql_query($sql);
 
 while($row = mysql_fetch_assoc($result))
 {
     $asinArray[] = $row['asin1'];
 }
 
 
 print_r($asinArray);
 
 if($flag > $listingCount )
     $newFlag = 0;
 else
     $newFlag = $flag + 10;
 
 mysql_query("UPDATE settings SET value = $newFlag WHERE name = 'getAmazonFlag'");
 
 $asin_List = new MarketplaceWebServiceProducts_Model_ASINListType();
 $asin_List->setASIN($asinArray);
 
 
// @TODO: set request. Action can be passed as MarketplaceWebServiceProducts_Model_GetCompetitivePricingForASIN
 $request = new MarketplaceWebServiceProducts_Model_GetCompetitivePricingForASINRequest();
 $request->setSellerId(MERCHANT_ID);
 $request->setASINList($asin_List);
 $request->setMarketplaceId(MARKETPLACE_ID);
 // object or array of parameters
 invokeGetCompetitivePricingForASIN($service, $request);

/**
  * Get Get Competitive Pricing For ASIN Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MarketplaceWebServiceProducts_Interface $service instance of MarketplaceWebServiceProducts_Interface
  * @param mixed $request MarketplaceWebServiceProducts_Model_GetCompetitivePricingForASIN or array of parameters
  */

  function invokeGetCompetitivePricingForASIN(MarketplaceWebServiceProducts_Interface $service, $request)
  {
      try {
        $response = $service->GetCompetitivePricingForASIN($request);

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
       // echo $dom->saveXML();
        
        $products = $dom->getElementsByTagName('GetCompetitivePricingForASINResult');
        
        
        foreach($products as $product)
        {
            
            @$rank = $product->getElementsByTagName('Rank')->item(0)->nodeValue;
            if($rank =='')
                $rank = 0;
            
            $ASIN = $product->getElementsByTagName('ASIN')->item(0)->nodeValue;
             //$OfferListing  = $product->getElementsByTagName('OfferListingCount')->item(1);
             
             foreach ($product->getElementsByTagName('OfferListingCount') as $OfferListing) {
                if($OfferListing->getAttribute('condition') === 'New') {
                     $OfferListingCount = $OfferListing ->getElementsByTagName('Value')->item(0)->nodeValue;
                    }
                }
            
            
           @ $LandedPrice = $product->getElementsByTagName('LandedPrice')->item(0);
            @$LandedPriceAmount = $LandedPrice->getElementsByTagName('Amount')->item(0)->nodeValue;
            
            $ListingPrice = $product->getElementsByTagName('ListingPrice')->item(0);
            $ListingPriceAmount = $ListingPrice->getElementsByTagName('Amount')->item(0)->nodeValue;
            
            $Shipping = $product->getElementsByTagName('Shipping')->item(0);
            $ShippingAmount = $Shipping->getElementsByTagName('Amount')->item(0)->nodeValue;
            
            
            
            //echo $rank.' '.$ASIN.' '.$OfferListingCount.' '. $LandedPriceAmount.' '.$ListingPriceAmount.' '. $ShippingAmount;
         
            $query = "UPDATE ams_part4motor_listings SET salesRank =  $rank , "
                    ." newOffers = $OfferListingCount, lowestLandedPrice = $LandedPriceAmount,"
                    ." lowestListingPrice = $ListingPriceAmount , lowestShippingPrice = $ShippingAmount "
                    ." WHERE asin1 = '".$ASIN."'";
            
           
            @mysql_query($query) or die(mysql_error());
            
            echo PHP_EOL;
        }
    
        
//        @$nextToken = $dom->getElementsByTagName('NextToken')->item(0);
//        if($nextToken)
//            $nextToken = $nextToken->nodeValue;
//        
//        $AmazonOrderId = $dom->getElementsByTagName('AmazonOrderId')->item(0);
//        $AmazonOrderId = $AmazonOrderId->nodeValue;
//        
//        
//         $orderItems = $dom->getElementsByTagName('OrderItem');
//        
//        foreach($orderItems as $orderItem)
//        {
//            
//        }
            
       // echo $dom->saveXML();
       //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

     } catch (MarketplaceWebServiceProducts_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }

