<?php

	class Camiloo_Amazonimport_Model_Competitorsuk extends Varien_Object {
	
	
		public function toOptionArray(){
			$country = "uk";
			
			// this will be used by the multiselect in system.xml for the sellernames to exclude.	
			$secBaseUrl = Mage::getStoreConfig('web/secure/base_url');
			$repricing = Mage::getModel('amazonimport/repricing');
			$response = simplexml_load_string($repricing->curlposter($repricing->getRepricingBase(), "licensekey={$secBaseUrl}&action=GetCompetitorsForExclusion&merchantname=".$repricing->getMyName($country)."&country=".$country));
			
			$results = array();
			
			if(is_object($response)) {
				if ($response->SellerNames[0]) {
				
					foreach($response->SellerNames[0] as $sellername){
						$results[] = array('label'=>urldecode($sellername),'value'=>urldecode($sellername));
					}
					
				}
			}
			return $results;
			
		}
		
	}

?>