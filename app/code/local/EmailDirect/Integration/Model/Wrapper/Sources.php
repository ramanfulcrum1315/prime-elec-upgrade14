<?php
/*
 * Created on Dec 6, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_Wrapper_Sources
{
	public function getSources()
	{
		$sources = array();
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('sources');
		foreach($rc->Source as $source)
		{
			$newsource = array('id' => (int)$source->SourceID,
							'name' => (string)$source->Name,
							'members' => (int)$source->ActiveMembers,
							'active' => (boolean)$source->IsActive
							);
			$sources[] = $newsource;
		}
		return $sources;
	}
	public function addSource($name)
	{
		$xml = "<Source><Name>$name</Name><Description>$name</Description></Source>";
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand('sources',"",null,$xml,false);
		return $rc;
	}
}
