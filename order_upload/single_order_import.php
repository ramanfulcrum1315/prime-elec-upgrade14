<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("max_execution_time", "150");
ini_set("memory_limit", "512M");

	require_once '../app/Mage.php';
	umask(0);
	Mage::app('default');

	echo 'Starting Import Script...<br /><br />';


	
	///import order csv and store into array
	$order_file_name='upload/'.$_GET['filename'];
	$customer_id=$_GET['cust_id'];
	$shipping_cost=$_GET['shipping_cost'];
	//$customer_id=54697;

	$customer_file_exists=file_exists($order_file_name);
	if($customer_file_exists){

		$order_arr=array();
		$order_csv = fopen($order_file_name,"r");
		while(! feof($order_csv))
		  {
		  $order_arr[]=fgetcsv($order_csv);
		  }
		fclose($order_csv);
	}
	else{
		echo 'order file does not seem to exist<br />';
	}
	//print_r($order_arr);



	  $customer = Mage::getModel('customer/customer')->load($customer_id);
	  $transaction = Mage::getModel('core/resource_transaction');
	  $storeId = $customer->getStoreId();
	  $storeId = 1;
	  $reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
	 
	 //$reservedOrderId=69691;
	 
	   $order = Mage::getModel('sales/order')
	  ->setIncrementId($reservedOrderId)
	  ->setStoreId($storeId)
	  ->setQuoteId(0)
	  ->setGlobal_currency_code('USD')
	  ->setBase_currency_code('USD')
	  ->setStore_currency_code('USD')
	  ->setOrder_currency_code('USD');
	 
	  // set Customer data
		 if($customer_id){
		  $order->setCustomer_email($customer->getEmail())
		  ->setCustomerFirstname($customer->getFirstname())
		  ->setCustomerLastname($customer->getLastname())
		  ->setCustomerGroupId($customer->getGroupId())
		  ->setCustomer_is_guest(0)
		  ->setCustomer($customer);
		 }
		 else{
		   $order->setCustomer_email($email)
		  ->setCustomerFirstname($cust_first_name)
		  ->setCustomerLastname($cust_last_name)
		  ->setCustomer_is_guest(1);
		 }	


	  // set Billing Address 
	  
	$billing = $customer->getDefaultBillingAddress();
	$billingAddress = Mage::getModel('sales/order_address')
	->setStoreId($storeId)
	->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
	->setCustomerId($customer->getId())
	->setCustomerAddressId($customer->getDefaultBilling())
	// ->setCustomer_address_id($billing->getEntityId())
	// ->setPrefix($billing->getPrefix())
	// ->setFirstname($billing->getFirstname())
	// ->setMiddlename($billing->getMiddlename())
	// ->setLastname($billing->getLastname())
	// ->setSuffix($billing->getSuffix())
	// ->setCompany($billing->getCompany())
	// ->setStreet($billing->getStreet())
	// ->setCity($billing->getCity())
	// ->setCountry_id($billing->getCountryId())
	// ->setRegion($billing->getRegion())
	// ->setRegion_id($billing->getRegionId())
	// ->setPostcode($billing->getPostcode())
	// ->setTelephone($billing->getTelephone())
	// ->setFax($billing->getFax())
	;
	$order->setBillingAddress($billingAddress);
	 
	// Set Shipping Address 
	$shipping = $customer->getDefaultShippingAddress();
	$shippingAddress = Mage::getModel('sales/order_address')
	->setStoreId($storeId)
	->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
	->setCustomerId($customer->getId())
	->setCustomerAddressId($customer->getDefaultShipping())
	// ->setCustomer_address_id($shipping->getEntityId())
	// ->setPrefix($shipping->getPrefix())
	// ->setFirstname($shipping->getFirstname())
	// ->setMiddlename($shipping->getMiddlename())
	// ->setLastname($shipping->getLastname())
	// ->setSuffix($shipping->getSuffix())
	// ->setCompany($shipping->getCompany())
	// ->setStreet($shipping->getStreet())
	// ->setCity($shipping->getCity())
	// ->setCountry_id($shipping->getCountryId())
	// ->setRegion($shipping->getRegion())
	// ->setRegion_id($shipping->getRegionId())
	// ->setPostcode($shipping->getPostcode())
	// ->setTelephone($shipping->getTelephone())
	// ->setFax($shipping->getFax())
	;
	 
	 
	 ///what is our shipping method?
	$order->setShippingAddress($shippingAddress)
	->setShipping_method('flatrate_flatrate');
	 
	  $orderPayment = Mage::getModel('sales/order_payment')
	  ->setStoreId($storeId)
	  ->setCustomerPaymentId(0)
	  ->setMethod('purchaseorder')
	  ->setPo_number(' - ');
	  $order->setPayment($orderPayment);
	  
	  
	 
	  $subTotal = 0;
	  $grand_total = 0;
	  $discounts = 0;
	  $tax_total = 0;
	  $product_count=count($order_arr);
	  for($i=0;$i<$product_count;$i++){
			
			$order_arr[$i][2]=str_replace("$","",$order_arr[$i][2]);
			//echo $order_arr[$i][2].'<br />';

			$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$order_arr[$i][0]); 
			if($_product) {
			
			$product_id=$_product->getId();
			$rowTotal = $order_arr[$i][2]*$order_arr[$i][1];
			$orderItem = Mage::getModel('sales/order_item')
			->setStoreId($storeId)
			->setQuoteItemId(0)
			->setQuoteParentItemId(NULL)
			->setProductId($product_id)
			->setProductType($_product->getTypeId())
			->setQtyBackordered(NULL)
			->setTotalQtyOrdered($order_arr[$i][1])
			->setQtyOrdered($order_arr[$i][1])
			->setName($_product->getName())
			->setSku($_product->getSku())
			->setPrice($order_arr[$i][2])
			->setBasePrice($order_arr[$i][2])
			->setOriginalPrice($order_arr[$i][2])
			->setRowTotal($rowTotal)
			->setBaseRowTotal($rowTotal)
			->setTaxAmount('')
			->setDiscountAmount('')
			->setDiscount('');

			$shipping_cost += 0;
			$tax_total += 0;
			$discounts -= 0;
			$subTotal += $rowTotal;

			$order->addItem($orderItem);
			
			}
			else{
			echo "product ".$order_arr[$i][0]." does not exist (possibly an empty line or header in the CSV)<br /><br />";
			}
	  }
		$subTotal+=$discounts;
		$subTotal+=$tax_total;
		$grand_total=$subTotal+$shipping_cost;
		$order->setSubtotal($subTotal)
		->setBaseSubtotal($subTotal)
		->setGrandTotal($grand_total)
		->setBaseGrandTotal($grand_total)
		->setShippingAmount($shipping_cost)
		->setTotalPaid($grand_total);
		$order->save();
		

		///////invoice the order
		//$thisinvoice = new Mage_Sales_Model_Order_Invoice_Api();
		//$invoiceId = $thisinvoice->create($reservedOrderId, $order->getItems(), '', false, false);
		
		/////ship the invoice
		//$thisShipment = new Mage_Sales_Model_Order_Shipment_Api();
		//$shipmentId = $thisShipment->create($reservedOrderId, $order->getItems(), '', false, false);
		 
		$transaction->addObject($order);
		$transaction->addCommitCallback(array($order, 'place'));
		$transaction->addCommitCallback(array($order, 'save'));
		$transaction->save(); 
		
		//Set status to 'pending' for now, uncomment below to set status to 'complete'
		//$order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true,false,false)->save();
		



	echo "Import Script Complete!<br /><br />";
	echo "Check orders in admin for confirmation of upload<br />";
?>