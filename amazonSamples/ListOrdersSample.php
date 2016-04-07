<?php
/*******************************************************************************
 * Copyright 2009-2014 Amazon Services. All Rights Reserved.
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
 * @package  Marketplace Web Service Orders
 * @version  2013-09-01
 * Library Version: 2013-09-01
 * Generated: Thu Feb 06 16:04:57 GMT 2014
 */

/**
 * List Orders Sample
 */

require_once('.config.inc.php');
require_once('dbConf.php');

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// More endpoints are listed in the MWS Developer Guide
// North America:
$serviceUrl = "https://mws.amazonservices.com/Orders/2013-09-01";
// Europe
//$serviceUrl = "https://mws-eu.amazonservices.com/Orders/2013-09-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Orders/2013-09-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2013-09-01";


 $config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'MaxErrorRetry' => 3,
 );

 $service = new MarketplaceWebServiceOrders_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);

/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebServiceOrders
 * responses without calling MarketplaceWebServiceOrders service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebServiceOrders/Mock tree
 *
 ***********************************************************************/
  //$service = new MarketplaceWebServiceOrders_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for List Orders Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_ListOrders
 $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
 $request->setSellerId(MERCHANT_ID);
 
 
 
 
 $request->setCreatedAfter(date("Y-m-d",strtotime('-4 day')));
 $request->setMarketplaceId(MARKETPLACE_ID);
 
 
 
 // object or array of parameters
 invokeListOrders($service, $request);

