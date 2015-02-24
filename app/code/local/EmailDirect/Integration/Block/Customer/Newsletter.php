<?php
/*
 * Created on Dec 15, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Block_Customer_Newsletter extends Mage_Customer_Block_Newsletter {

	public function isEmailDirectEnabled($store) {
//		Mage::log(__METHOD__);
		return  true;
	}
	public function getSubscribedAdditionalLists() {
//		Mage::log(__METHOD__);
	}
}
