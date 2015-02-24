<?php

	error_reporting(0);
	
class Camiloo_Amazonimport_Model_Observer {

	public function refreshMenu(){
	 Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS));	
	}

	public function categorywasmoved($observer){
	
		$marketplace = array('com','uk','de','fr');
	    $db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$_sql = "DELETE FROM {$table_prefix}amazonimport_categorymapping WHERE country_id='$mkt' AND inherited=1";
		$db->query($_sql);
		  
		// flush all inherited values
		// for each marketplace
		foreach($marketplace as $mkt){
			// get all non-inherited values
			  $db = Mage::getSingleton("core/resource")->getConnection("core_write");
		  $table_prefix = Mage::getConfig()->getTablePrefix();
		
	  	  $_sql = "SELECT * FROM {$table_prefix}amazonimport_categorymapping WHERE country_id='$mkt' AND inherited=0";
		  $result = $db->query($_sql);		
			
			// while
			while($row = $result->fetch(PDO::FETCH_ASSOC)){
				// load the category
					$category = Mage::getModel('catalog/category')->load($row['category_id']);
				// run the recursive processor
					$this->recursiveprocessor($category,$row, $mkt);
				// end while
			}
		
		// end for
		}
	}
	
	
	public function categorywasdeleted($observer){
		
		  // if something was deleted, we may need to remove a non-inherited value.
		  $category = $observer->getEvent()->getCategory();
		  $categoryid = $category->getId();
		  // remove anything in our category mapping db with this category ID.
		  $db = Mage::getSingleton("core/resource")->getConnection("core_write");
		  $table_prefix = Mage::getConfig()->getTablePrefix();
		
		  $_sql = "DELETE FROM {$table_prefix}amazonimport_categorymapping WHERE category_id=$categoryid";
		  $db->query($_sql);
		  
		  // then call categorywasmoved to run the standard traversal processor.
		  $this->categorywasmoved($observer);	
	
	}
	
	public function categorywassaved($observer){
 		  // if a category was saved, we may have a new inherited child
		  // therefore call categorywasmoved to run the standard traversal processor.
		  $this->categorywasmoved($observer);
	}
	
	
	public function recursiveprocessor($category, $inheritData, $marketplace, $inherited = false){
		  $db = Mage::getSingleton("core/resource")->getConnection("core_write");
		  $table_prefix = Mage::getConfig()->getTablePrefix();
		  $categoryid = $category->getId();
		
			// get level of this category
			$level = $category->getData('level');
			
			// update the level of this category.
			if($inherited == false){
				$_sql = "UPDATE {$table_prefix}amazonimport_categorymapping SET level='$level' WHERE category_id='$categoryid' AND country_id='$marketplace'";	
				$db->query($_sql);
				
			}else{
				// check whether we have a value for this category, if inherited is not false.
		  		// if so, we must stop here.
				
				$_sql = "SELECT count(*) as count FROM {$table_prefix}amazonimport_categorymapping WHERE category_id='$categoryid' AND country_id='$marketplace' GROUP BY category_id";	
				$result = $db->query($_sql);
				$row = $result->fetch(PDO::FETCH_ASSOC);
				
				if($row['count'] > 0){
					return "";
				}else{			
					
					// else, insert an inherited row.
					$_sql = "REPLACE INTO {$table_prefix}amazonimport_categorymapping (`category_id`,`country_id`,`browsenode1`,`browsenode2`,`itemtype`,`variation_theme`,`inherited`,`level`,`condition`,`condition_note`)
					VALUES ('".$inheritData['category_id']."','".$inheritData['country_id']."','".$inheritData['browsenode1']."','".$inheritData['browsenode2']."','".$inheritData['itemtype']."','".$inheritData['variation_theme']."','1','".$level."','".$inheritData['condition']."','".$inheritData['condition_note']."')";
				    $db->query($_sql);
				
				}
				
				
			}
			
			// get children of this category.
			foreach($category->getChildrenCategories() as $childcat){
				// for each...
				$this->recursiveprocessor($childcat, $inheritData,$marketplace,true);
				//...loop
			}
			
			return "";
	}
	


	public function checkForCancellation(Varien_Event_Observer $observer){
		
		$order = $observer->getOrder();
				
		if($order->getState() === 'canceled'){
		
			// order is cancelled and has been saved. most likely cause: cancellation.
			
			if(version_compare(Mage::getVersion(), "1.4.1.0", ">=")){
				
				$data = Mage::getModel('amazonimport/amazonimport')->flatordersLookupHelper($order->getId());
				if(sizeof($data) > 0){
					$amazonid = $data[0];
					$country = $data[1];
				}
			
			}else{
			    try {
				
				$amazon_resource = Mage::getResourceModel('amazonimport/amazonimport_orderDetails_Collection');
				$amazon_data = $amazon_resource->addAttributeToSelect('amazon_order_id')->addAttributeToSelect('amazon_country_id')
				->setOrderFilter($order->getId())->load();	
				
				foreach ($amazon_data as $amazon) {
					$amazonid = $amazon->getAmazonOrderId();
					$country = $amazon->getAmazonCountryId();
				}
			    } catch (Exception $e) { }
			}
				
			if(isset($amazonid)){
				
				if($country == "us"){
						
					$mpid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('com');		
					$mid = Mage::getStoreConfig('amazonint/amazoncom/mid');		
					$url = "https://mws.amazonservices.com";
				
				}else if($country == "uk"){
					
					$mpid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('uk');		
					$mid = Mage::getStoreConfig('amazonint/amazonuk/mid');		
					$url = "https://mws.amazonservices.co.uk";
									
				}else if($country == "fr"){
					
					$mpid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('fr');		
					$mid = Mage::getStoreConfig('amazonint/amazonfr/mid');		
					$url = "https://mws.amazonservices.fr";
		
				}else if($country == "de"){
					
					$mpid = Mage::getModel('amazonimport/amazonimport')->getMarketplaceId('de');		
					$mid = Mage::getStoreConfig('amazonint/amazonde/mid');		
					$url = "https://mws.amazonservices.de";
							
				}
				
				
				$model = Mage::getModel('amazonimport/amazonimportlog');
				$model->setOutgoing("Order ".$order->getIncrementId()." has been cancelled - notify Amazon");
				$model->setIncoming("");
				$model->setError("Notification.");
				$model->setMessageTime(date("c"));
				$model->save();	
				
				
				$amazon = Mage::getModel('amazonimport/amazonlink');
				$amazon->cancelOrder($mpid,$mid,$url,$amazonid);
			}
		}
		
	}
	
	public function orderWasPlaced(Varien_Event_Observer $observer)
	{
		if (is_object($observer)) {
			
			$ev = $observer->getEvent();
		
			if (is_object($ev)) {
				
				$order = $ev->getOrder();
				
				if (is_object($order)) {
					
					$items = $order->getAllItems();
					
					$this->getItemsForUpdateCommon($items);
					
				}
			}
		}
	}
	
	public function getItemsForUpdate(Varien_Event_Observer $observer)
	{
		if (is_object($observer) && is_object($observer->getInvoice()))
		{
			$order = $observer->getInvoice()->getOrder();
			
			if (is_object($order))
			{
				$items = $order->getAllItems();
				$this->getItemsForUpdateCommon($items);
			}
		}
	}
	
	public function getItemsForUpdateCommon($items) {
		ini_set('display_errors', 'on');
		error_reporting(E_ALL);
		$countryarray = array("uk", "com", "de", "fr");
		
		foreach($items as $item){
			
			if($item->getProductType() == "simple"){
				$sku = $item->getSku();
				
				$prodTemp = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
				if (!$prodTemp) {
					
					continue;
				}
				
				$productid = $prodTemp->getId();
				$db = Mage::getSingleton("core/resource")->getConnection("core_write");
				$table_prefix = Mage::getConfig()->getTablePrefix();
				
				foreach($countryarray as $country){
					if($this->canSellOnCountry($country)){
							
						$queryString = "select * from {$table_prefix}amazonimport_setup_".$country." as setup, {$table_prefix}amazonimport_listthis_$country as listthis
							WHERE setup.initial_setup_complete = 1 AND setup.productid = $productid AND setup.productid = listthis.productid AND listthis.is_active=1 AND setup.productid not in 
							(select productid from {$table_prefix}amazonimport_errorlog_".$country." where submission_type='Product')";
						
						$result = $db->query($queryString);
						
						while($row = $result->fetch(PDO::FETCH_ASSOC)){
							
							$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Stock')");
						}
					}
				}
			}
		}
		
		
	}
	
	public function canSellOnCountry($country_code) {
		
		return Mage::getStoreConfig("amazonint/amazon".$country_code."/mid") != "";
	}
	
	public function productWasSaved(Varien_Event_Observer $observer){
		
		$productid = $observer->getEvent()->getProduct()->getId();
		
		
		$relationsNeeded = $observer->getEvent()->getProduct()->isConfigurable()
						|| $observer->getEvent()->getProduct()->isGrouped();
						
						
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$countryarray = array("uk","com","de","fr");
		
		foreach($countryarray as $country)
		{
			
			if($this->canSellOnCountry($country))
			{
				$queryString = "select * from {$table_prefix}amazonimport_setup_".$country." as setup, {$table_prefix}amazonimport_listthis_$country as listthis
					WHERE setup.initial_setup_complete = 1 AND setup.productid = $productid AND setup.productid = listthis.productid AND listthis.is_active=1 AND setup.productid not in 
					(select productid from {$table_prefix}amazonimport_errorlog_".$country." where submission_type='Product')";
				
				$result = $db->query($queryString);
				
				while($row = $result->fetch(PDO::FETCH_ASSOC)){
					$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Product')");
					$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Image')");
					
					if ($relationsNeeded)
					{
						$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Relation')");
					}
					$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Stock')");
					$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Price')");
				}
			}
		}
		
	}
	
	public function stockWasSaved(Varien_Event_Observer $observer){
		
		
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$countryarray = array("uk","com","de","fr");
		$productid = $observer->getStockItem()->getProduct()->getId();
	
		foreach($countryarray as $country){
			if($this->canSellOnCountry($country)){
				$db->query("REPLACE INTO {$table_prefix}amazonimport_errorlog_".$country." (`productid`,`submission_type`) VALUES (".$productid.",'Stock')");							
			}
		}
		
	}
	
	public function shipAmazon(Varien_Event_Observer $observer)
    {
    	$shiponcomplete = Mage::getStoreConfig('amazonint/general/shiponcomplete');
    	
		if ($shiponcomplete)
		{
			/*
			 * Send shipment to Amazon when shipment is first made.
			 */
			
	        $shipment = $observer->getEvent()->getShipment();
	        $order = $shipment->getOrder();
			
		
			if(version_compare(Mage::getVersion(), "1.4.1.0", ">=")){
				
				$data = Mage::getModel('amazonimport/amazonimport')->flatordersLookupHelper($order->getId());
				if(sizeof($data) > 0){
					$amazonid = $data[0];
					$country = $data[1];
				}
			
			}else{
			    try {
				$amazon_resource = Mage::getResourceModel('amazonimport/amazonimport_orderDetails_Collection');
				$amazon_data = $amazon_resource->addAttributeToSelect('amazon_order_id')->addAttributeToSelect('amazon_country_id')
					->setOrderFilter($order->getId())->load();	
				
					foreach ($amazon_data as $amazon) {
							$amazonid = $amazon->getAmazonOrderId();
							$country = $amazon->getAmazonCountryId();
					}
                               } catch (Exception $e) { }
			}
			
			if(isset($amazonid)){
				
				
				if(Mage::getStoreConfig('amazonint/amazon'.$country.'/enabled_dispatch') == 1){
					// save the shipment to the database; we'll save any tracking data in a moment.
					$db = Mage::getSingleton("core/resource")->getConnection("core_write");
					$table_prefix = Mage::getConfig()->getTablePrefix();
					
					
					// do we have this already by any chance?
					$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream_shipping WHERE amazon_order_id='".$amazonid."'");
					if($result->rowCount() == 0){
					
								
						$result = $db->query("INSERT INTO {$table_prefix}amazonimport_surestream_shipping (`marketplace`,`amazon_order_id`) VALUES ('".$country."','".$amazonid."')");							
				
					}
				
				
				}
				
			}
		}
		
	}
	
	
	public function saveTrackingToAmazon(Varien_Event_Observer $observer)
	{
		$shiponcomplete = Mage::getStoreConfig('amazonint/general/shiponcomplete');
		
		
		if (!$shiponcomplete)
		{
			/*
			 * Send shipment to Amazon when tracking information is added.
			 */
			
			// only mark as shipped when order has tracking information..
			$track = $observer->getEvent()->getTrack();
			$order = $track->getShipment()->getOrder();
		
		
				if(version_compare(Mage::getVersion(), "1.4.1.0", ">=")){
				
					$data = Mage::getModel('amazonimport/amazonimport')->flatordersLookupHelper($order->getId());
					if(sizeof($data) > 0){
						$amazonid = $data[0];
						$country = $data[1];
					}
				
				}else{
		
					try {
						$amazon_resource = Mage::getResourceModel('amazonimport/amazonimport_orderDetails_Collection');
						$amazon_data = $amazon_resource->addAttributeToSelect('amazon_order_id')->addAttributeToSelect('amazon_country_id')
						->setOrderFilter($order->getId())->load();	
						
							foreach ($amazon_data as $amazon) {
										$amazonid = $amazon->getAmazonOrderId();
										$country = $amazon->getAmazonCountryId();
							}
					} catch (Exception $e) { }
				}
				
			if(isset($amazonid)){
				
				
				if(Mage::getStoreConfig('amazonint/amazon'.$country.'/enabled_dispatch') == 1){
					// save the shipment to the database; we'll save any tracking data in a moment.
					$db = Mage::getSingleton("core/resource")->getConnection("core_write");
					$table_prefix = Mage::getConfig()->getTablePrefix();
					
					if($track->getCarrierCode() == "custom"){
						$tcode = $track->getTitle();	
					}else{
						$tcode = $track->getCarrierCode();
					}
/*
					try {
						$db->query("ALTER TABLE {$table_prefix}amazonimport_surestream_shipping ADD COLUMN (shipping_method TEXT)");

					}
					catch (Exception $e) {}
*/				
					
					$db->query("REPLACE INTO 
					    {$table_prefix}amazonimport_surestream_shipping 
					    (`amazon_order_id`,`tracking_number`,`carrier_name`, 
					    `marketplace`, shipping_method) VALUES ('"
							.$amazonid."','".$track->getNumber()."','".
							$tcode."','".$country."',
							'".$track->getTitle()."')");		
				
					
				
				}
			}
				
		}
		
	}
		
}

?>