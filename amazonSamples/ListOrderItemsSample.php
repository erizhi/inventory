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
 * List Order Items Sample
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
 // $service = new MarketplaceWebServiceOrders_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for List Order Items Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_ListOrderItems
 $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
 $request->setSellerId(MERCHANT_ID);
 
 
 $result = mysql_query("SELECT AmazonOrderId FROM ams_part4motor_orders ORDER BY PurchaseDate DESC LIMIT 0, 100");
 
 while($row = mysql_fetch_assoc($result))
 {

     $request->setAmazonOrderId($row['AmazonOrderId']);
        
      invokeListOrderItems($service, $request);
 }

 // object or array of parameters

/**
  * Get List Order Items Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
  * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrderItems or array of parameters
  */

  function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
        
          $nextToken = '';
          do {
          
              
               if($nextToken!='')
              {
                   $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsByNextTokenRequest();
                   $request->setSellerId(MERCHANT_ID);
                   $request->setNextToken($nextToken);
                   $response = $service->ListOrderItemsByNextToken($request); 
              }
                  else 
                      $response = $service->ListOrderItems($request);

              
              

        //echo ("Service Response\n");
        //echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        //echo $dom->saveXML();
       
        
        @$nextToken = $dom->getElementsByTagName('NextToken')->item(0);
        if($nextToken)
            $nextToken = $nextToken->nodeValue;
        
        $AmazonOrderId = $dom->getElementsByTagName('AmazonOrderId')->item(0);
        $AmazonOrderId = $AmazonOrderId->nodeValue;
        
        
         $orderItems = $dom->getElementsByTagName('OrderItem');
        
        foreach($orderItems as $orderItem)
        {
           
            $ASIN = $orderItem->getElementsByTagName('ASIN')->item(0)->nodeValue;
            $SellerSKU = $orderItem->getElementsByTagName('SellerSKU')->item(0)->nodeValue;
            $OrderItemId = $orderItem->getElementsByTagName('OrderItemId')->item(0)->nodeValue;
            
            $Title = $orderItem->getElementsByTagName('Title')->item(0)->nodeValue;
            $QuantityOrdered = $orderItem->getElementsByTagName('QuantityOrdered')->item(0)->nodeValue;
            $QuantityShipped = $orderItem->getElementsByTagName('QuantityShipped')->item(0)->nodeValue;
            
            $ItemPriceNode = $orderItem->getElementsByTagName('ItemPrice')->item(0);
               if($ItemPriceNode){
                    $itemPriceAmount = $ItemPriceNode->getElementsByTagName('Amount')->item(0)->nodeValue;
                    $itemPriceCurrency = $ItemPriceNode->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;
               }
                
            $GiftWrapPriceNode = $orderItem->getElementsByTagName('GiftWrapPrice')->item(0);
             if($GiftWrapPriceNode){
                 $GiftWrapAmount = $GiftWrapPriceNode->getElementsByTagName('Amount')->item(0)->nodeValue;
                 $GiftWrapCurrency = $GiftWrapPriceNode->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;
                 }   
                
                
            $ItemTaxNode = $orderItem->getElementsByTagName('ItemTax')->item(0);
               if($ItemTaxNode){
                   $itemTaxAmount = $ItemTaxNode->getElementsByTagName('Amount')->item(0)->nodeValue;
                   $itemTaxCurrency = $ItemTaxNode->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;    
                 }   
                
            
            $ShippingTaxNode = $orderItem->getElementsByTagName('ShippingTax')->item(0);
              if($ShippingTaxNode){
                $shippingTaxAmount = $ShippingTaxNode->getElementsByTagName('Amount')->item(0)->nodeValue;
                $shippingTaxCurrency = $ShippingTaxNode->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;
              }
                
                
            $GiftWrapTaxNode = $orderItem->getElementsByTagName('GiftWrapTax')->item(0);
            if($GiftWrapTaxNode)
            {
                $giftWrapTaxAmount = $GiftWrapTaxNode->getElementsByTagName('Amount')->item(0)->nodeValue;
                $giftWrapTaxCurrency = $GiftWrapTaxNode->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;    
            }
            
            
            $ShippingDiscountNode = $orderItem->getElementsByTagName('ShippingDiscount')->item(0);
            if($ShippingDiscountNode) {
                $shippingDiscountAmount = $ShippingDiscountNode->getElementsByTagName('Amount')->item(0)->nodeValue;
                $shippingDiscountCurrency = $ShippingDiscountNode->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;   
            }
                
            $PromotionDiscountNode = $orderItem->getElementsByTagName('PromotionDiscount')->item(0);
            if($PromotionDiscountNode){
                $promotionDiscountAmount = $PromotionDiscountNode->getElementsByTagName('Amount')->item(0)->nodeValue;
                $promotionDiscountCurrency = $PromotionDiscountNode->getElementsByTagName('CurrencyCode')->item(0)->nodeValue;   
            }
                
            @$PromotionIds = $orderItem->getElementsByTagName('PromotionIds')->item(0)->nodeValue;
            
            $ConditionId = $orderItem->getElementsByTagName('ConditionId')->item(0)->nodeValue;
            $ConditionSubtypeId = $orderItem->getElementsByTagName('ConditionSubtypeId')->item(0)->nodeValue;
            
            echo $Title.'<br>';
          
            
            
            $result = mysql_query("SELECT AmazonOrderId FROM ams_part4motor_orderItems WHERE AmazonOrderId = '".$AmazonOrderId."' AND OrderItemId='".$OrderItemId."'");
            $num_rows = mysql_num_rows($result);

            if ($num_rows > 0) {
                // UPDATE
            }
            else
            {
             // find location and deduct from inventory   
              $sql = "SELECT location FROM ams_part4motor_listings WHERE asin1 ='".$ASIN."'";
              $result = mysql_query($sql);
              
              if($result)
               $num_rows = mysql_num_rows($result);
               if ($num_rows > 0) {
                   $row = mysql_fetch_assoc ($result);
                   
                   if($row['location']!='' && $row['location']!='URO' && $row['location']!='MTC' && $row['location']!=null)
                   {
                       mysql_query ("UPDATE inventory SET qty = qty- $QuantityOrdered WHERE location = '".$row['location']."' ");
                       mysql_query ("UPDATE part4engine_active SET instock = instock - $QuantityOrdered WHERE location = '".$row['location']."' ");
                       mysql_query ("UPDATE pepauto_active SET instock = instock - $QuantityOrdered WHERE location = '".$row['location']."' ");
                   }   
                   
                   $itemLocation = $row['location'];
               }
               
               
                // Add Record

               $sql = "INSERT INTO ams_part4motor_orderItems SET AmazonOrderId='".$AmazonOrderId."',ASIN = '".$ASIN."',"
                    ." SellerSKU = '".$SellerSKU."',OrderItemId='".$OrderItemId."', Title='".addslashes($Title)."',"
                    ." QuantityOrdered='".(int)($QuantityOrdered)."', QuantityShipped='".(int)($QuantityShipped)."',"
                    ." ItemPriceAmount=".(double)$itemPriceAmount.", ItemPriceCurrency='".addslashes($itemPriceCurrency)."',"
                    ." GiftWrapPriceAmount = ".(double)($GiftWrapAmount).","
                    ." GiftWrapCurrency = '".$GiftWrapCurrency."', ItemTaxAmount= ".(double)$itemTaxAmount.", "
                    ."ItemTaxCurrency = '".$itemTaxCurrency."', ShippingTaxAmount= ".(double)$shippingTaxAmount." , "
                    ." ShippingTaxCurrency='".addslashes($shippingTaxCurrency)."', GiftWrapTaxAmount='".(double)($giftWrapTaxAmount)."', "
                    ."GiftWrapTaxCurrency='".$giftWrapTaxCurrency."', "
                    ."ShippingDiscountAmount='".(double)$shippingDiscountAmount."', ShippingDiscountCurrency='".addslashes($shippingDiscountCurrency)."', "
                    ."PromotionDiscountAmount = '".(double)$promotionDiscountAmount."', PromotionDiscountCurrency='".$promotionDiscountCurrency."', "
                    ."PromotionIds='".  addslashes($PromotionIds)."', "
                    ."location = '".addslashes($itemLocation)."', "
                    ." ConditionId='".  addslashes($ConditionId)."', ConditionSubtypeId='".addslashes($ConditionSubtypeId)."' ";

                echo "<br>";

              // echo $sql;

               mysql_query($sql) or die(mysql_error());
               
                
             
               
               
               
               
               
            }
                    
            
            $itemPriceAmount = '';
            $itemPriceCurrency = '';
            $GiftWrapAmount = '';
            $GiftWrapCurrency = '';
            $itemTaxAmount = '';
            $itemTaxCurrency = '';
            $shippingTaxAmount = '';
            $shippingTaxCurrency = '';
            $giftWrapTaxAmount = '';
            $giftWrapTaxCurrency = '';
            $shippingDiscountAmount = '';
            $shippingDiscountCurrency = '';
            $promotionDiscountAmount = '';
            $promotionDiscountCurrency = '';
            $PromotionIds = '';
            
            
        }
        
        
        
        //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

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

