<?php

require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Client.php');
require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportRequest.php');
require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/RequestReportResponse.php');
require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListRequest.php');
require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequestListResponse.php');
require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportRequest.php');
require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/GetReportResponse.php');
require_once (Mage::getBaseDir().'/app/code/community/Camiloo/Amazonimport/Model/MarketplaceWebService/Model/IdList.php');


//This class contains all the callback methods that will actually
//handle the XML data.
class SaxClass {

	private $resultHit = false;
	private $currentElement = "";
	
	private $index = 0;
	
	private $lastSku;

	// Parameters
	public $subtype = "x";
	public $errorlogtable = "x";
	public $db; // our database connection
	
	public $debugMsg = "";

	// Parsed data
	private $messageid = 0;
	private $resultdesc = "";
	private $resultcode = "";
	public $successCount;

	//callback for the start of each element
	function startElement($parser_object, $elementname, $attribute) {
		

		if ($elementname == "Result") {

			$this->debugMsg .= "startElement: Result Hit\n";
			$this->resultHit = true;
			$this->resultdesc = '';
		}
		
		$this->currentElement = $elementname;
		$this->debugMsg .= "startElement: currentElement = $elementname\n";
	}

	//callback for the end of each element
	function endElement($parser_object, $elementname) {
				
		$this->index++;
		
		$this->debugMsg .= "endElement: elementname = $elementname\n";
		
		if ($elementname == "Result") {
			
			$this->debugMsg .= "endElement: RESULT\n";

			$this->resultHit = false;
			
			$this->debugMsg .= "Result code is ".$this->resultcode."\n";

			if ($this->resultcode == "Error" || $this->resultcode == "Warning")
			{
				
				$this->debugMsg .= "endElement: ERROR\n";
				
				//======================================
				// Analyse the error message description
				$table_prefix = Mage::getConfig()->getTablePrefix();
				$errorDescTemp = mysql_escape_string($this->resultdesc);
				/*
 				if (strpos($errorDescTemp, 'indicate that the item is a variation parent before setting up variation relationships') > 0) {
				
					// Relation feed referenced a product which isn't in seller central - make sure we try and add it
					$prodModel = Mage::getModel('catalog/product')->loadByAttribute('sku', $this->lastSku);
					if (is_object($prodModel)) {
						
						$sqlTemp = "insert into ".$this->errorlogtable." (productid, submission_type) VALUES (".($prodModel->getId()).", 'Product')";
						
							
						$this->db->query($sqlTemp);
						
					}
				}
				*/
				$sqlTemp = "update ".$this->errorlogtable." set result='Error', result_description='"
					.$errorDescTemp."', messageid=0 where messageid=".$this->messageid." and submission_type='"
					.$this->subtype."' and result = ''";
				
				$this->debugMsg .= $sqlTemp."\n\n";
				$this->db->query($sqlTemp);
				
			}
		}
	}

	//callback for the content within an element
	function contentHandler($parser_object,$data)
	{
		$this->debugMsg .= "contentHandler: ".$this->resultHit." $data\n";
		
		if($this->currentElement == "MessagesSuccessful") {
			if (strlen(trim($data)) > 0)
			{
				$this->successCount = trim($data);
			}
		}
		
		if ($this->resultHit) {
			switch ($this->currentElement) {
				case "MessageID":
					if (strlen(trim($data)) > 0)
					{
						$this->messageid = trim($data);
					}
					break;

				case "ResultDescription":
					if (strlen(trim($data)) > 0)
					{
						$this->resultdesc .= ' '.str_replace("the Amazon.com catalog",
														"your Amazon seller account",
														htmlspecialchars_decode(trim($data)));
														
					}
					break;

				case "ResultCode":
					if (strlen(trim($data)) > 0)
					{
						$this->resultcode = trim($data);
					}
					break;
					
				case "SKU":
				{
					if (strlen(trim($data)) > 0)
					{
						$this->lastSku = trim($data);
					//	mail('matt@camiloo.co.uk', 'Last SKU', $this->lastSku);
					}
					break;
				}
			}
		}
	}
}


class Camiloo_Amazonimport_Model_Cron extends Varien_Object
{
	private $feedStates = array("WaitingToSubmitProductFeed", "WaitingForProductFeedResult",
								"WaitingToSubmitImageFeed", "WaitingForImageFeedResult",
								"WaitingToSubmitRelationFeed", "WaitingForRelationFeedResult");
	/*

	WaitingToSubmitShippingOverrideFeed
	WaitingForShippingOverrideFeedResult

	*/

	public function fixEncoding($in_str)
	{
		 if (function_exists('mb_strlen')) {
		 	
		    $cur_encoding = mb_detect_encoding($in_str);
		   	if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8")){
		  		
		   	} else {
		  		$in_str = utf8_encode($in_str);
		   }
		 }
		
