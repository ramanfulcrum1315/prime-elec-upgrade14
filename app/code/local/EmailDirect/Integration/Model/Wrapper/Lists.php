<?php
/*
 * Created on Nov 14, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_Wrapper_Lists
{
	public function __construct($args)
	{
		$storeId = isset($args['store']) ? $args['store'] : null;
		$apikey  = (!isset($args['apikey']) ? Mage::helper('emaildirect')->getApiKey($storeId) : $args['apikey']);

	}

	public function getLists()
	{
		$lists = array();
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('lists');
		foreach($rc->List as $list)
		{
			$newlist = array('id' => (int)$list->ListID,
							'name' => (string)$list->Name,
							'members' => (int)$list->ActiveMembers,
							'active' => (boolean)$list->IsActive
							);
			$lists[] = $newlist;
		}
		return $lists;
	}

	public function listUnsubscribe($listId, $email)
	{
		$xml = "<Subscribers><EmailAddress>$email</EmailAddress></Subscribers>";
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('lists','RemoveEmails',$listId,$xml);
		if(isset($rc->ErrorCode))
		{
			Mage::getSingleton('customer/session')->addError((string)$rc->Message);
			Mage::throwException((string)$rc->Message);
		}
		elseif((int)$rc->ContactsSubmitted != (int)$rc->Successes) {
			Mage::getSingleton('customer/session')->addError((string)$rc->Failures->Failure->Message);
			Mage::throwException((string)$rc->Failures->Failure->Message);
		}
	}

	public function listSubscribe($listId, $email)
	{
		// ask if the customer is a subscriber
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('subscribers',null,$email,null);
		// if already a subscriber
		if(isset($rc->EmailID)) {
//			Mage::log("only subscribe");
			$xml = "<Subscribers><EmailAddress>$email</EmailAddress></Subscribers>";
			$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('lists','AddEmails',$listId,$xml);
		}
		else {
//			Mage::log("add subscriber and subscribe to list");

		}
	}
}
