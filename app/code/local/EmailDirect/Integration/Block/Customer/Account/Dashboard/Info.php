<?php

class EmailDirect_Integration_Block_Customer_Account_Dashboard_Info extends Mage_Customer_Block_Account_Dashboard_Info 
{
    public function IsSubscribed($email)
    {
		$rc = Mage::getSingleton('emaildirect/wrapper_suscribers')->getProperties($email);
		$lists = array();
	
		$abandonedID = Mage::helper('emaildirect')->config('abandonedpublication');
		
		if($rc->Publications->Publication && $rc->Publications->Publication->PublicationID != $abandonedID) {
			$lists[]=(string)$rc->Publications->Publication->Name;
		}
		$abandonedID = Mage::helper('emaildirect')->config('abandonedlist');
		
		foreach($rc->Lists->List as $i) {
			if($i->ListID != $abandonedID) {
				$lists[]=(string)$i->Name;
			}
		}
		return $lists;
		
//        return $this->getSubscriptionObject()->isSubscribed();
    }
	
}
