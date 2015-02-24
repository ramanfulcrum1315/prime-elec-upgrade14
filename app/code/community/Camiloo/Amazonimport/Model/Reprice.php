<?php

	class Camiloo_Amazonimport_Model_Reprice extends Camiloo_Amazonimport_Model_Amazonimport
	{
		
		// USAGE: 	Mage::getModel('amazonimport/reprice')->load("ASIN","UK");
		/* RETURNS:	Varien_Data_Collection {
						Varien_Object {
							'asin' => B00JAJAA
							'country' => Jacks Emporium
							'condition' => Jacks Emporium
							'sellername' => Jacks Emporium
							'price' => 14.99
							'shipping' => 1.99
							'total' => 16.98
							'stars' => 5
							'fba' => 0
							'rcount' => 1272
						}
						... list of results ...
					}
					
		*/
				
		public function loadPriceData($asin, $country, $condition="new"){
			
			// this process will get the Amazon Prices for a given product and assemble them into a data
			// collection for later usage.
			$finalResults = new Varien_Data_Collection();
			
			$country = strtoupper($country);
			$condition = strtolower($condition);
			
			if($country == "UK"){
				$url = "http://www.amazon.co.uk/gp/offer-listing/".$asin."/ref=dp_olp_new_mbc?ie=UTF8&condition=".$condition;
			}else if($country == "DE"){
				$url = "http://www.amazon.de/gp/offer-listing/".$asin."/ref=dp_olp_new_mbc?ie=UTF8&condition=".$condition;
			}else if($country == "FR"){
				$url = "http://www.amazon.fr/gp/offer-listing/".$asin."/ref=dp_olp_new_mbc?ie=UTF8&condition=".$condition;
			}else if($country == "COM"){
				$url = "http://www.amazon.com/gp/offer-listing/".$asin."/ref=dp_olp_new_mbc?ie=UTF8&condition=".$condition;
			}else if($country == "CA"){
				$url = "http://www.amazon.ca/gp/offer-listing/".$asin."/ref=dp_olp_new_mbc?ie=UTF8&condition=".$condition;
			}else if($country == "JP"){
				$url = "http://www.amazon.jp/gp/offer-listing/".$asin."/ref=dp_olp_new_mbc?ie=UTF8&condition=".$condition;
			}
			
			$scanresult = $this->getDataFromUrl($url);
			
			$results = $this->breakitup('<tbody class="result">','</tbody>',$scanresult);
			
			if(sizeof($results) > 0){
				foreach($results as $offer){

			
					$offerData = new Varien_Object();
					$offerData->setAsin($asin);
					$offerData->setCountry($country);
					$offerData->setCondition($condition);
				
					$sellertemp = $this->breakitup('<ul class="sellerInformation">','<li>',$offer);
		
					$stmp = $this->breakitup('alt="','"',$sellertemp[0]);
					$sellername = $stmp[0];
					if($sellername == ""){
						$sellertemp = $this->breakitup('<ul class="sellerInformation">','</a>',$offer);
						$sellertemp = $this->breakitup('<b>','</b>',$sellertemp[0]);
						$sellername = $sellertemp[0];
					}
					// get price for offer. - store as $price
					
					///// BUGFIX WHQ-935-38269 & GWT-498-47451 - EUR price has commas instead of periods as delimiters.
					///// Not handling this was causing issues with price calculation. 
					$pricestemp = $this->breakitup('<span class="price">','</span>',$offer);
					
					if (!isset($pricestemp[0])) {
					    continue;
					}
					
					$price = strrev($pricestemp[0]);
					
					if (!isset($price[2])) {
					    continue;
					}
					
					$price[2] = str_replace(",",".",$price[2]);
					$price = strrev($price);
					$price = preg_replace('/[^0-9\.]+/i', '', $price); 
					
					// get shipping surcharge - store as $shipping
					$shippingtemp = $this->breakitup('<span class="price_shipping">','</span>',$offer);
					
					if (!isset($shippingtemp[0])) {
					    continue;
					}
					
					$shipping = strrev($shippingtemp[0]);
					if (isset($shipping[2])) {
                        $shipping[2] = str_replace(",", ".", $shipping[2]);
                        if (is_array($shipping)) { $shipping = "0"; }
					}
					$shipping = strrev($shipping);
					$shipping = preg_replace('/[^0-9\.]+/i', '', $shipping); 
					
					if($shipping == ""){
						$shipping = 0;	
					}
					$total = $price + $shipping;
					
					// get star rating - store as $stars
					$starstemp = $this->breakitup('/detail/stars-','._V',$offer);
					$stars = str_replace("-",".",$starstemp[0]);
					
					// get merchant rating count - store as $rcount
					$rcounttemp = $this->breakitup('<a href="/gp/help/seller/at-a-glance.html/ref=olp_merch_rating_',')</div>',$offer);
					$merchName = false;
					
					if(sizeof($rcounttemp) == 0) {
		
						$rcounttemp = $this->breakitup('<span class="ratingHeader"',')</div>',$offer);
						$merchName = true;
					
					}
					
					if($merchName) {
						$rcount = explode(" ",strip_tags($rcounttemp[0]));
					
					}
					else {
						$rcount = explode(" ",strip_tags('<a href="/gp/help/seller/at-a-glance.html/ref=olp_merch_rating_'.$rcounttemp[0]));
					}
					foreach($rcount as $rtemp) {
						if (strlen($rtemp) > 0 && $rtemp[0] == '(') { // does string start with (
							$rcount = $rtemp;
							break;
						}
					}
					if(is_array($rcount)) { // Failsafe if above loop has 0 matches
						$rcount = $rcount[0];
					}
					if($rcount == "Seller"){
						$rcount = "";	
					}
					
					// Remove non-numerics
					$rcount = str_replace(array("(", ","), array("", ""), $rcount);
					// is this offer FBA? 
					$fbatemp = $this->breakitup('&amp;isAmazonFulfilled=','&amp;',$offer);
					$fba = $fbatemp[0];
					
					// thats all we need for now - output to the array.					
					
					$offerData->setSellername(mysql_escape_string($sellername));
					$offerData->setPrice($price);
					$offerData->setShipping($shipping);
					$offerData->setTotal($total);
					if($stars > 0){
						$offerData->setStars($stars);
					}
					$offerData->setFba($fba);
					if($rcount > 0){
						$offerData->setRcount($rcount);
					}else{
						$offerData->setRcount(0);
					}
					$finalResults->addItem($offerData);
				}
			}
			return $finalResults;
			
		}		
		
		public function getMerchantName($merchantId, $country){
			
			$country = strtoupper($country);
			
			if($country == "UK"){
				$url = "http://www.amazon.co.uk/gp/help/seller/at-a-glance.html?ie=UTF8&seller=".$merchantId;
			}else if($country == "DE"){
				$url = "http://www.amazon.de/gp/help/seller/at-a-glance.html?ie=UTF8&seller=".$merchantId;
			}else if($country == "FR"){
				$url = "http://www.amazon.fr/gp/help/seller/at-a-glance.html?ie=UTF8&seller=".$merchantId;
			}else if($country == "COM"){
				$url = "http://www.amazon.com/gp/help/seller/at-a-glance.html?ie=UTF8&seller=".$merchantId;
			}else if($country == "CA"){
				$url = "http://www.amazon.ca/gp/help/seller/at-a-glance.html?ie=UTF8&seller=".$merchantId;
			}else if($country == "JP"){
				$url = "http://www.amazon.jp/gp/help/seller/at-a-glance.html?ie=UTF8&seller=".$merchantId;
			}
			$scanresult = $this->getDataFromUrl($url);
			
			// do the processing here.
			$merchantName = $this->breakitup(": ","</title>",$scanresult);
			return $merchantName[0];
			
		}
		
		
		public function getDataFromUrl($url){
			
				// If you're a developer looking at this and licking their lips, prearing to remove the caching below,
				// please stop :) Doing so could potentially result in Amazon blocking the repricing engines access to Amazon
				// for ALL users of the software, and we will have no control over this. You've been warned!
				// The repricing engine will refresh caches automatically once every 12 hours.
				$cachedir = Mage::getModel('amazonimport/amazonimport')->getLocalRoot()."media/amzpricecache";
				
				if(!is_dir($cachedir)){
					mkdir($cachedir);
				}
				
				// some systems may not allow us to save cache. if not, carry on
				if(is_dir($cachedir)){
					if(file_exists($cachedir."/".md5($url).".dat")){
						
						// is the file older than 1 hour?
						if((time() - filemtime($cachedir."/".md5($url).".dat")) > (60*60)){
							
							// file is out of date - update.
							$scanresult = $this->curlmeup($url);
							$file = fopen($cachedir."/".md5($url).".dat","w+");
							fwrite($file,$scanresult);
							fclose($file);
							
						
						}else{
						
							// load from cache.
							$scanresult = file_get_contents($cachedir."/".md5($url).".dat","w+");
							
						}
					
					}else{
					
						// cachefile doesn't exist; create.
						$scanresult = $this->curlmeup($url);
						$file = fopen($cachedir."/".md5($url).".dat","w+");
						fwrite($file,$scanresult);
						fclose($file);
						
					}
				}else{
					
					$scanresult = $this->curlmeup($url);
					
				}
				
				return $scanresult;
				
		}
		
		public function sellersToIgnore($country) {
			$setting = Mage::getStoreConfig('amazonint/repricing'.$country.'/ignore_sellers');
			return $setting;
		}
		
		
		public function ignorePennySellers($cmc) {
		$setting = Mage::getStoreConfig('amazonint/repricing'.$cmc.'/ignore_penny_sellers');
		return $setting == "1";
		}
		
		public function valueToUndercutBy($cmc) {
			$setting = Mage::getStoreConfig('amazonint/repricing'.$cmc.'/undercut_value');
			return $setting;
		}
		
		public function minimumPriceAttribute($cmc) {
			$setting = Mage::getStoreConfig('amazonint/repricing'.$cmc.'/minimum_price_attribute');
			return $setting;
		}
		
		public function calculatedPriceAttribute($cmc) {
			$setting = Mage::getStoreConfig('amazonint/repricing'.$cmc.'/competitive_price_attribute');
			return $setting;
		}
		
		public function excludeFeedbackBelow($cmc) {
			$setting = Mage::getStoreConfig('amazonint/repricing'.$cmc.'/exclude_feedback_below');
			return $setting;
		}
	
		public function excludeRatingsBelow($cmc) {
			$setting = Mage::getStoreConfig('amazonint/repricing'.$cmc.'/exclude_ratings_below');
			return $setting;
		}
		
		public function curlmeup($urltograb){
		
			$session = curl_init("$urltograb");
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_USERAGENT, "Camiloo Amazon Integration for Magento Repricing Engine/2.2 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)");
			curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($session, CURLOPT_TIMEOUT, 30);
			curl_setopt($session, CURLOPT_COOKIEJAR, "/tmp/cookie.txt");
			curl_setopt($session, CURLOPT_COOKIEFILE, "/tmp/cookie.txt");
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($session);
			return $result;
		
		}
		
		public function breakitup($colim, $delim, $input){
	
				// this function splits the results up as its told to.
				$tempstr = explode("$colim","$input");
				
				foreach($tempstr as $key => $value){
					if($key > 0){
						$tempstr2 = explode("$delim","$value");
						$output[$key-1] = $tempstr2[0];
					}				
	
				}
				
				if(!isset($output)){
					
				}else{
					return $output;
				}
			
		}
		
		
		
		
		
		
	}



?>