/**
  * Get List Orders Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
  * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrders or array of parameters
  */

  function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
  
          $nextToken = '';
          do {
         
              if($nextToken!='')
              {
                   $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
                   $request->setSellerId(MERCHANT_ID);
                   $request->setNextToken($nextToken);
                  $response = $service->ListOrdersByNextToken($request); 
              }
                  else 
                  $response = $service->ListOrders($request);
      

        //echo ("Service Response\n");
        echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->saveXML();
       
       // $ListOrdersResponse = $dom->ListOrdersResponse;
       // $ListOrdersResult = $ListOrdersResponse->NextToken;
        
        @$nextToken = $dom->getElementsByTagName('NextToken')->item(0);
        if($nextToken)
            $nextToken = $nextToken->nodeValue;
        

       // echo $nextToken;
        

        $orders = $dom->getElementsByTagName('Order');
        
        
        foreach($orders as $order)
        {
            $AmazonOrderId = $order->getElementsByTagName('AmazonOrderId')->item(0)->nodeValue;
            $PurchaseDate = $order->getElementsByTagName('PurchaseDate')->item(0)->nodeValue;
            $LastUpdateDate = $order->getElementsByTagName('LastUpdateDate')->item(0)->nodeValue;
            $OrderStatus = $order->getElementsByTagName('OrderStatus')->item(0)->nodeValue;
            
            $ShippingAddress = $order->getElementsByTagName('ShippingAddress')->item(0);
            
            if($ShippingAddress)
            {
                $shippingName =  $ShippingAddress->getElementsByTagName('Name')->item(0)->nodeValue;
                $shippingAddressLine1 =  $ShippingAddress->getElementsByTagName('AddressLine1')->item(0)->nodeValue;
                @$shippingAddressLine2 =  $ShippingAddress->getElementsByTagName('AddressLine2')->item(0)->nodeValue;
                $shippingCity =  $ShippingAddress->getElementsByTagName('City')->item(0)->nodeValue;
                $shippingState =  $ShippingAddress->getElementsByTagName('StateOrRegion')->item(0)->nodeValue;
                $shippingPostal =  $ShippingAddress->getElementsByTagName('PostalCode')->item(0)->nodeValue;
                $shippingCountry =  $ShippingAddress->getElementsByTagName('CountryCode')->item(0)->nodeValue;
                $shippingPhone =  $ShippingAddress->getElementsByTagName('Phone')->item(0)->nodeValue;
            }
            
           $OrderTotal = $order->getElementsByTagName('OrderTotal')->item(0); 
           if($OrderTotal) {
                $OrderCurrency = $OrderTotal->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;
                $OrderTotalAmount = $OrderTotal->getElementsByTagName('Amount')->item(0)->nodeValue;
           }
           
           $NumberOfItemsShipped = $order->getElementsByTagName('NumberOfItemsShipped')->item(0)->nodeValue;
           $NumberOfItemsUnshipped = $order->getElementsByTagName('NumberOfItemsUnshipped')->item(0)->nodeValue;
           
           
            $BuyerEmail = $order->getElementsByTagName('BuyerEmail')->item(0)->nodeValue;
            $BuyerName = $order->getElementsByTagName('BuyerName')->item(0)->nodeValue;
            $ShipmentServiceLevelCategory = $order->getElementsByTagName('ShipmentServiceLevelCategory')->item(0)->nodeValue;
            $ShippedByAmazonTFM = $order->getElementsByTagName('ShippedByAmazonTFM')->item(0)->nodeValue;
            $OrderType = $order->getElementsByTagName('OrderType')->item(0)->nodeValue;
            $EarliestShipDate = $order->getElementsByTagName('EarliestShipDate')->item(0)->nodeValue;
            $LatestShipDate = $order->getElementsByTagName('LatestShipDate')->item(0)->nodeValue;
            $EarliestDeliveryDate = $order->getElementsByTagName('EarliestDeliveryDate')->item(0)->nodeValue;
            $LatestDeliveryDate = $order->getElementsByTagName('LatestDeliveryDate')->item(0)->nodeValue;
            
            echo $shippingName.'<br>';
            echo $AmazonOrderId;
            echo '<br>';
            echo $shippingCity.'<br>';
            
            // Check for record in Database , update or add record
            
            $result = mysql_query("SELECT AmazonOrderId FROM ams_part4motor_orders WHERE AmazonOrderId = '".$AmazonOrderId."'");
            $num_rows = mysql_num_rows($result);

            if ($num_rows > 0) {
              // update records
                $sql = "UPDATE ams_part4motor_orders SET PurchaseDate = '".$PurchaseDate."',"
                        ." LastUpdateDate = '".$LastUpdateDate."',OrderStatus='".$OrderStatus."', ShippingName='".addslashes($shippingName)."',"
                        ." ShippingStreet1='".addslashes($shippingAddressLine1)."', ShippingStreet2='".addslashes($shippingAddressLine2)."',"
                        ." ShippingState='".$shippingState."', ShippingCountry='".addslashes($shippingCountry)."', ShippingPhone = '".addslashes($shippingPhone)."',"
                        ." OrderTotalAmount = ".(double)$OrderTotalAmount.", OrderTotalCurrency='".$OrderCurrency."', "
                        ."NumberOfItemsShipped = ".(int)$NumberOfItemsShipped.", NumberOfItemsUnshipped= $NumberOfItemsUnshipped , "
                        ." BuyerEmail='".addslashes($BuyerEmail)."', BuyerName='".addslashes($BuyerName)."', ShipmentServiceLevelCategory='".$ShipmentServiceLevelCategory."', "
                        ."ShippedByAmazonTFM='".$ShippedByAmazonTFM."', OrderType='".$OrderType."', "
                        ."EarliestShipDate = '".$EarliestShipDate."', LatestShipDate='".$LatestShipDate."', LatestDeliveryDate='".$LatestDeliveryDate."' WHERE AmazonOrderId='".$AmazonOrderId."'";
            
                 echo $sql;
                mysql_query($sql) or die(mysql_error());
            }
            else {
              // add record
                $sql = "INSERT INTO ams_part4motor_orders SET AmazonOrderId='".$AmazonOrderId."',PurchaseDate = '".$PurchaseDate."',"
                        ." LastUpdateDate = '".$LastUpdateDate."',OrderStatus='".$OrderStatus."', ShippingName='".addslashes($shippingName)."',"
                        ." ShippingStreet1='".addslashes($shippingAddressLine1)."', ShippingStreet2='".addslashes($shippingAddressLine2)."',"
                        ." ShippingState='".$shippingState."', ShippingCountry='".addslashes($shippingCountry)."', ShippingPhone = '".addslashes($shippingPhone)."',"
                        ." OrderTotalAmount = ".(double)$OrderTotalAmount.", OrderTotalCurrency='".$OrderCurrency."', "
                        ."NumberOfItemsShipped = ".(int)$NumberOfItemsShipped.", NumberOfItemsUnshipped= $NumberOfItemsUnshipped , "
                        ." BuyerEmail='".addslashes($BuyerEmail)."', BuyerName='".addslashes($BuyerName)."', ShipmentServiceLevelCategory='".$ShipmentServiceLevelCategory."', "
                        ."ShippedByAmazonTFM='".$ShippedByAmazonTFM."', OrderType='".$OrderType."', "
                        ."EarliestShipDate = '".$EarliestShipDate."', LatestShipDate='".$LatestShipDate."', LatestDeliveryDate='".$LatestDeliveryDate."'";
                
                
                echo $sql;
                mysql_query($sql) or die(mysql_error());
            }
            
            // Reset values in loop
        
                $shippingName =  '';
                $shippingAddressLine1 ='';
                $shippingAddressLine2 =  '';
                $shippingCity =  '';
                $shippingState =  '';
                $shippingPostal = '';
                $shippingCountry =  '';
                $shippingPhone = '';
                $OrderCurrency = '';
                $OrderTotalAmount = '';
            
            }  // END of LOOP through Orders in XML
        
          } while($nextToken!='');

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }

