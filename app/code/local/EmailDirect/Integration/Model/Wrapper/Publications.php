<?php
/*
 * Created on Dec 6, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_Wrapper_Publications
{
	public function getPublications()
	{
		$publications = array();
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('publications');
		foreach($rc->Publication as $publication)
		{
			$newpublication = array('id' => (int)$publication->PublicationID,
							'name' => (string)$publication->Name,
							'members' => (int)$publication->ActiveMembers,
							'active' => (boolean)$publication->IsActive
							);
			$publications[] = $newpublication;
		}
		return $publications;
	}
	public function subscribe($id, $email)
	{
		$xml = "<Subscribers><EmailAddress>$email</EmailAddress></Subscribers>";
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('publications','AddEmails',$id,$xml);
		return $rc;
	}
	public function unsubscribe($id,$email)
	{
		$xml = "<Subscribers><EmailAddress>$email</EmailAddress></Subscribers>";
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('publications','RemoveEmails',$id,$xml);
		return $rc;
	}
	public function getPublication($id) {
		$xml = "";
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('publications','',$id,$xml);
		return $rc;
	}
}
