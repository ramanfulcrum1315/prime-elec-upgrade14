<?php
/*
 * Created on Nov 28, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_Observer
{
   const ABANDONED_SETUP_PATH = 'emaildirect/general/abandonedsetup';
	const ABANDONED_SETUP_VERSION = 'emaildirect/general/setup_version';
	
	private function getVersion()
	{
		return (string) Mage::getConfig()->getNode('modules/EmailDirect_Integration/version');
	}
	
	/**
	 * Handle Subscriber object saving process
	 */
	public function handleSubscriber(Varien_Event_Observer $observer)
	{
		$subscriber = $observer->getEvent()->getSubscriber();
		$subscriber->setImportMode(false);

		$email  = $subscriber->getSubscriberEmail();
		$listId = Mage::helper('emaildirect')->getDefaultPublication($subscriber->getStoreId());
		$isConfirmNeed = (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRMATION_FLAG, $subscriber->getStoreId()) == 1) ? TRUE : FALSE;

		//New subscriber, just add
		if( $subscriber->isObjectNew() ){

			if( TRUE === $isConfirmNeed ){
				$subscriber->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED);
				if( !Mage::helper('customer')->isLoggedIn() && Mage::registry('ed_guest_customer')) {
					$guestCustomer = Mage::registry('ed_guest_customer');
					$subscriber->setFirstname($guestCustomer->getFirstname());
					$subscriber->setLastname($guestCustomer->getLastname());
					Mage::unregister('ed_guest_customer');
					$subscriber->save();
				}
			}
			else {
				$subscriber->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
				$mergeVars = $this->_mergeVars($subscriber);
				Mage::getSingleton('emaildirect/wrapper_suscribers')
									->suscriberAdd($email,$mergeVars);
			}
		}else{
			$status    = (int)$subscriber->getData('subscriber_status');
			
			$oldSubscriber = Mage::getModel('newsletter/subscriber')
								->load($subscriber->getId());
			$oldstatus = (int)$oldSubscriber->getOrigData('subscriber_status');
			if($oldstatus == Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED && $status == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
				$subscriber->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
				$mergeVars = $this->_mergeVars($subscriber);
				Mage::getSingleton('emaildirect/wrapper_suscribers')
									->suscriberAdd($email,$mergeVars);
			}
			elseif( $status !== $oldstatus ){ //Status change
				//Unsubscribe customer
				if($status == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED){
					Mage::getSingleton('emaildirect/wrapper_publications')
									->unsubscribe($listId, $email);
				}else if($status == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED){
					if( $oldstatus == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE || $oldstatus == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED ){
						Mage::getSingleton('emaildirect/wrapper_publications')
									->subscribe($listId, $email);
					}
				}
			}
		}
	}

	/**
	 * Handle Subscriber deletion from Magento, unsubcribes email
	 * and sends the delete_member flag so the subscriber gets deleted.
	 */
	public function handleSubscriberDeletion(Varien_Event_Observer $observer)
	{
		$subscriber = $observer->getEvent()->getSubscriber();
		$subscriber->setImportMode(TRUE);

		$listId = Mage::helper('emaildirect')->getDefaultList($subscriber->getStoreId());
	}
	
	private function addDatabaseField($data)
	{
		$name = $data['name'];
		$type = $data['type'];
		
		if (!Mage::getSingleton('emaildirect/wrapper_database')->exists($name))
      {
      	if (isset($data['size']))
				$rc = Mage::getSingleton('emaildirect/wrapper_database')->add($name,$type,$data['size']);
			else
         	$rc = Mage::getSingleton('emaildirect/wrapper_database')->add($name,$type);
			
         if(isset($rc->ErrorCode) && $rc->ErrorCode != 233)
			{
				Mage::helper('emaildirect')->debugLog("Error adding {$name} field");
				Mage::helper('emaildirect')->debugLog($rc);
            Mage::throwException("Error adding {$name} field");
			}
         
         if (!Mage::getSingleton('emaildirect/wrapper_database')->exists($name))
			{
				Mage::helper('emaildirect')->debugLog("Error creating {$name} field");
				Mage::helper('emaildirect')->debugLog($rc);
            Mage::throwException("Error creating {$name} field");
			}
      }
	}
	
	private function addMultiFields($prefix = "", $count = null)
	{
		if ($count == null)
			$count = Mage::helper('emaildirect')->config('product_fields');
			
		for ($i = 1; $i <= $count; $i++)
		{
			$multi_fields = array(
				array('name' => "{$prefix}ProductName{$i}",
						'type' => 'Text',
						'size' => '200'),
				array('name' => "{$prefix}SKU{$i}",
						'type' => 'Text',
						'size' => '50'),
				array('name' => "{$prefix}URL{$i}",
						'type' => 'Text',
						'size' => '200'),
				array('name' => "{$prefix}Image{$i}",
						'type' => 'Text',
						'size' => '200'),
				array('name' => "{$prefix}Description{$i}",
						'type' => 'Text',
						'size' => '200'),
				array('name' => "{$prefix}Cost{$i}",
						'type' => 'Text',
						'size' => '20')
					);

			Mage::helper('emaildirect')->debugLog("Multi Field:");
			Mage::helper('emaildirect')->debugLog($multi_fields);

			foreach ($multi_fields as $data)
			{
				$this->addDatabaseField($data);
				
			}
		}
	}
	
	private function verifyAbandonedFields()
	{
		Mage::helper('emaildirect')->debugLog("verifyAbandonedFields Start");
		$fields = array(
				array('name' => 'AbandonedDate',
						'type' => 'Date'),
				array('name' => 'AbandonedUrl',
						'type' => 'Text',
						'size' => '1000')
					);
		
		foreach ($fields as $data)
		{
			$this->addDatabaseField($data);
		}
		
		$this->addMultiFields("AB");
		
		// Save version so we know if we are up to date or not
      Mage::getConfig()->saveConfig(self::ABANDONED_SETUP_PATH, 1,"default","default");
		Mage::getConfig()->saveConfig(self::ABANDONED_SETUP_VERSION, $this->getVersion(),"default","default");
		Mage::helper('emaildirect')->debugLog("verifyAbandonedFields End");
	}
	
	private function verifyProductFields()
	{
		$fields = array(
				array('name' => 'LastOrderNumber',
						'type' => 'Text',
						'size' => '30'),
				array('name' => 'LastPurchaseDate',
						'type' => 'Date'),
				array('name' => 'LastPurchaseTotal',
						'type' => 'Text',
						'size' => '20')
					);
		
		foreach ($fields as $data)
		{
			$this->addDatabaseField($data);
		}
		
		$this->addMultiFields();
		
		$this->addMultiFields("Related", Mage::helper('emaildirect')->config('related_fields'));
	}

	public function saveConfig(Varien_Event_Observer $observer)
	{
		$store  = is_null($observer->getEvent()->getStore()) ? 'default': $observer->getEvent()->getStore();
		$post   = Mage::app()->getRequest()->getPost();
		$apiKey = isset($post['groups']['general']['fields']['apikey']['value']) ? $post['groups']['general']['fields']['apikey']['value'] : Mage::helper('emaildirect')->config('apikey');
		if(!$apiKey){
			return $observer;
		}
      
		$source = isset($post['groups']['general']['fields']['source']['value']) ? $post['groups']['general']['fields']['source']['value'] :  Mage::helper('emaildirect')->config('source');

		if($source == '') {
			$source = "Magento";
			$config =  new Mage_Core_Model_Config();
	        $config->saveConfig('emaildirect/general/source',$source,"default",$store);
	        Mage::getConfig()->cleanCache();
		}
		//check if the source exist
		$sources = Mage::getSingleton('emaildirect/wrapper_sources')->getSources();
		$found = false;
		foreach($sources as $item)
		{
			if($item['name']==$source) {
				$found = true;
				$sourceid = $item['id'];
//				Mage::log($item);
				break;
			}
		}

		if(!$found) {
			$rc = Mage::getSingleton('emaildirect/wrapper_sources')->addSource($source);
			if(!isset($rc->SourceID)) {
				Mage::throwException("Error adding source");
			}
			else {
				$config =  new Mage_Core_Model_Config();
	            $config->saveConfig('emaildirect/general/sourceid',$rc->SourceID,"default",$store);
	            Mage::getConfig()->cleanCache();
			}
		}
		else {
			$config =  new Mage_Core_Model_Config();
			$config->saveConfig('emaildirect/general/sourceid',$sourceid,"default",$store);
			Mage::getConfig()->cleanCache();
		}
		
		if (isset($post['groups']['general']['fields']['save_latest_order']['value']) && $post['groups']['general']['fields']['save_latest_order']['value'] == true)
		{
			$this->verifyProductFields();
		}
		
		//add the custom fields for abandoned cart
		$this->verifyAbandonedFields();
	}

	public function _mergeVars($object = NULL, $includeEmail = FALSE)
	{
		//Initialize as GUEST customer
		$customer = new Varien_Object;

		$regCustomer   = Mage::registry('current_customer');
		$guestCustomer = Mage::registry('ed_guest_customer');

		if( Mage::helper('customer')->isLoggedIn() ){
		   $customer = Mage::helper('customer')->getCustomer();
		}elseif($regCustomer){
		   $customer = $regCustomer;
		}elseif($guestCustomer){
		   $customer = $guestCustomer;
		}else{
			/*if(is_null($object)){
			   $customer->setEmail($object->getSubscriberEmail())
					 ->setStoreId($object->getStoreId());
			}else{
			   $customer = $object;
			}*/
         $customer = $object;
		}
      
      $address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
      if ($address)
         $customer->setBillingAddress($address);

		$mergeVars = Mage::helper('emaildirect')->getMergeVars($customer, $includeEmail);

		return $mergeVars;
	}
	
	private function addMergeItem($item, $pos, $mergeVars, $prefix = "")
	{
		$name = "";
		$sku = "";
		$url = "";
		$image = "";
		$cost = "";
		$description = "";
		
		if ($item != null)
		{
			$product_id = 0;
			
			if (is_string($item))
				$product_id = $item;
			else
				$product_id = $item->getProduct()->getId();
			
			$product = Mage::getModel('catalog/product')->load($product_id);
			
			if ($product != null)
			{
				$name = $product->getName();
				$sku = $product->getSku();
				$url = $product->getProductUrl();
				
				if ($product->getImage() != 'no_selection')
					$image = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
				
				if (is_string($item))
					$cost = $product->getPrice();
				else
					$cost = $item->getPrice();
				
				$cost = number_format($cost, 2, '.', '');
				$description = $product->getShortDescription();
			} 
		}
      
		$mergeVars["{$prefix}ProductName{$pos}"] = $name;
		$mergeVars["{$prefix}SKU{$pos}"] = $sku;
		$mergeVars["{$prefix}URL{$pos}"] = $url;
		$mergeVars["{$prefix}Image{$pos}"] = $image;
		
		$mergeVars["{$prefix}Cost{$pos}"] = $cost;
		$mergeVars["{$prefix}Description{$pos}"] = $description;
		
		return $mergeVars;
	}

	private function getRelatedOrderItems($quote, $mergeVars)
	{
		$prefix = "Related";
		
		$max_count = Mage::helper('emaildirect')->config('related_fields');
		
		$id_list = array();
		
		foreach($quote->getAllItems() as $item) 
      {
      	$id_list[] = $item->getProduct()->getId();
      }
		
		$collection = Mage::getModel('catalog/product_link')
                    ->getCollection()
                    ->addFieldToFilter('product_id', array('in' => $id_list))
                    ->addFieldToFilter('link_type_id','1')
						  ->setPageSize($max_count);

		$related_products = $collection->getData();
		
		$count = 0;

		foreach ($related_products as $rp)
		{
			$count++;
			
			if ($count > $max_count)
				break;
			
			$mergeVars = $this->addMergeItem($rp['linked_product_id'], $count, $mergeVars, $prefix);
		}
		
		while ($count < $max_count)
		{
			$count++;
			$mergeVars = $this->addMergeItem(null, $count, $mergeVars, $prefix);
		}
		
		return $mergeVars;
	}
	
	private function getMergeOrderItems($quote, $mergeVars, $prefix = "")
	{
		$max_count = Mage::helper('emaildirect')->config('product_fields');
		
		$count = 0;
		
		foreach($quote->getAllItems() as $item) 
      {
      	$count++;
			
			if ($count > $max_count)
				break;
			
         $mergeVars = $this->addMergeItem($item, $count, $mergeVars, $prefix);
      }
		
		while ($count < $max_count)
		{
			$count++;
			$mergeVars = $this->addMergeItem(null, $count, $mergeVars, $prefix);
		}
		
		return $mergeVars;
	}
	
	public function updateCustomer(Varien_Event_Observer $observer)
	{
	   $store = Mage::app()->getStore()->getId();
		$customer = $observer->getEvent()->getCustomer();

		$mergeVars = $this->_mergeVars($customer, TRUE);
		
		$api   = Mage::getSingleton('emaildirect/wrapper_suscribers');

		$oldEmail = $customer->getOrigData('email');
		$email = $customer->getEmail();
		if($oldEmail == '') {
		   $api->suscriberAdd($email,$mergeVars);
		}
		elseif($oldEmail != $email)
		{
		   $api->mailModify($oldEmail,$email);
         $api->suscriberAdd($email,$mergeVars);
		}

		return $observer;
	}

	public function registerCheckoutSubscribe(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('emaildirect')->canEdirect()){
			return;
		}
		$subscribe = Mage::app()->getRequest()->getPost('emaildirect_subscribe');

		if(!is_null($subscribe)){
			Mage::getSingleton('core/session')->setEmaildirectCheckout($subscribe);
		}
	}

	/**
	 * Subscribe customer to Newsletter if flag on session is present
	 */
	public function registerCheckoutSuccess(Varien_Event_Observer $observer)
	{
		if(!Mage::helper('emaildirect')->canEdirect()){
			return;
		}
		$sessionFlag = Mage::getSingleton('core/session')->getEmaildirectCheckout(TRUE);
		if($sessionFlag){
			$orderId = (int)current($observer->getEvent()->getOrderIds());

			if($orderId){
				$order = Mage::getModel('sales/order')->load($orderId);
				if( $order->getId() ){

						//Guest Checkout
						if( (int)$order->getCustomerGroupId() === Mage_Customer_Model_Group::NOT_LOGGED_IN_ID ){
							Mage::helper('emaildirect')->registerGuestCustomer($order);
						}

						$subscriber = Mage::getModel('newsletter/subscriber')
							->subscribe($order->getCustomerEmail());
				}
			}
		}
	}
	
	public function salesOrderShipmentTrackSaveAfter(Varien_Event_Observer $observer)
	{
		$track = $observer->getEvent()->getTrack();
		
		//Zend_debug::dump($track);
	
		$order = $track->getShipment()->getOrder();
		$shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
		if (!$shippingMethod)
	      return;
		
		$email = $order->getCustomerEmail();
		
		$mergeVars = array();
		
		$mergeVars = Mage::helper('emaildirect')->getTrackingMergeVars($track, $order);
		
		Mage::getSingleton('emaildirect/wrapper_orders')->addSubscriberTracking($email, $mergeVars);
	}
	
	public function orderSaveAfter(Varien_Event_Observer $observer)
	{
	   $sendit = Mage::helper('emaildirect')->config('sendorder');
		
		if ($sendit)
		{
			$order = $observer->getEvent()->getOrder();
         if($order->getState() === Mage_Sales_Model_Order::STATE_COMPLETE)
         {
            $email = $order->getCustomerEmail();
  
            if ($order->getData('customer_is_guest'))
            {
               $customer = new Varien_Object;
               
               $customer->setData('email',$email);
               $customer->setData('firstname',$order->getData('customer_firstname'));
               $customer->setData('lastname',$order->getData('customer_lastname'));
               $customer->setData('store_id',$order->getStoreId());
               
               $customer->setBillingAddress($order->getBillingAddress());
            }
            else
            {
               $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
               $address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
               $customer->setBillingAddress($address);
            }

			   $mergeVars = Mage::helper('emaildirect')->getMergeVars($customer, true);
				$mergeVars = Mage::helper('emaildirect')->getOrderMergeVars($mergeVars,$order);
				
				if (Mage::helper('emaildirect')->config('save_latest_order'))
				{
					$this->checkProductFields();
					$this->checkAbandonedFields(true);
					
					$mergeVars['LastOrderNumber'] = $order->getIncrementId();
					$mergeVars['LastPurchaseDate'] = $order->getData('created_at');
					$mergeVars['LastPurchaseTotal'] = number_format($order->getData('total_paid'), 2, '.', '');
					
					$mergeVars = $this->getMergeOrderItems($order, $mergeVars);
					$mergeVars = $this->getRelatedOrderItems($order, $mergeVars);
				}

				Mage::helper('emaildirect')->debugLog("MERGE VARS:");
				Mage::helper('emaildirect')->debugLog($mergeVars);
            
            Mage::getSingleton('emaildirect/wrapper_orders')->addSubscriberOrder($email,$order, $mergeVars);
         }
		}
		return $observer;
	}
   
   // START ABANDONED CRON
   private function getAbandonedTime()
   {
      $time = Mage::helper('emaildirect')->config('abandonedtime');
      $time *= 60; // Adjust to seconds.
 
      $date = date(Mage::getModel('core/date')->gmtTimestamp());
 
      $date = $date - $time;
 
      return date("Y-m-d H:i:s", $date);
   }
	
	private function getLastOrder($quote)
	{
		Mage::helper('emaildirect')->debugLog("Get Last Order");
		$customer_id = $quote->getData('customer_id');
		
		Mage::helper('emaildirect')->debugLog("Customer ID: {$customer_id}");
		
		$orders = Mage::getResourceModel('sales/order_collection')
		    ->addFieldToSelect('*')
		    ->addFieldToFilter('customer_id', $customer_id)
		    ->addAttributeToSort('created_at', 'DESC')
		    ->setPageSize(1);
			 
		Mage::helper('emaildirect')->debugLog("Order Count: " . $orders->getSize());
		if ($orders->getSize() <= 0)
			return null;
 
 		Mage::helper('emaildirect')->debugLog("Order Found");
		$order = $orders->getFirstItem();
		
		return $order;
	}
	
   private function processAbandoned($quote)
   {
      //$customer = $quote->getCustomer();
      $email = $quote->getCustomerEmail();
      
      $abandonedDate = $quote->getUpdatedAt();
      
      //$mergeVars = Mage::helper('emaildirect')->getMergeVars($customer, true);
      $mergeVars = array();
      
      $mergeVars['FirstName'] = $quote->getData('customer_firstname');
      $mergeVars['LastName'] = $quote->getData('customer_lastname');
      
      $mergeVars['AbandonedDate'] = $abandonedDate;
      $mergeVars['AbandonedUrl'] = $quote->getEmaildirectAbandonedUrl();
		
		$mergeVars = $this->getMergeOrderItems($quote, $mergeVars, "AB");
		
		$order = $this->getLastOrder($quote);
		
		if ($order != null)
		{
			$mergeVars = Mage::helper('emaildirect')->getOrderMergeVars($mergeVars,$order);
		}
		
		Mage::helper('emaildirect')->debugLog("Check Save Lastest");
		if (Mage::helper('emaildirect')->config('save_latest_order'))
		{
			Mage::helper('emaildirect')->debugLog("Get Order");
			
			if ($order != null)
			{
				Mage::helper('emaildirect')->debugLog("Order Found");
				$mergeVars['LastOrderNumber'] = $order->getIncrementId();
				$mergeVars['LastPurchaseDate'] = $order->getData('created_at');
				$mergeVars['LastPurchaseTotal'] = number_format($order->getData('total_paid'), 2, '.', '');
				
				$mergeVars = $this->getMergeOrderItems($order, $mergeVars);
				$mergeVars = $this->getRelatedOrderItems($order, $mergeVars);
			}
			
			Mage::helper('emaildirect')->debugLog("Finish Save Latest");
		}

      $xml = Mage::getSingleton('emaildirect/wrapper_abandoned')->getOneSubscriber($email,$mergeVars);
      
      // Set the abandoned date so we don't process this again.
      $quote->setEmaildirectAbandonedDate($quote->getUpdatedAt());
      $quote->save();
      
      return $xml;
   }
	
	private function checkAbandonedFields()
	{
		$version = $this->getVersion();
		
		$setup_version = Mage::helper('emaildirect')->config('setup_version');
		
		if ($setup_version == NULL || (version_compare($setup_version, $version) < 0))
			$this->verifyAbandonedFields();
	}
	
	private function checkProductFields()
	{
		if (!Mage::helper('emaildirect')->config('save_latest_order'))
			return;
		
		$version = $this->getVersion();
		
		$setup_version = Mage::helper('emaildirect')->config('setup_version');
		
		if ($setup_version == NULL || (version_compare($setup_version, $version) < 0))
			$this->verifyProductFields();
	}
   
   public function abandonedCartsProcessor()
   {
   	Mage::helper('emaildirect')->debugLog("Abandoned Carts Processor Start");
		
      $sendit = Mage::helper('emaildirect')->config('sendabandoned');
      $setup = Mage::helper('emaildirect')->config('abandonedsetup');
      
      if (!$sendit)
		{
			Mage::helper('emaildirect')->debugLog("Skipping... SendIt = False");
         return;
		}
		
		if (!$setup)
		{
			Mage::helper('emaildirect')->debugLog("Skipping... Not Setup");
         return;
		}
		
		$this->checkProductFields();
		$this->checkAbandonedFields();
      
      $check_date = $this->getAbandonedTime();
		
		Mage::helper('emaildirect')->debugLog("Check Date: {$check_date}");
      
      // Get abandoned collection
      $collection = Mage::getResourceModel('reports/quote_collection');
      
      $collection->addFieldToFilter('items_count', array('neq' => '0'))
            ->addFieldToFilter('main_table.is_active', '1')
            ->setOrder('updated_at');

      $collection->addFieldToFilter('main_table.updated_at', array('lt' => $check_date));
      $collection->addFieldToFilter('main_table.emaildirect_abandoned_date', array('null' => true));
      $collection->addFieldToFilter('main_table.emaildirect_abandoned_url', array('notnull' => true));
      $collection->addFieldToFilter('main_table.customer_email', array('notnull' => true));
		
		Mage::helper('emaildirect')->debugLog("SQL: " . $collection->getSelect()->__toString());
      
      $subscribers = false;
      
      $xml = "<Subscribers>";
      
      // Get the data for each abandoned cart
      foreach ($collection as $quote)
      {
         $xml .= $this->processAbandoned($quote);
         $subscribers = true;
      }
      
      $xml .= "</Subscribers>";
      
      if (!$subscribers)
      {
      	Mage::helper('emaildirect')->debugLog("Exiting... No Carts Found");
         return; // No abandoned carts found
      }
      
      Mage::helper('emaildirect')->debugLog("Subscriber Xml:");
      Mage::helper('emaildirect')->debugLog($xml);
      
      Mage::helper('emaildirect')->debugLog("Sending Abandoned Carts");
      // Send them all at once
      $rc = Mage::getSingleton('emaildirect/wrapper_abandoned')->sendSubscribers($xml);
		
		Mage::helper('emaildirect')->debugLog("Results:");
		Mage::helper('emaildirect')->debugLog($rc);
		
		Mage::helper('emaildirect')->debugLog("Abandoned Carts Processor End");
   }
   // END ABANDONED CRON

   // START QUOTE SAVE AFTER
   private function getAbandonedUrl($quote)
   {
      // We are using comma separated lists for the ID's and Quantities so that it takes up less
      // space when we generate the Querystring
      
      $ids = "";
      $qtys = "";
      
      foreach($quote->getAllItems() as $item) 
      {
         if ($ids != "")
         {
            $ids .= ",";
            $qtys .= ",";
         }
         
         $product = $item->getProduct();
         
         $ids .= $product->getId();
         $qtys .= $item->getQty();
      }
      
      $url_data = array("quote" => $quote->getId(), "id" => $ids, "qty" => $qtys);

      $url = base64_encode(serialize($url_data));
      
      $url = Mage::getUrl('emaildirect/abandoned/restore',array('_secure'=>true)) . "?cart={$url}";
      
      return $url;
   }
   
   public function quoteSaveAfter(Varien_Event_Observer $observer)
   {
   	Mage::helper('emaildirect')->debugLog("Quote Save After Start");
      $quote = $observer->getEvent()->getQuote();

      if (trim($quote->getEmaildirectAbandonedDate()))
		{
			Mage::helper('emaildirect')->debugLog("Skipping... Already sent.");
         return;
		}
       
      $url = $this->getAbandonedUrl($quote);
		
		Mage::helper('emaildirect')->debugLog("Abandoned Url: {$url}");
      
      if ($quote->getEmaildirectAbandonedUrl() == $url)
		{
			Mage::helper('emaildirect')->debugLog("Skipping... Abandoned Url is up to date.");
         return;
		}
      
		Mage::helper('emaildirect')->debugLog("Saving Quote.");
      // Re-save quote with new URL
      $quote->setEmaildirectAbandonedUrl($url);
      $quote->save();
		Mage::helper('emaildirect')->debugLog("Quote Save After End");
   }
   // END QUOTE SAVE AFTER
}