		return $in_str;
	}

	public function downloadActivelistings($country, $url, $mpid, $mid){
		$amazon = Mage::getModel('amazonimport/amazonlink');
			
		$lastid = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_activelistings_lastreportid_'.$country);
		$waitingfor = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_activelistings_waitingfor_'.$country);

		if(($lastid == "")&&($waitingfor == "")){
			// first run failsafe.
			$waitingfor = $amazon->createActiveListingsRequest($url,$mid,$mpid);
			Mage::getModel('amazonimport/amazonimport')->saveSessionValue($waitingfor, 'camiloo_amazon_activelistings_waitingfor_'.$country);
				
		}else{
				
			//- Check that we aren't awaiting a file.
			if($waitingfor == 0){
				$waitingfor = $amazon->createActiveListingsRequest($url,$mid,$mpid);
				Mage::getModel('amazonimport/amazonimport')->saveSessionValue($waitingfor, 'camiloo_amazon_activelistings_waitingfor_'.$country);
			}else{
				$status = $amazon->CheckOrdersReportStatus($url,$mid,$mpid,Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_activelistings_waitingfor_'.$country));

				if($status == "_SUBMITTED_"){
					// log: submitted.
				}else if($status == "_IN_PROGRESS_"){
					// log: processing is in progress
				}else if($status == "_CANCELLED_"){
					// log: request was cancelled
				}else if($status == "_DONE_NO_DATA_"){
					// log: file was empty.
					Mage::getModel('amazonimport/amazonimport')->saveSessionValue('0','camiloo_amazon_activelistings_waitingfor_'.$country);
				}else{
					$output = $amazon->downloadOrdersReport($url,$mid,$mpid,$status);
					$this->importActivelistings($output, $country, $mid);
					Mage::getModel('amazonimport/amazonimport')->saveSessionValue('0','camiloo_amazon_activelistings_waitingfor_'.$country);
				}
			}
		}
	}
	
	public function importActiveListings($output, $country, $mid){
		// Issue 12: Remove current 'is on amazon' setting and reapply
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$db->query("UPDATE {$table_prefix}amazonimport_listthis_{$country} set is_on_amazon=0");
		
		// active listings are both AutoMatched and 'Setup'.
		// the product SKU is the common denominator here.
		$products = array();
		$lines = explode("\n",$output);
		// explode lines into the columns
		$titles = explode("	",$lines[0]);

		foreach($lines as $key=>$value){
			if($key > 0){
				$temp = explode("	",$lines[$key]);
				foreach($temp as $key2=>$value2){
					if($key2 < 33){
						$title = rtrim($titles[$key2]);
						$products[$key][$title] = "$value2";
					}
				}
			}
		}

		foreach($products as $product){
			if(isset($product['asin'])){
				$sku = $product['sku']; // TODO use SKU mapping
				$asin = $product['asin'];
				$model = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
				
					
				if(is_object($model)){
					// continue with the import as the product exists in the catalogue.
					$productid = $model->getId();

					// first, we must check if there is a row for this product id - if so, delete it.
					$model = Mage::getModel('amazonimport/amazonimportlistthis'.$country)->getCollection()
					    ->addFieldToFilter('productid',array($productid));
					if(sizeof($model) > 0){
						foreach($model as $mdl){
							break;
						}
					}else{
						$mdl = Mage::getModel('amazonimport/amazonimportlistthis'.$country);
						$mdl->setData('productid',$productid);
					}
				
					$mdl->setData('is_on_amazon',1);
					if($country == "uk"){
						$tld = ".co.uk";
					}else if($country == "de"){
						$tld = ".de";
					}else if($country == "fr"){
						$tld = ".fr";
					}else if($country == "com"){
						$tld = ".com";
					}
						
						
					$mdl->setData('amazonlink','http://www.amazon'.$tld.'/dp/'.$asin.'?seller='.$mid);
					$mdl->save();
						
					$saving = Mage::getModel('amazonimportsetup'.$country.'/amazonimportsetup'.$country)->getCollection()->addFieldToFilter('productid',$productid);
					if(sizeof($saving) > 0){
						foreach($saving as $mdl){
							break;
						}
					}else{
						$mdl = Mage::getModel('amazonimportsetup'.$country.'/amazonimportsetup'.$country);
						$mdl->setData('productid',$productid);
					}
					$mdl->setData('initial_setup_complete',1);
				
					$mdl->setData('asincode',$asin);
					$mdl->save();
						

					// ============== ============== ==============
				}else{

					// log - the product wasn't found in the magento catalogue but exists on the amazon store.

				}
					
			}
		}


	}


	/**
	 * Imports a series of Amazon orders.
	 *  
	 * @param String $input 		The raw Amazon order report data 
	 * @param String $country 		The country of the Amazon store: uk, de, fr or com
	 * @param String $configtree 	The location of the corresponding Amazon country settings
	 */
	public function goimport($input, $country, $configtree){
		// Prevent duplicate orders
		
		$semEnabled = function_exists('sem_get');
		$semRes = null;
		
		if ($semEnabled) {
			$semRes = sem_get(19819);
			sem_acquire($semRes);
		}
		
		// =================================
		$array = $this->convertCsvToArray($input, $country, $configtree);
		if (sizeof($array) > 0)
		{
			$this->importArray($array);
		}
		
		// =================================
		if ($semEnabled) {
			sem_release($semRes);
		}
	}

	public function isOrderAlreadyImported($orderid)
	{
		if (version_compare(Mage::getVersion(), "1.4.1.0", ">="))
		{
			$amazon_data = Mage::getModel('amazonimport/amazonimport')->flatordersUniqueHelper($orderid);
		}
		else
		{
			$amazon_resource = Mage::getResourceModel('amazonimport/amazonimport_orderDetails_Collection');
			$amazon_data = $amazon_resource->addAttributeToSelect('amazon_order_id')->setAmazonOrderFilter($orderid)->load();
		}
		
		return sizeof($amazon_data) > 0;
	}
	
	public function convertCsvToArray($input, $country, $configtree)
	{
		// Get each line of the order report
		$lines = explode("\n", $input);
		// Get the headers
		$titles = explode("\t", array_shift($lines));

		// For each non-header line
		foreach ($lines as $key => $value)
		{
			// Explode row by tab
			$rowFieldList = explode("\t", $value);
			
			foreach ($rowFieldList as $key2 => $value2)
			{
				// If there is a corresponding title for this field value
				if (isset($titles[$key2]))
				{
					$title = trim($titles[$key2]);
					$orders[$key][$title] = $value2;
				}
			}
		}
		
		// $orders is an array at this point: 
		// [row][field] = fieldValue
		//
		// [0][order-id] = 203-7752884-9746724
		// [0][item-price] = 24.11
		// [1][order-id] = 443-7552884-9748824
		// [1][item-price] = 7.99
		//
		// etc.....
		//
		
		foreach ($orders as $line)
		{
			// Because of the way the Amazon API works, there is no need to filter the results; they only show up if they have been paid for
			$orderid = $line['order-id'];
			
			if (strlen($orderid) < 4)
			{
				// Bad order ID
				continue;
			}
			
			if ('' == $line['quantity-purchased'])
			{	// Bug fix 14-8-2010: May not have a full order row!
				continue;
			}
			
			// If the Amazon order referenced in the raw order report is not already imported
			if (!$this->isOrderAlreadyImported($orderid))
			{
				if (!isset($amazonorders[$orderid]))
				{
					if(isset($line['sales-channel'])) {
						if (strpos($line['sales-channel'], "Amazon Checkout") !== false) {
							continue; // ignore order
						}
					}

					$buyer_tmp = isset($line['buyer-name']) ? $line['buyer-name'] : "Unknown";
					$recipient_name = $buyer_tmp;
					
					if (isset($line['recipient-name']))
					{
						$recipient_name = $line['recipient-name'];
					}
					
					// bugfix 08/07/2009: Missing letters in amazon recipient names.
					$i = strripos($buyer_tmp, " ");
					$k = strripos($recipient_name, " ");

					if ($i === FALSE) {
					
						$amazonorders[$orderid]['customer']['firstName'] = $this->fixEncoding($buyer_tmp);
						$amazonorders[$orderid]['customer']['surname'] = $this->fixEncoding($buyer_tmp);
					}
					else {
					
						$amazonorders[$orderid]['customer']['firstName'] = $this->fixEncoding(substr($buyer_tmp, 0, $i));
						$amazonorders[$orderid]['customer']['surname'] = $this->fixEncoding(substr($buyer_tmp, $i+1));
					}

					$amazonorders[$orderid]['customer']['buyerphoneNumber'] = $line['buyer-phone-number'];
					$amazonorders[$orderid]['customer']['shipphoneNumber'] = $line['ship-phone-number'];
					$amazonorders[$orderid]['customer']['email'] = $line['buyer-email'];

					if ($k === FALSE) {
						$amazonorders[$orderid]['customer']['address']['firstshipName'] = $recipient_name;
						$amazonorders[$orderid]['customer']['address']['secondshipName'] = $recipient_name;
					}
					else {

						$amazonorders[$orderid]['customer']['address']['firstshipName'] = substr($recipient_name, 0, $k);
						$amazonorders[$orderid]['customer']['address']['secondshipName'] = substr($recipient_name, $k+1);
					}

					$amazonorders[$orderid]['customer']['address']['addressl1'] = $this->fixEncoding($line['ship-address-1']);
					if(($line['ship-address-2'] != "") && ($line['ship-address-3'] != "")){
						$amazonorders[$orderid]['customer']['address']['addressl2'] = $this->fixEncoding($line['ship-address-2']." - ".$line['ship-address-3']);
					}else if(($line['ship-address-2'] != "") && ($line['ship-address-3'] == "")){
						$amazonorders[$orderid]['customer']['address']['addressl2'] = $this->fixEncoding($line['ship-address-2']);
					}else if(($line['ship-address-2'] == "") && ($line['ship-address-3'] == "")){
						$amazonorders[$orderid]['customer']['address']['addressl2'] = $this->fixEncoding($line['ship-address-3']);
					}else{
						$amazonorders[$orderid]['customer']['address']['addressl2'] = NULL;
					}
					$amazonorders[$orderid]['customer']['address']['town'] = $this->fixEncoding($line['ship-city']);
					$amazonorders[$orderid]['customer']['address']['county'] = $this->fixEncoding($line['ship-state']);
					$amazonorders[$orderid]['customer']['address']['postcode']	= $this->fixEncoding($line['ship-postal-code']);
					$amazonorders[$orderid]['customer']['address']['country'] = $line['ship-country'];
					
					/*
					 * bill-address-1 bill-address-2 bill-address-3 bill-city bill-state bill-postal-code bill-country
					 */
					if(isset($line['bill-address-1'])) {
						$amazonorders[$orderid]['bill-address-1'] = $this->fixEncoding($line['bill-address-1']);
					}
					if(isset($line['bill-address-2'])) {
						$amazonorders[$orderid]['bill-address-2'] = $this->fixEncoding($line['bill-address-2']);
					}
					if(isset($line['bill-address-3'])) {
						$amazonorders[$orderid]['bill-address-3'] = $this->fixEncoding($line['bill-address-3']);
					}
					if(isset($line['bill-city'])) {
						$amazonorders[$orderid]['bill-city'] = $this->fixEncoding($line['bill-city']);
					}
					if(isset($line['bill-state'])) {
						$amazonorders[$orderid]['bill-state'] = $this->fixEncoding($line['bill-state']);
					}
					if(isset($line['bill-postal-code'])) {
						$amazonorders[$orderid]['bill-postal-code'] = $this->fixEncoding($line['bill-postal-code']);
					}
					if(isset($line['bill-country'])) {
						$amazonorders[$orderid]['bill-country'] = $this->fixEncoding($line['bill-country']);
					}
					if (isset($line['gift-message-text'])) {
						$amazonorders[$orderid]['gift-message-text'] = $this->fixEncoding($line['gift-message-text']);
					}
                    if (isset($line['gift-wrap-price']) && isset($line['gift-wrap-type'])) {
                        
                        // Add the gift wrap as a product =================================
                        // can't add gift wrap price to msg as risk that recipient might see it
                        
                        $giftWrapSku = "_AMAZON_GIFTWRAP_".$line['gift-wrap-type'];
                        
                        $amazonorders[$orderid]['orderItems'][] = array('sku' => $giftWrapSku,
                                'productTitle' => "Amazon Gift Wrap Type ".$line['gift-wrap-type'], 
                                'qty' => 1,
                                'priceEach' => $line['gift-wrap-price'],
                                'itemShippingCost' => '0.00',
                                'itemShippingInsuranceCost' => NULL,
                                'itemShippingService' => 'Standard',
                                'itemnumber' => '111111111');
                        $latest = sizeof($amazonorders[$orderid]['orderItems'])-1;
                        
                        
                        // ================================================================
					}
					
					// as we haven't processed this customer's orderlines yet, let's define some empty floats for later.
					$amazonorders[$orderid]['orderSubtotalIncVat'] = 0.0;

					// define the shipping service for the order... hopefully this will be the same for each item as I think it has to be?
						
					// Add Amazon to shipping service to better differentiate from own website shipping methods.
					$amazonorders[$orderid]['shippingService'] = 'Amazon '.$line['ship-service-level'];
						
					// and finally let's define the tax rate as this will be hopefully stable throughout the order [fair assumption]
					$amazonorders[$orderid]['dates']['orderDate'] = $line['purchase-date'];
					$amazonorders[$orderid]['dates']['paidDate'] = $line['payments-date'];
					$amazonorders[$orderid]['dates']['dispatchDate'] = NULL;
					$amazonorders[$orderid]['amazon_country'] = $country;
					$amazonorders[$orderid]['config_tree'] = $configtree;
				}
				
				$amazonorders[$orderid]['orderItems'][] = array('sku'=>$line['sku'],
					'productTitle'=>$line['product-name'],'qty'=>$line['quantity-purchased'],
					'priceEach'=>($line['item-price'] / $line['quantity-purchased']),
					'itemShippingCost'=>$line['shipping-price'],'itemShippingInsuranceCost'=>NULL,
					'itemShippingService'=>$line['ship-service-level'],'itemnumber'=>$line['order-item-id']);
				$latest = sizeof($amazonorders[$orderid]['orderItems'])-1;
                
				if(!isset($amazonorders[$orderid]['shippingCost'])){
					$amazonorders[$orderid]['shippingCost'] = 0;
				}
				// now let's update the totals for this order
				$amazonorders[$orderid]['shippingCost'] = $amazonorders[$orderid]['shippingCost'] 
					+ $amazonorders[$orderid]['orderItems'][$latest]['itemShippingCost'];
			}
			
		}
		
		if(isset($amazonorders))
		{			
			return $amazonorders;
		}
		else
		{
			return null;
		}
	}
	
	private function setOrderNameAndAddress($array, $quote) {
		// Quote should be assigned to a store to ensure correct order ID incrementation
		$code = Mage::getStoreConfig('amazonint/'.$array['config_tree'].'/store');
		$quote->setStoreId($code);
		$quote->reserveOrderId();
		
		$shipping_address = Mage::getModel('sales/quote_address');
		$shipping_address->setFirstname($this->fixEncoding($array['customer']['firstName']));
		$shipping_address->setLastname($this->fixEncoding($array['customer']['surname']));

		$shipping_address->setStreet(trim( $this->fixEncoding($array['customer']['address']['addressl1']
			."\n".$array['customer']['address']['addressl2']) ));

		if($array['amazon_country'] == "de"
			&& isset($array['customer']['address']['addressl2']) 
			&& strlen($array['customer']['address']['addressl2']) > 0) {
			
			$shipping_address->setCompany($this->fixEncoding($array['customer']['address']['addressl1']));
			$shipping_address->setStreet($this->fixEncoding($array['customer']['address']['addressl2']));
		}
		$shipping_address->setCity($this->fixEncoding($array['customer']['address']['town']));
		$shipping_address->setRegion($this->fixEncoding($array['customer']['address']['county']));
		$shipping_address->setPostcode($array['customer']['address']['postcode']);
		$shipping_address->setCountryId($array['customer']['address']['country']);
		$shipping_address->setEmail($array['customer']['email']);
		$shipping_address->setTelephone($array['customer']['buyerphoneNumber']);
		$billing_address = clone $shipping_address;

		$shipping_address->setFirstname($this->fixEncoding($array['customer']['address']['firstshipName']));
		$shipping_address->setLastname($this->fixEncoding($array['customer']['address']['secondshipName']));
		$shipping_address->setTelephone($array['customer']['shipphoneNumber']);
		
		/*
		 * bill-address-1 bill-address-2 bill-address-3 bill-city bill-state bill-postal-code bill-country
		 */
		
		$billAddress1 = isset($array['bill-address-1']) ? $array['bill-address-1']: "";
		$billAddress2 = isset($array['bill-address-2']) ? $array['bill-address-2']: "";
		$billAddress3 = isset($array['bill-address-3']) ? $array['bill-address-3']: "";
		$billCity = 	isset($array['bill-city']) ? $array['bill-city']: "";
		$billState = 	isset($array['bill-state']) ? $array['bill-state']: "";
		$billPostalCode = isset($array['bill-postal-code']) ? $array['bill-postal-code']: "";
		$billCountry = 	isset($array['bill-country']) ? $array['bill-country']: "";
		
		if ($billAddress1 != '') {
			$billing_address->setStreet($billAddress1." ".$billAddress2." ".$billAddress3);
		}
		if ($billCity != '') {
			$billing_address->setCity($billCity);
		}
		if ($billState != '') {
			$billing_address->setRegion($billState);
		}
		if ($billPostalCode != '') {
			$billing_address->setPostcode($billPostalCode);
		}
		if ($billCountry != '') {
			$billing_address->setCountryId($billCountry);
		}

		$quote->setCustomerFirstname($this->fixEncoding($array['customer']['firstName']));
		$quote->setCustomerLastname($this->fixEncoding($array['customer']['surname']));
		$quote->setCustomerEmail($array['customer']['email']);

		$quote->setCustomerIsGuest(1);
		$quote->setShippingAddress($shipping_address);
		$quote->setBillingAddress($billing_address);
	}
	
	private $amzTestOrder = array (
		  'customer' => 
		  array (
		    'firstName' => 'TEST XüXXXX',
		    'surname' => 'TEST XüXXXX',
		    'buyerphoneNumber' => '01122 112233',
		    'shipphoneNumber' => '01122 112233',
		    'email' => 'qiqiqiqiq2882828@marketplace.amazon.co.uk',
		    'address' => 
		    array (
		      'firstshipName' => 'JüXXXXX',
		      'secondshipName' => 'BüXXXX',
		      'addressl1' => 'TEST BüXXXX',
		      'addressl2' => 'TEST BüXXXX',
		      'town' => 'TEST BüXXXX',
		      'county' => 'TEST BüXXXXXXå',
		      'postcode' => 'LA1 1AA',
		      'country' => 'GB',
		    ),
		  ),
		  'orderSubtotalIncVat' => 0,
		  'shippingService' => 'Amazon Standard',
		  'dates' => 
		  array (
		    'orderDate' => '2010-02-02T12:00:00+00:00',
		    'paidDate' => '2010-02-02T12:00:00+00:00',
		    'dispatchDate' => NULL,
		  ),
		'gift-message-text' => 'hello world',
           'gift-wrap-price' => 5.99,
           'gift-wrap-type' => 'RED',
		  'amazon_country' => 'uk',
		  'config_tree' => 'amazonuk',
		  'orderItems' => 
		  array (
		    0 => 
		    array (
		      'sku' => '_AMAZON_GIFTWRAP_RED',
		      'productTitle' => 'Amazon gift wrap red',
		      'qty' => '1',
		      'priceEach' => 5.99,
		      'itemShippingCost' => '0.00',
		      'itemShippingInsuranceCost' => NULL,
		      'itemShippingService' => 'Standard',
		      'itemnumber' => '1111111111',
		    ),
                 1 => 
                 array (
                        'sku' => 'n2610',
                        'productTitle' => '2m TEST TEST',
                        'qty' => '1',
                        'priceEach' => 14.99,
                        'itemShippingCost' => '0.00',
                        'itemShippingInsuranceCost' => NULL,
                        'itemShippingService' => 'Standard',
                        'itemnumber' => '65165146514621651',
                        )
		  ),
		  'shippingCost' => 2.50
		);

	/**
	 * This function will take given input and create a fully valid magento order.
	 */
	public function importArray($input, $isTestMode = false)
	{
		if ($isTestMode)
		{
			echo "***** IN TEST MODE\n";
			
			// Adding a test order - use the above array.
			$input = array();
			$randomise = rand('100','999')."-".rand('1000000','9999999')."-".rand('1000','9999');
			$input[$randomise] = $this->amzTestOrder;
			
			print_r($input);
		}
		
		foreach ($input as $keyorder=>$array)
		{
			unset($order);
			
			if(version_compare(Mage::getVersion(), "1.4.1.0", ">="))
			{
				$amazon_data = Mage::getModel('amazonimport/amazonimport')->flatordersUniqueHelper($keyorder);
				if(sizeof($amazon_data) > 0)
				{
					if ($isTestMode) { echo "This order already exists - don't import again\n"; }
					// This order already exists - don't import again
					continue;
				}else{
					Mage::getModel('amazonimport/amazonimport')->flatordersDuplicationLock($keyorder,$array['amazon_country']);	
				}

			}
			
			$quote = Mage::getModel('sales/quote');
			$this->setOrderNameAndAddress($array, $quote);
			
			$quote->collectTotals()->save();
			
			// May need to reconciliate the tax totals for each line in the order
			// as Magento can get it a few pennies out
			$lineTaxTotalsForReconcil = array();

			//
			// Add all items to the Magento order
			//
			foreach ($array['orderItems'] as $key2 => $value2)
			{
				$catalog = Mage::getModel('catalog/product');
				
				// TODO: Check what the SKU is mapped to
				$db = Mage::getSingleton("core/resource")->getConnection("core_write");
				$table_prefix = Mage::getConfig()->getTablePrefix();
				$product = Mage::getModel('amazonimport/amazonimport')->getSkuAccordingToOrderImport($value2['sku'], $db, $table_prefix, $array['amazon_country']);
				
				if ($product == false) {
                    
					// v2.2 New Feature: Creating Stub Products when not on order
					$cmc = $array['amazon_country'];
					if ($cmc == "uk") { $store = Mage::getStoreConfig('amazonint/amazonuk/store'); }
					if ($cmc == "fr") { $store = Mage::getStoreConfig('amazonint/amazonfr/store'); }
					if ($cmc == "de") { $store = Mage::getStoreConfig('amazonint/amazonde/store'); }
					if ($cmc == "com"){ $store = Mage::getStoreConfig('amazonint/amazoncom/store');}
                    
					//$product = Mage::getModel('catalog/product');
					$product = new Mage_Catalog_Model_Product();
                    // Let's search eav_entity_type for entity_type_code=catalog_product and get entity_type_id
					// then search  eav_attribute_set where entity_type_id = the above result, order by attribute_set_id
					// attribute_set_id 
					
					$db = Mage::getSingleton("core/resource")->getConnection("core_write");
					$table_prefix = Mage::getConfig()->getTablePrefix();
					$sql = "SELECT entity_type_id FROM  {$table_prefix}eav_entity_type WHERE entity_type_code='catalog_product'";
					$result = $db->query($sql);
					$row = $result->fetch();
					
					$sql = "SELECT attribute_set_id FROM {$table_prefix}eav_attribute_set WHERE entity_type_id='".$row['entity_type_id']."' ORDER BY attribute_set_id ASC";
					$result = $db->query($sql);
					$row = $result->fetch();	
					$attributeSetId = $row['attribute_set_id'];
			
					// Build the product
					$product->setSku($value2['sku']);
					$product->setAttributeSetId($attributeSetId);
					$product->setTypeId('simple');
					$product->setName($value2['productTitle']);
					
					$websiteID = Mage::getModel('core/store')->load($store)->getWebsiteId();
					
					$product->setWebsiteIDs(array($websiteID)); # derive website ID from store.
					$product->setDescription('Product missing from Amazon order ID '.$keyorder);
					$product->setShortDescription('Product missing from Amazon order ID '.$keyorder);
					$product->setPrice($value2['priceEach']); # Set some price    
					
					//Default Magento attribute
					$product->setWeight('0.01');
					 
					$product->setVisibility(1); // not visible
					$product->setStatus(0);	// not available, thanks.
					$product->setTaxClassId(0); # My default tax class
					$product->setStockData(array(
						'is_in_stock' => 1,
						'qty' => $value2['qty']
					));
					 
					$product->setCreatedAt(strtotime('now'));
					 
					try {
						$product->save();
					}
					catch (Exception $ex) {  
						$product = "";
						
						//not sure why, but something unexpected happened here.
						// log - product ID was not found.	
						Mage::getModel('amazonimport/amazonimport')->limitLoggingHelper();
						$model = Mage::getModel('amazonimport/amazonimportlog');
						$model->setOutgoing("Importing orders...");
						$model->setIncoming("Amazon Order ID ".$keyorder);
						$model->setError("SKU ".$value2['sku']." was ordered but could not be found in your Magento catalog.");
						$model->setMessageTime(date("c"));
						$model->save();
					}
					 
					
					
				}
				
				if (is_object($product) && $product->getId())
				{
	
					$stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
					$stock->setData('qty', $stock->getData('qty') - $value2['qty']);
					$stock->save();
									
					// Bugfix 02/12/2009	-	If quantity is zero or lower and 'allow qty below zero' is set, orders would not import.
					$item = Mage::getModel('sales/quote_item');
					$product = Mage::getModel('catalog/product')->load($product->getId());
					$item->setQuote($quote)->setProduct($product)->setData('qty', $value2['qty']);
					
					$quote->addItem($item);
					
				} else {
					// log - product ID was not found.
						
						// log - product ID was not found.	
						Mage::getModel('amazonimport/amazonimport')->limitLoggingHelper();
						$model = Mage::getModel('amazonimport/amazonimportlog');
						$model->setOutgoing("Importing orders...");
						$model->setIncoming("Amazon Order ID ".$keyorder);
						$model->setError("SKU ".$value2['sku']." was ordered but could not be found in your Magento catalog.");
						$model->setMessageTime(date("c"));
						$model->save();
						
				}
			}
			$quote->collectTotals();
			
			$totaldiscount = 0;
			$count = 0;
			$subtotal = 0;
			$subtotalExShipping = 0;
			$runningTaxAmount = 0;
			
			$qitemsloop = $quote->getShippingAddress()->getAllItems();
			if(version_compare(Mage::getVersion(), "1.4.2.0", ">="))
			{
				$qitemsloop = $quote->getAllItems();
			}
			
			//
			// For each item we are adding to the Magento order
			//
			foreach ($qitemsloop as $key=>$item)
			{
				// PriceEach is from Amazon is always including TAX
				$priceEachAmzIncTax = $array['orderItems'][$count]['priceEach'];
				$qty 	   = $array['orderItems'][$count]['qty'];
				$taxPercent = 0;
				
				if (is_object($item))		// This block commented out for MPS
				{
					$taxPercent = $item->getData('tax_percent');
					if (0 == $taxPercent)
					{
						$custTaxClassId = $quote->getCustomerTaxClassId();
						
						$taxCalculationModel = Mage::getSingleton('tax/calculation');
						
						$request = $taxCalculationModel->getRateRequest($quote->getShippingAddress(), $quote->getBillingAddress(), 
							$custTaxClassId, $quote->getStore());
						
						$taxPercent = $taxCalculationModel->getRate($request->setProductClassId($item->getData('tax_class_id')));
						
					}
				}
				
				// Get the TAX only part for all Qty
				$taxAmtEach = $priceEachAmzIncTax - $priceEachAmzIncTax / (1 + ($taxPercent / 100));
				$taxAmtEach = round($taxAmtEach, 2); // Truncate Tax to 2dp
				$itemExTaxEach = $priceEachAmzIncTax - $taxAmtEach;
				
				$runningTaxAmount += ($taxAmtEach * $qty); // Update tax amount for whole order
				
				$item->setOriginalPrice($itemExTaxEach);
				$item->setBaseOriginalPrice($itemExTaxEach);
				$item->setCustomPrice($itemExTaxEach);
				$item->setBaseCustomPrice($itemExTaxEach);
				$item->setCalculationPrice($itemExTaxEach);
				$item->setBaseCalculationPrice($itemExTaxEach);
				$item->setPriceInclTax($priceEachAmzIncTax);
				$item->setBasePriceInclTax($priceEachAmzIncTax);
				
				$lineTaxTotalsForReconcil[] = ($taxAmtEach*$qty);
				
				$item->setTaxAmount($taxAmtEach*$qty);		// This line commented out for MPS
				$item->setBaseTaxAmount($taxAmtEach*$qty);	// This line commented out for MPS
				$item->setTaxPercent($taxPercent);
				
				$item->calcRowTotal();
				
				$item->setRowTotal($itemExTaxEach*$qty);
				$subtotal += $priceEachAmzIncTax*$qty; // getRowTotal returns an Ex VAT value
				
				$count++;
			}

			$quote->collectTotals();
			$quote->save();
			$quotePaymentObj = $quote->getPayment();
			
			// need marketplace key...
			$mkt = $array['amazon_country'];
			$quotePaymentObj->setMethod('amzpaymentmethod'.$mkt);
			$quotePaymentObj->setTransactionId($keyorder);

			$quote->setPayment($quotePaymentObj);

			$convertquote = Mage::getModel('sales/convert_quote');
			$order = $convertquote->addressToOrder($quote->getShippingAddress());
			$orderPaymentObj = $convertquote->paymentToOrderPayment($quotePaymentObj);
			
			$order->setBillingAddress($convertquote->addressToOrderAddress($quote->getBillingAddress()));
			$order->setPayment($convertquote->paymentToOrderPayment($quote->getPayment()));
			$order->setShippingAddress($convertquote->addressToOrderAddress($quote->getShippingAddress()));
			
			$qitemsloop = $quote->getShippingAddress()->getAllItems();
			if(version_compare(Mage::getVersion(), "1.4.2.0", ">="))
			{
				$qitemsloop = $quote->getAllItems();
			}

			foreach ($qitemsloop as $item)
	 	    {
			
				$orderItem = $convertquote->itemToOrderItem($item);
				if ($item->getParentItem()) {
				 $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
				}
			
				if (Mage::getStoreConfig('amazonint/general/import_time_as_order_time') == 1)
				{
				 $array['dates']['orderDate'] = substr($array['dates']['orderDate'],0,19);
				 $orderItem->setCreatedAt($array['dates']['orderDate']);  // Remove any timezone offset.
				}
			
				$order->addItem($orderItem);
			
			   }

			$order->setCanShipPartiallyItem(false);
			
			// Set shipping method
			$order->setData('payment_type', 'Amazon');
			$order->setShippingMethod("amazonimport_shippingmethod");
			$order->setShippingDescription($array['shippingService']);

			///////////////////////////////// Tax Calculation Fix relating to shipping
			
			// Get the shipping cost from Amazon which is inc TAX
			$shippingCostAmzIncTax = $array['shippingCost'];
			
			// Get the tax rate for the shipping
			$shippingTaxRate = 0.0;

			$store = $quote->getStore();
			$shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);
			if ($shippingTaxClass)
			{
				$custTaxClassId = $quote->getCustomerTaxClassId();
				
				$taxCalculationModel = Mage::getSingleton('tax/calculation');
				
				$request = $taxCalculationModel->getRateRequest($quote->getShippingAddress(), $quote->getBillingAddress(), $custTaxClassId, $store);
				
				$shippingTaxRate = $taxCalculationModel->getRate($request->setProductClassId($shippingTaxClass));
			}
			
			$subtotalExShipping = $subtotal;
			$subtotal += $shippingCostAmzIncTax;
			
			unset($shippingTax);
			
			if ($shippingTaxRate > 0)
			{
				$shippingTax = $shippingCostAmzIncTax - (100 / (100 + $shippingTaxRate) * $shippingCostAmzIncTax);
				$shippingTax = round($shippingTax, 2);
				$shippingCostExTax = $shippingCostAmzIncTax - $shippingTax;
				
				$order->setShippingTaxAmount($shippingTax);
				$order->setBaseShippingTaxAmount($shippingTax);
				
				$order->setShippingAmount($shippingCostExTax);
				$order->setBaseShippingAmount($shippingCostExTax);
				
				$order->setShippingInclTax($shippingCostAmzIncTax);
    			        $order->setBaseShippingInclTax($shippingCostAmzIncTax);
				
				$order->setTaxAmount($runningTaxAmount + $shippingTax);
				$order->setBaseTaxAmount($runningTaxAmount + $shippingTax);
			}
			else
			{
				$order->setShippingAmount($shippingCostAmzIncTax);
				$order->setBaseShippingAmount($shippingCostAmzIncTax);
				$order->setShippingInclTax($shippingCostAmzIncTax);
                                // Bugfix for issue PUF-999-35896 - MN: Base Tax Amount not set
                                // when shipping is not taxed.
				$order->setBaseTaxAmount($runningTaxAmount);
                       $order->setBaseShippingInclTax($shippingCostAmzIncTax);
			}
			
			///////////////////////////////// Tax Calculation Fix relating to shipping
			
			if (!isset($taxPercent)) {
				$taxPercent = 0.0;
			}
			
			$order->setGrandTotal($subtotal);
			$order->setBaseGrandTotal($subtotal);
			$order->setTotalRefunded(0);
			$order->setTotalDue(0);
			
			$order->getPayment()->setTransactionId($keyorder);
			$order->getPayment()->setLastTransId($keyorder);
			
			
			if (version_compare(Mage::getVersion(), "1.4", ">="))
			{
				// bugfix 05/04/10 - Magento v1.4 compatibility fix.
				$order->setSubtotal($subtotalExShipping);
				$order->setBaseSubtotal($subtotalExShipping);
			}
			
			try {
				$order->place();
			}
			catch(Exception $x) {
				#exception silencer
			}
			$order->save();
			
			
			// Mark order as imported now, incase of an error later - we won't get duplicate orders
			if(version_compare(Mage::getVersion(), "1.4.1.0", ">=")){
				// fix for v1.4.1.0 upwards while flat orders are not playing ball.
				Mage::getModel('amazonimport/amazonimport')->flatordersInsertHelper($order->getId(),$keyorder,$array['amazon_country']);		
			}
			else
			{
				$new_order_amazon = Mage::getModel('amazonimport/amazonimport_OrderDetails');
				$new_order_amazon->setParentId($order->getId())
					->setAmazonOrderId($keyorder)
					->setAmazonCountryId($array['amazon_country'])
					->save();
			}
			
			$code = Mage::getStoreConfig('amazonint/'.$array['config_tree'].'/store');
			$order->setStoreId($code);
			$order->setBaseGrandTotal($order->getGrandTotal());
			
			// Fix row totals
			if (Mage::getStoreConfig('tax/calculation/price_includes_tax') == 0)
			{
				$items = $order->getAllItems();
				$i = 0;
				
				foreach ($items as $itemId => $item)
				{
					 $item->setTaxAmount($lineTaxTotalsForReconcil[$i]);
					 $i++;
				}
			}
			
			$_dataTemp = $order->getData();
			$_dataTemp['tax_amount'] = $runningTaxAmount;
			$order->setData($_dataTemp);
			$order->save();
			
			$order->setSubtotal($subtotalExShipping - $runningTaxAmount);
			$order->setBaseSubtotal($subtotalExShipping - $runningTaxAmount);
			
			if (isset($shippingTax))
			{
				$order->setTaxAmount($runningTaxAmount + $shippingTax);
				$order->setBaseTaxAmount($runningTaxAmount + $shippingTax);
			}
			
			$order->save();
			
			try {
				# New Feature Request ID OXK-657-45368 - implement transaction on payment method.
				if(is_object(Mage::getModel('sales/order_payment_transaction'))){
					try {
					    $order->getPayment()->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
					}catch(Exception $e){
						# new feature failsafe.	
					}
				}
			}catch(Exception $e){
				# new feature failsafe.	
			}
			$invoice = $order->prepareInvoice();
			
			if (version_compare(Mage::getVersion(), "1.4", ">="))
			{
				//bugfix 05/08/2010 - subtotal incl tax not set on order printouts. -- MN
			   $invoice->setSubtotalInclTax($order->getSubtotal() + $runningTaxAmount);
			   $invoice->setBaseSubtotalInclTax($order->getBaseSubtotal() + $runningTaxAmount);
			}
			
			
			$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
			// Making sure other modules do not cause our module to crash
			if (in_array("Netz98_Picklist", $modules))
			{
				$invoice->save();
			}
			else
			{
				error_reporting(E_ALL);
				ini_set("display_errors","on");
				$invoice->register();
			}
			
			Mage::getModel('core/resource_transaction')
				->addObject($invoice)
				->addObject($invoice->getOrder())
				->save();
				
			Mage::dispatchEvent('sales_order_invoice_pay', array('invoice' => $invoice));

			if (Mage::getStoreConfig('amazonint/general/import_time_as_order_time') == 1)
			{
				$array['dates']['orderDate'] = substr($array['dates']['orderDate'],0,19);
				$order->setCreatedAt($array['dates']['orderDate']); 	// Remove any timezone offset.
			}

			if (isset($array['gift-message-text'])) {

				$message = Mage::getModel('giftmessage/message');

              	// $gift_sender = $message->getData('sender');
                // $gift_recipient = $message->getData('recipient');

                $message->setMessage($array['gift-message-text']);
				$message->save();

                $gift_message_id = $message->getId();

                $order->setData('gift_message_id', $gift_message_id);
			}

			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
			$order->save();
			
			
			Mage::getModel('amazonimport/amazonimport')->limitLoggingHelper();
			$model = Mage::getModel('amazonimport/amazonimportlog');
			$model->setOutgoing("Import Amazon Order ID ".$keyorder);
			$model->setIncoming("Magento order ID ".$order->getIncrementId()." created successfully");
			$model->setError("No error.");
			$model->setMessageTime(date("c"));
			$model->save();
			//
			// Some Amazon orders import as “Pending” status instead of “Processing”
			// EWI-162-53926
			//
			try {

			    // Sometimes orders are not set to processing status - try this call:
		    	    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, 'processing', 'Processing', false);

			} catch (Exception $x1) { }

			$order->save();
		}
	}

	// New in v2.08 -------------------------------------
	public function updateErrorLog($elog_id, $count, $currentMarketplaceCode)
	{
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$table_errorlog = $table_prefix."amazonimport_errorlog_".$currentMarketplaceCode;

		$sql = "update $table_errorlog set messageid = $count, result = '' where elog_id=$elog_id";

		$db->query($sql);
	}

	public function doSubmitFeed($table_errorlog, $subtype, $db, $maxFileSize,
		$currentMarketplaceCode, $store, $marketplaceId, $merchantId,
		$endpointUrlMws)
	{
		$amazonlink = Mage::getModel('amazonimport/amazonlink');
		$table_prefix = Mage::getConfig()->getTablePrefix();
		
		$sql = "select * from $table_errorlog where submission_type = '$subtype' 
		    and messageid = 0 and result = ''";

		$count = 1;
		$xmlFileName = $this->getTempFolder()."".time().".xml";

		// Create an XML file for appending to
		
		$fileHandle = fopen($xmlFileName, "w+");
		$currentXmlSize = 0;
		$starttime = $this->microtime_float();
		$endtime = $this->microtime_float();
		$runtime = round($endtime - $starttime);
		
		$maxruntime = 30;
			
		$result = $db->query($sql);
		
		$db->query("DROP FUNCTION IF EXISTS GetAmzCatmapIdCron; CREATE FUNCTION GetAmzCatmapIdCron(paramProductid INTEGER) RETURNS INTEGER DETERMINISTIC
					 BEGIN
					 
					 DECLARE  current_cat_id INT;
					 DECLARE  catmapid INT;
					 DECLARE  current_count_two INT;
					 DECLARE  current_count_three INT;
					 DECLARE  no_more_products INT;
					 DECLARE  match_found INT;
					 
					 DECLARE  cur_product CURSOR FOR 
					 SELECT category_id FROM {$table_prefix}catalog_category_product
											INNER JOIN {$table_prefix}catalog_category_entity ON entity_id=category_id
											WHERE product_id = paramProductid ORDER BY {$table_prefix}catalog_category_entity.level DESC;
					
					 DECLARE  CONTINUE HANDLER FOR NOT FOUND 
				     	SET no_more_products = 1;
				
					 SET match_found = 0;
					 OPEN  cur_product;
					 
					 FETCH  cur_product INTO current_cat_id;
					 
					 mainloop: REPEAT 
					 	SELECT category_id INTO catmapid FROM {$table_prefix}amazonimport_categorymapping
						WHERE country_id='$currentMarketplaceCode' AND category_id = current_cat_id; 

						SET no_more_products = 0;
					 	
						IF  catmapid > 0 AND match_found < 1 THEN
							SET match_found = catmapid;
							SELECT count(*) INTO current_count_two FROM {$table_prefix}amazonimport_categorise_$currentMarketplaceCode WHERE productid = paramProductid;
							IF  current_count_two > 0 THEN
								SET match_found = 0;
								LEAVE mainloop;
							END IF;
						END IF;
	
						FETCH  cur_product INTO current_cat_id;
					  	UNTIL  no_more_products = 1
					 END REPEAT;
					 
					 CLOSE  cur_product;
					 RETURN match_found;
					 
END;");
		
		
		$beforeMemory = memory_get_peak_usage();
		$maxMemory = ini_get('memory_limit');
		
        $maxMemoryChar = substr($maxMemory,strlen($maxMemory)-1,1);
        if($maxMemoryChar == "M"){
              $maxMemory = str_replace("M","",$maxMemory);
        	  $maxMemory = $maxMemory * 1024 * 1024;
        }else if($maxMemoryChar == "G"){
              $maxMemory = str_replace("G","",$maxMemory);
              $maxMemory = $maxMemory * 1024 * 1024 * 1024;
        }   
        
		if($maxMemory < 100){
			$maxMemory = 10000000000;
		}
		$changeMemory = 0;
		
		//TEMP
		$maxFileSize = 10 * 1024 * 1024; // 10MB
		
		while ($row = $result->fetch()) 
		{
			if ($currentXmlSize < $maxFileSize && $runtime < $maxruntime 
				&& (memory_get_peak_usage() + $changeMemory) < $maxMemory) 
			{
					
			    $_productid = $row['productid'];
			    
			    if ($_productid <= 0) {
			    	
			    	continue;
			    }
				
				$msgFragment = $amazonlink->getMessageFragment($row, $currentMarketplaceCode, $store, $count, $subtype);

				if ($msgFragment != "") 
				{
					fwrite($fileHandle, $msgFragment);
					
					$currentXmlSize = filesize($xmlFileName);
					
					$this->updateErrorLog($row['elog_id'], $count, $currentMarketplaceCode);
					
					$count++;
					$endtime = $this->microtime_float();
					$runtime = round($endtime - $starttime);
				}
				else {
					
					$currentXmlSize += 128;
				}
			}
			else
			{
				break;
			}
			
			$tmpval = memory_get_peak_usage() - $beforeMemory;
			if($tmpval > $changeMemory){
				$changeMemory = $tmpval;
			}
			
		}
		
		fclose($fileHandle);

		$fileLimitReached = $currentXmlSize >= $maxFileSize;
		if($fileLimitReached == 0)
		{
			$fileLimitReached = $runtime >= $maxruntime;
		}
		
		$canShortcut = $count == 1;
	

		if ($canShortcut != true)
		{
			// Need to submit to amazon now
			$submissionId = $amazonlink->submitFeed($xmlFileName, $subtype, $currentMarketplaceCode, $marketplaceId, $merchantId, $endpointUrlMws);

		}else{
			$submissionId = 0;
		}

		// Need to return the above 3 variables
		$returnArray = array('SubmissionId' => $submissionId,
							 'FileLimitReached' => $fileLimitReached,
							 'CanShortcut' => $canShortcut);
		return $returnArray;
	}


	public function microtime_float(){
		list ($msec, $sec) = explode(' ', microtime());
		$microtime = (float)$msec + (float)$sec;
		return $microtime;
	}


	public function doProcessFeedResults($previousSubmissionId, $currentMarketplaceCode, $marketplaceId, $merchantId,
	$endpointUrlMws, $subtype, $table_errorlog, $db) {
		$amazonlink = Mage::getModel('amazonimport/amazonlink');


		$resultFileName = $amazonlink->getFeedResult($previousSubmissionId, $currentMarketplaceCode, $marketplaceId, $merchantId,
		$endpointUrlMws);


		// If feed is not ready script will die.

		// Feed must be ready here.....
		if($resultFileName == "NOTREADY"){
				
			return false;
				
		}else{

			$SaxObject = new SaxClass();

			$SaxObject->subtype = $subtype;
			$SaxObject->errorlogtable = $table_errorlog;
			$SaxObject->db = $db;

			$parser_object = xml_parser_create();
			xml_set_object($parser_object, $SaxObject);

			// Don't alter the case of the data
			xml_parser_set_option($parser_object, XML_OPTION_CASE_FOLDING, false);

			xml_set_element_handler($parser_object, "startElement", "endElement");
			xml_set_character_data_handler($parser_object, "contentHandler");

			$fp = fopen($resultFileName, "r");

			//loop through data
			while ($data = fread($fp, 4096)) {
				//parse the fragment
				xml_parse($parser_object, $data, feof($fp));
			}
			
			// Finished parsing
			xml_parser_free($parser_object);

			// $SaxObject will have done its work by now (i.e. error log DB table updated)
			if($subtype == "Product") {
				$successCount = $SaxObject->successCount;
				$index = 0;
				
				$sql = "select * from $table_errorlog where result = '' and messageid > 0 and submission_type = '".$subtype."'";
				$result = $db->query($sql);
				foreach ($result as $r) {
					
					if($index >= $successCount) {
						break;	// We can't mark any more products as successful as we've been through all the success messages
						// Should not get here
					}
					
					$productIdSuccess = $r['productid'];
					
					// Update the product ID as 'Is On Amazon'=Yes
					$table_prefix = Mage::getConfig()->getTablePrefix();
					$db->query("update {$table_prefix}amazonimport_listthis_$currentMarketplaceCode set is_on_amazon=1 where productid=".$productIdSuccess);
					
					$index++;
				}
			}
			
			// Remove successful rows from the error_log
			$sql = "delete from $table_errorlog where result = '' and messageid > 0 and submission_type = '".$subtype."'";
			$db->query($sql);
			
			return true;
		}
	}

	public function runSurestreamForCountry($currentMarketplaceCode, $marketplaceId, $merchantId,
			$endpointUrlMws, $store, $staterow) {

		$configtree = "amazon".$currentMarketplaceCode;
		$currentState = $staterow['state'];
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$table_errorlog = $table_prefix."amazonimport_errorlog_".$currentMarketplaceCode;
		$maxFileSize = 1024*1024*9.5;// 9.5MB in bytes
		$fileLimitReached = false;
		$canShortcut = false;
		$previousSubmissionId = $staterow['submission_id'];
		$submissionId = '';

		// ======================


		switch ($currentState) {
			case "WaitingToSubmitProductFeed":
					
				if(Mage::getStoreConfig('amazonint/amazon'.$staterow['marketplace'].'/enabled_upload') == 1){
					
					$resultArray = $this->doSubmitFeed($table_errorlog, "Product", $db, $maxFileSize,
						$currentMarketplaceCode, $store, $marketplaceId, $merchantId,
						$endpointUrlMws);
						
						
					$submissionId = $resultArray['SubmissionId'];
					$fileLimitReached = $resultArray['FileLimitReached'];
					$canShortcut = $resultArray['CanShortcut'];
					$result = true;

				}else{
						
					$result = true;
					$canShortcut = true;

				}

				break;
					
			case "WaitingForProductFeedResult":
					
				if(Mage::getStoreConfig('amazonint/amazon'.$staterow['marketplace'].'/enabled_upload') == 1){
						
						
					$result = $this->doProcessFeedResults($previousSubmissionId, $currentMarketplaceCode, $marketplaceId, $merchantId,
						$endpointUrlMws, "Product", $table_errorlog, $db);
	
				}else{
					$result = true;
					$canShortcut = true;
				}
					
				break;
					
			case "WaitingToSubmitImageFeed":
				
				if(Mage::getStoreConfig('amazonint/amazon'.$staterow['marketplace'].'/enabled_images') == 1){
					
					$resultArray = $this->doSubmitFeed($table_errorlog, "Image", $db, $maxFileSize,
						$currentMarketplaceCode, $store, $marketplaceId, $merchantId,
						$endpointUrlMws);
					
					$submissionId = $resultArray['SubmissionId'];
					$fileLimitReached = $resultArray['FileLimitReached'];
					$canShortcut = $resultArray['CanShortcut'];
					$result = true;

				}else{
					$result = true;
					$canShortcut = true;
				}

				break;
					
			case "WaitingForImageFeedResult":
				
				if(Mage::getStoreConfig('amazonint/amazon'.$staterow['marketplace'].'/enabled_images') == 1){
						
					$result = $this->doProcessFeedResults($previousSubmissionId, $currentMarketplaceCode, $marketplaceId, $merchantId,
					$endpointUrlMws, "Image", $table_errorlog, $db);

				}else{

					$result = true;
					$canShortcut = true;
				}

				break;
					
			case "WaitingToSubmitRelationFeed":

				$resultArray = $this->doSubmitFeed($table_errorlog, "Relation", $db, $maxFileSize,
				$currentMarketplaceCode, $store, $marketplaceId, $merchantId,
				$endpointUrlMws);
				$submissionId = $resultArray['SubmissionId'];
				$fileLimitReached = $resultArray['FileLimitReached'];
				$canShortcut = $resultArray['CanShortcut'];
				$result = true;

			break;
					
			case "WaitingForRelationFeedResult":
					
				$result = $this->doProcessFeedResults($previousSubmissionId, $currentMarketplaceCode, $marketplaceId, $merchantId,
				$endpointUrlMws, "Relation", $table_errorlog, $db);
				break;

			break;
		
		}

		if(isset($result) && $result == false){
				
			/* Do nothing; feed has not yet finished processing. */
		
		}else{


			$nextState = $this->calculateNextState($currentState, $canShortcut, $fileLimitReached);
			
			
			if (strlen($submissionId) > 0) {
				$db->query("update {$table_prefix}amazonimport_surestream set state = '$nextState', last_state_change = last_state_change, submission_id = '$submissionId' where marketplace='$currentMarketplaceCode'");
			}
			else
			{
				$db->query("update {$table_prefix}amazonimport_surestream set state = '$nextState' where marketplace='$currentMarketplaceCode'");
			}
				
			if ($canShortcut && $nextState != "WaitingToSubmitProductFeed") {
				$staterow['state'] = $nextState;

				$this->runSurestreamForCountry($currentMarketplaceCode, $marketplaceId, $merchantId,
					$endpointUrlMws, $store, $staterow);
			}

		}
	}

	public function calculateNextState($currentState, $canShortcut, $fileLimitReached) {
			
		$index = array_search($currentState, $this->feedStates);
		$index++;
		if ($index == sizeof($this->feedStates)) {
			$index = 0;
		}

		if ($canShortcut && strpos($this->feedStates[$index], 'Result')) {
			$index++;
			if ($index == sizeof($this->feedStates)) {
				$index = 0;
			}
		}
		return $this->feedStates[$index];
	}

	public function getTempFolder() {
		return "/tmp/";
	}

	public function canSellOnCountry($country_code) {

		return Mage::getStoreConfig("amazonint/amazon".$country_code."/mid") != "";
	}
	
	/**
	 * Function created specifically for importing of orders over specific date ranges.
	 */
	public function orderImportRange() {
		$marketplaces = array();
		$mpid = array();
		$mid = array();
		$url = array();
		$configtree = array();
		$store = array();
		$staterow = array();
			
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$amazon = Mage::getModel('amazonimport/amazonlink');
		

		if($this->canSellOnCountry("uk")){
			$marketplaces[] = "uk";
			$mid[] = Mage::getStoreConfig('amazonint/amazonuk/mid');
			
            $mpid[] = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("uk");
			$url[] = "https://mws.amazonservices.co.uk";

			$store[] = Mage::getStoreConfig('amazonint/amazonuk/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='uk'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
			
		if($this->canSellOnCountry("fr")){
			$marketplaces[] = "fr";
			$mid[] = Mage::getStoreConfig('amazonint/amazonfr/mid');
			
			$mpid[] = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("fr");
			$url[] = "https://mws.amazonservices.fr";

			$store[] = Mage::getStoreConfig('amazonint/amazonfr/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='fr'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
			
		if($this->canSellOnCountry("de")){
			$marketplaces[] = "de";
			$mid[] = Mage::getStoreConfig('amazonint/amazonde/mid');
			
			$mpid[] = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("de");
			$url[] = "https://mws.amazonservices.de";

			$store[] = Mage::getStoreConfig('amazonint/amazonde/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='de'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
			
		if($this->canSellOnCountry("com")){
			$marketplaces[] = "com";
			$mid[] = Mage::getStoreConfig('amazonint/amazoncom/mid');
			
			$mpid[] = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("com");
			$url[] = "https://mws.amazonservices.com";

			$store[] = Mage::getStoreConfig('amazonint/amazoncom/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='com'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
		
		for ($i = 0; $i < sizeof($marketplaces); $i++) {
		
			// are we waiting for a job to finish processing?
			if($staterow[$i]['orderimport_submission_id'] == ''){	// no

			 $waitingfor = $this->createOrdersRequest($url[$i], $mid[$i], $mpid[$i]);
			 
			 $result = $db->query("UPDATE {$table_prefix}amazonimport_surestream SET last_state_change = last_state_change, orderimport_submission_id='".$waitingfor."'
											WHERE marketplace='".$marketplaces[$i]."'");
			 $staterow[$i]['orderimport_submission_id'] = $waitingfor;
			 
			}else{	
			
			 $status = $amazon->checkOrdersReportStatus($url[$i],$mid[$i],$mpid[$i],$staterow[$i]['orderimport_submission_id']);
			 $isJobFinished = $this->evaluateReportState($status, $url[$i], $mid[$i], $mpid[$i], $amazon, $marketplaces[$i],"orders");
			 	
			 if($isJobFinished){
			 
			 	$result = $db->query("UPDATE {$table_prefix}amazonimport_surestream SET last_state_change = last_state_change, orderimport_submission_id=''
											WHERE marketplace='".$marketplaces[$i]."'");

			 }
			 
			}
			
		}
	
	}
	
	/**
	 * Main entry point for the scheduled job.
	 */
	public function runSurestream($jobtype="standard") {
		$marketplaces = array();
		$mpid = array();
		$mid = array();
		$url = array();
		$configtree = array();
		$store = array();
		$staterow = array();
			
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$amazon = Mage::getModel('amazonimport/amazonlink');

		if($this->canSellOnCountry("uk")){
			$marketplaces[] = "uk";
			$mpid[] = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("uk");
			
			$mid[] = Mage::getStoreConfig('amazonint/amazonuk/mid');
			$url[] = "https://mws.amazonservices.co.uk";

			$store[] = Mage::getStoreConfig('amazonint/amazonuk/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='uk'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
			
		if($this->canSellOnCountry("fr")){
			$marketplaces[] = "fr";
			$mpid[] = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("fr");
			
			$mid[] = Mage::getStoreConfig('amazonint/amazonfr/mid');
			$url[] = "https://mws.amazonservices.fr";

			$store[] = Mage::getStoreConfig('amazonint/amazonfr/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='fr'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
			
		if($this->canSellOnCountry("de")){
			$marketplaces[] = "de";
            
            $mktTemp = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("de");
            
			$mpid[] = $mktTemp;
			$mid[] = Mage::getStoreConfig('amazonint/amazonde/mid');
			$url[] = "https://mws.amazonservices.de";

			$store[] = Mage::getStoreConfig('amazonint/amazonde/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='de'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
			
		if($this->canSellOnCountry("com")){
			$marketplaces[] = "com";
			$mpid[] = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId("com");
			$mid[] = Mage::getStoreConfig('amazonint/amazoncom/mid');
			$url[] = "https://mws.amazonservices.com";

			$store[] = Mage::getStoreConfig('amazonint/amazoncom/store');
			$result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream WHERE marketplace='com'");
			$staterow[] = $result->fetch(PDO::FETCH_ASSOC);
		}
		
		
		for ($i = 0; $i < sizeof($marketplaces); $i++) {
				
			if($jobtype == "standard"){

				$lastStateChange = str_replace(" ", "T", $staterow[$i]['last_state_change']);
					
			 // Run job for 1 country (7 params)
			 if($staterow[$i]['running_flag'] == 0){

			 		
			 	$result = $db->query("UPDATE {$table_prefix}amazonimport_surestream set running_flag=1 WHERE marketplace='".$staterow[$i]['marketplace']."'");
                 
			 	try {
				 	$this->runSurestreamForCountry($marketplaces[$i], $mpid[$i], $mid[$i],
				 		$url[$i], $store[$i], $staterow[$i]);
				}catch (Exception $e) {
			 		/* Do nothing */
                    echo "Exception runSurestreamForCountry $e\n";
				}
                 
			 	$result = $db->query("UPDATE {$table_prefix}amazonimport_surestream set running_flag=0 WHERE marketplace='".$staterow[$i]['marketplace']."'");

			 }else{

			 		
			 	if ((time() - strtotime($lastStateChange)) > 60*60) {
			 		// If running for an hour or more - hung - reset
			 		$db->query("UPDATE {$table_prefix}amazonimport_surestream set running_flag=0, submission_id=0, state='WaitingToSubmitProductFeed'
			 						 WHERE marketplace='".$staterow[$i]['marketplace']."'");
					
					// rewind any message IDs which have been set, as we'll need to retry generation.
					$table_errorlog = $table_prefix."amazonimport_errorlog_".$staterow[$i]['marketplace'];
					$sqlTemp = "update ".$table_errorlog." set result='', result_description='', messageid='' where messageid != 0 and result = ''";
					$db->query($sqlTemp);
					
			 	}
			 }

			}else if($jobtype == "orderimport"){

				// is order importing enabled in the settings?

				if(Mage::getStoreConfig('amazonint/amazon'.$staterow[$i]['marketplace'].'/enabled_import') == 1){

					// are we waiting for a job to finish processing?
					if($staterow[$i]['orderimport_submission_id'] == ''){	// no
					
					 $waitingfor = $amazon->createOrdersRequest($url[$i],$mid[$i],$mpid[$i]);
					 $result = $db->query("UPDATE {$table_prefix}amazonimport_surestream SET last_state_change = last_state_change, orderimport_submission_id='".$waitingfor."'
													WHERE marketplace='".$marketplaces[$i]."'");
					 $staterow[$i]['orderimport_submission_id'] = $waitingfor;
					 	
					 
					}else{

					 $status = $amazon->checkOrdersReportStatus($url[$i],$mid[$i],$mpid[$i],$staterow[$i]['orderimport_submission_id']);
					 $isJobFinished = $this->evaluateReportState($status, $url[$i], $mid[$i], $mpid[$i], $amazon, $marketplaces[$i],"orders");
					 	
					 if($isJobFinished){
					 	
						$result = $db->query("UPDATE {$table_prefix}amazonimport_surestream SET last_state_change = last_state_change,  
											  orderimport_submission_id=''
													WHERE marketplace='".$marketplaces[$i]."'");

					 }
					}

				}

			}else if($jobtype == "productimport"){

				// are we waiting for a job to finish processing?
				if($staterow[$i]['productimport_submission_id'] == ''){	// no

					$waitingfor = $amazon->createActiveListingsRequest($url[$i],$mid[$i],$mpid[$i]);
					$result = $db->query("UPDATE {$table_prefix}amazonimport_surestream SET last_state_change = last_state_change, 
										 	productimport_submission_id='".$waitingfor."'
										 	WHERE marketplace='".$marketplaces[$i]."'");
					$staterow[$i]['productimport_submission_id'] = $waitingfor;
						
				}else{
					
					$status = $amazon->checkOrdersReportStatus($url[$i],$mid[$i],$mpid[$i],$staterow[$i]['productimport_submission_id']);
					$isJobFinished = $this->evaluateReportState($status, $url[$i], $mid[$i], $mpid[$i], $amazon, $marketplaces[$i],"product");
					if($isJobFinished){

						$result = $db->query("UPDATE {$table_prefix}amazonimport_surestream SET last_state_change = last_state_change, 
											 productimport_submission_id=''
										 	WHERE marketplace='".$marketplaces[$i]."'");
			
					}
					
				}

			}

				
		}
		
	}


	public function evaluateReportState($status, $url, $mid, $mpid, $amazon, $marketplace, $processingType){
		
		if($status == "_SUBMITTED_"){
			return false;
		}else if($status == "_IN_PROGRESS_"){
			return false;
		}else if($status == "_CANCELLED_"){
			return true;
		}else if($status == "_DONE_NO_DATA_"){
			return true;
		}else{
			$output = $amazon->downloadOrdersReport($url, $mid, $mpid, $status, $processingType);
				
			if($processingType == "orders"){
				$this->goimport($output, $marketplace, "amazon".$marketplace);
			}else{
				$this->importActivelistings($output, $marketplace, "amazon".$marketplace);
			}
				
			return true;
		}
			
	}

	public function orderImport(){
		
		try {
			if(session_id() == '') session_start();
		  
		}catch (Exception $e1) { }
		
		$this->runSurestream("orderimport");
		
	}

	public function productImport(){
		
		try {
			if(session_id() == '') session_start();
		  
		}catch (Exception $e1) { }
		
		$this->runSurestream("productimport");
		
	}

	
	public function refreshLicenseExpiryInfo(){
		 Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS));	
	}

	public function refreshStockOrPrice($type){
		
		$marketplaces = array();
		$db = Mage::getSingleton("core/resource")->getConnection("core_write");
		$table_prefix = Mage::getConfig()->getTablePrefix();
		$amazonlink = Mage::getModel('amazonimport/amazonlink');
		
		if ($this->canSellOnCountry("uk")){
		    $marketplaces["https://mws.amazonservices.co.uk"] = "uk"; 
		}
		if ($this->canSellOnCountry("fr")){
		    $marketplaces["https://mws.amazonservices.fr"] = "fr"; 
		}
		if ($this->canSellOnCountry("de")){
		    $marketplaces["https://mws.amazonservices.de"] = "de"; 
		}
		if ($this->canSellOnCountry("com")){
		    $marketplaces["https://mws.amazonservices.com"] = "com"; 
		}
		
		foreach ($marketplaces as $url=>$mkt)
		{
		    $myTempFileName = "./".$type."toamz".time()."".$mkt.".xml";
		    $file = @fopen($myTempFileName, "w+");

		    if (FALSE === $file) {

		    	$myTempFileName = "/tmp/".$type."toamz".time()."".$mkt.".xml";
		   	$file = fopen($myTempFileName, "w+");

		    }

		    if (Mage::getStoreConfig('amazonint/amazon'.$mkt.'/enabled_prices') == 0 && $type == "Price"){
                        continue;
		    }
                    if (Mage::getStoreConfig('amazonint/amazon'.$mkt.'/enabled_sync') == 0 && $type == "Stock"){
                        continue;
                    }

            $listThisTable = 
                'amazonimportlistthis'.$mkt.'/amazonimportlistthis'.$mkt;
            
            $result = Mage::getModel('catalog/product')->getCollection()
                ->joinTable($listThisTable, 'productid=entity_id',
                    array('productid' => 'productid', 
                        'is_active' => 'is_active'), null,
                    'left')->addFieldToFilter(
                'is_active', array('eq' => '1'));						
            $result->getSelect()->distinct(true);

            $mpid = Mage::getModel('amazonimport/amazonimport')
                ->getMarketplaceId($mkt);
            $mid = Mage::getStoreConfig('amazonint/amazon'.$mkt.'/mid');
            $store = Mage::getStoreConfig('amazonint/amazon'.$mkt.'/store');
            $count = 1;
            
            foreach($result as $item){
                $row = array('productid'=>$item->getId(),'elog_id'=>'999999');
                $msg = $amazonlink->getMessageFragment($row, $mkt, $store,
                    $count, $type);
                
                if ($msg != '') {

					$count++;
					fwrite($file, $msg);
				}
            }
            
            fclose($file);
            
            $amazonlink->submitfeed($myTempFileName, $type, $mkt, $mpid, 
                $mid, $url);
            @unlink($myTempFileName);
		}
	}
	
	public function runDispatches(){
			
		$mps = array("com","uk","de","fr");
		foreach($mps as $marketplace){

		 $db = Mage::getSingleton("core/resource")->getConnection("core_write");
		 $table_prefix = Mage::getConfig()->getTablePrefix();
		 
		 	
		 $result = $db->query("SELECT * FROM {$table_prefix}amazonimport_surestream_shipping WHERE marketplace='"
		 .$marketplace."' AND (`last_update`+60) < (NOW()+0)");
		 if($result->rowCount() > 0){
		 	Mage::getModel('amazonimport/amazonlink')->submitMultipleShipping($marketplace);
		 }
		 
	 }

	}

}


?>
