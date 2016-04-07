<?php
/*  � 2013 eBay Inc., All Rights Reserved */ 
/* Licensed under CDDL 1.0 -  http://opensource.org/licenses/cddl1.php */
//header("Content-Type: text/plain; charset=UTF-8");
//$response = simplexml_import_dom($responseDoc);

$ordersTable = ORDERS_TABLE;
$transactionsTable = TRANSACTIONS_TABLE;
$activeTable = ACTIVE_LISTING_TABLE;

if ($entries == 0) {
    echo "Sorry No entries found in the Time period requested. Change CreateTimeFrom/CreateTimeTo and Try again";
} else {
    $orders = $response->OrderArray->Order;
    if ($orders != null) {
        foreach ($orders as $order) {
			
		
			
			$sql = "INSERT INTO ".$ordersTable ." (orderId, SKU, OrderLineItemId, TransactionId, ItemID, quantity, "
			."AmountPaid, SalesTaxAmount, TransactionPrice, email, address, paypalTransactionId, paypalFee, orderStatus, date) "
			."VALUES ('$order->OrderID', )";
			
			//mysql_query($sql);
			
            echo "Order Information:\n";
            echo "OrderID ->" . $order->OrderID;
            echo "ShippedTime ->" . $order->ShippedTime;
            echo "Order -> Status:" . $orderStatus = $order->OrderStatus;
echo '<br>';
            //if the order is completed, print details
            if ($orderStatus) {

                // get the amount paid
                $AmountPaid = $order->AmountPaid;
                $AmountPaidAttr = $AmountPaid->attributes();
               // echo "AmountPaid : " . $AmountPaid . " "  .$AmountPaidAttr["currencyID"]. "\n";

//                // get the payment method 
//                if($order->PaymentMethod)
//                    echo "PaymentMethod : " . $order->PaymentMethod . "\n";
//
//
//                // get the checkout message left by the buyer, if any
//                if ($order->BuyerCheckoutMessage) {
//                    echo "BuyerCheckoutMsg : " . $order->BuyerCheckoutMessage . "\n";
//                }

                // get the sales tax, if any 
                $SalesTaxAmount = $order->ShippingDetails->SalesTax->SalesTaxAmount;
                $SalesTaxAmountAttr = $SalesTaxAmount->attributes();
                  //  echo "SalesTaxAmount : " . $SalesTaxAmount. " " .$SalesTaxAmountAttr["currencyID"] .  "\n";

                // get the external transaction information - if payment is made via PayPal, then this is the PayPal transaction info
                $externalTransaction = $order->ExternalTransaction;
                if ($externalTransaction) {
                   // echo "ExternalTransactionID  : " . $externalTransaction->ExternalTransactionID . "\n";
                    //echo "ExternalTransactionTime  : " . $externalTransaction->ExternalTransactionTime . "\n";
                    $externalTransactionFeeAttr = $externalTransaction->FeeOrCreditAmount->attributes();
                   // echo "ExternalFeeOrCreditAmount  : " . $externalTransaction->FeeOrCreditAmount . " " .$externalTransactionFeeAttr["currencyID"]  . " \n";
                    //echo "ExternalTransactionPaymentOrRefundAmount   : " . $externalTransaction->PaymentOrRefundAmount . " " .$externalTransactionFeeAttr["currencyID"]  . " \n";
                }

                // get the shipping service selected by the buyer
                $ShippingServiceSelected = $order->ShippingServiceSelected;
                if($ShippingServiceSelected){
             //   echo "Shipping Service Selected  : " . $ShippingServiceSelected->ShippingService . " \n";
                $ShippingCostAttr = $ShippingServiceSelected->ShippingServiceCost->attributes();
              //  echo "ShippingServiceCost  : " . $ShippingServiceSelected->ShippingServiceCost . " " . $ShippingCostAttr["currencyID"] . "\n";
                }
               
                // get the buyer's shipping address 
                $shippingAddress = $order->ShippingAddress;
                $address = $shippingAddress->Name . ",\n";
               
			    if ($shippingAddress->Street1 != null) {
                    $address .=  $shippingAddress->Street1 . ",";
                }
                if ($shippingAddress->Street2 != null) {
                    $address .=  $shippingAddress->Street2 . "\n";
                }
                if ($shippingAddress->CityName != null) {
                    $address .= 
                            $shippingAddress->CityName . ",\n";
                }
                if ($shippingAddress->StateOrProvince != null) {
                    $address .= 
                            $shippingAddress->StateOrProvince . "-";
                }
                if ($shippingAddress->PostalCode != null) {
                    $address .= 
                            $shippingAddress->PostalCode . ",\n";
                }
                if ($shippingAddress->CountryName != null) {
                    $address .= 
                            $shippingAddress->CountryName . ".\n";
                }
                if ($shippingAddress->Phone != null) {
                    $address .=  $shippingAddress->Phone . "\n";
                }
//                if($address){
//               //  echo "Shipping Address : " . $address;
//                }else echo "Shipping Address: Null" . "\n";

                
              
                echo '-------------------> '.$count['count'];
 
                 $sql =  "SELECT COUNT(*) as count FROM ".$ordersTable ." WHERE OrderID='".$order->OrderID."'";
                 
                $res = mysql_query($sql) or die(mysql_error());
                $count = mysql_fetch_assoc($res);
                
                if($count['count']>=1)
                {
                    // UPDATE ORDER
                    if($order->ShippedTime == '' or $order->ShippedTime == '0000-00-00 00:00:00')
                        $shippedTime = null;
                    else
                        $shippedTime = $order->ShippedTime;
                    $sql = "UPDATE ".$ordersTable ." SET status =  '".$orderStatus."', ShippedTime = '".$shippedTime."' WHERE OrderID='".$order->OrderID."'";
                    
                    mysql_query($sql);
                    continue;
                }
                
              
                echo '------------------------------><br>'.PHP_EOL;

                $sql = "INSERT INTO ".$ordersTable ." "
                ."(OrderID, AmountPaid, SalesTax, ExternalTransactionID, ExternalFeeOrCredit, date, ShippingServiceCost, shipName, street1, street2, city, state, zip, country, phone, note , status)"
                ." VALUES ('".$order->OrderID."', $AmountPaid, $SalesTaxAmount, '".$externalTransaction->ExternalTransactionID."', '".$externalTransaction->FeeOrCreditAmount."', '".$order->CreatedTime."', $ShippingServiceSelected->ShippingServiceCost, '"
                .trim(addslashes($shippingAddress->Name))."', '".trim(addslashes($shippingAddress->Street1))."' , '".trim(addslashes($shippingAddress->Street2))."', '".addslashes($shippingAddress->CityName)
                ."', '".addslashes($shippingAddress->StateOrProvince)."', '"
				.trim(addslashes($shippingAddress->PostalCode))."', '".$shippingAddress->CountryName
                ."', '".$shippingAddress->Phone."', '".addslashes($order->BuyerCheckoutMessage)."' , '".$orderStatus."' )";

                mysql_query($sql) or die(mysql_error());
				
                $transactions = $order->TransactionArray;
                if ($transactions) {
                    echo "Transaction Array \n";
                    // iterate through each transaction for the order
                    foreach ($transactions->Transaction as $transaction) {
                        // get the OrderLineItemID, Quantity, buyer's email and SKU

                        echo "OrderLineItemID : " . $transaction->OrderLineItemID . "\n";
                        echo "QuantityPurchased  : " . $transaction->QuantityPurchased . "\n";
                        echo "Buyer Email : " . $transaction->Buyer->Email . "\n";
                        $SKU = $transaction->Item->SKU;
                        if ($SKU) {
                            echo "Transaction -> SKU  :" . $SKU ."\n";
                        }
                        
                        // if the item is listed with variations, get the variation SKU
                        $VariationSKU = $transaction->Variation->SKU;
                        if ($VariationSKU != null) {
                            echo "Variation SKU  : " . $VariationSKU. "\n";
                        }
                        echo "TransactionID: " . $transaction->TransactionID . "\n";
                        $transactionPriceAttr = $transaction->TransactionPrice->attributes();
                        echo "TransactionPrice : " . $transaction->TransactionPrice . " " . $transactionPriceAttr["currencyID"] . "\n";
                        echo "Platform : " . $transaction->Platform . "\n";
						$paypalEmail = ($transaction->Buyer->Email!='Invalid Request')? $transaction->Buyer->Email : '';
						$itemId = explode('-', $transaction->OrderLineItemID);
						$itemId = $itemId[0];
						
						
            // INSERT TRANSACTION
            $sql = "INSERT INTO ".$transactionsTable." (orderId, SKU, OrderLineItemId, TransactionId, ItemID, quantity, "." TransactionPrice, email, VariationSKU, date) "
                    ."VALUES ('".$order->OrderID."', '".trim($SKU)."', '".$transaction->OrderLineItemID."', '"
                    .$transaction->TransactionID."', '".$itemId."', $transaction->QuantityPurchased, "
                    .(float)$transaction->TransactionPrice.", '".addslashes($paypalEmail)."', '".addslashes($VariationSKU)."', '"
                    .$externalTransaction->ExternalTransactionTime."'  )";


            mysql_query($sql) or die(mysql_error());

				
		
			
			//find location
			
			$sql = "SELECT location FROM ".$activeTable." WHERE listingId = ".$itemId;
			$res = mysql_query($sql);
			
			$location = mysql_fetch_assoc($res);
			$location =trim($location['location']);	
						
            
			
if($location != 'URO' && $location!='MTC' && $location!='' && $location!='IMC'){
				//UPDATE INVENTORY
            $sql = "UPDATE pepauto_active SET instock = instock - ".(int)$transaction->QuantityPurchased." WHERE location = '".$location."'";
            mysql_query($sql);
            
            $sql = "UPDATE part4engine_active SET instock = instock - ".(int)$transaction->QuantityPurchased." WHERE location = '".$location."'";
            mysql_query($sql);
			
			//UPDATE INVENTORY
             $sql = "UPDATE inventory SET qty = qty - ".(int)$transaction->QuantityPurchased." WHERE location = '".$location."'";
             mysql_query($sql);
			
			}
                    
					}  //foreach
					
					
                }
                
                
            }//end if
			
            echo "---------------------------------------------------- \n";
        }
    }else{
	echo "No Order Found";
	}
}
?>