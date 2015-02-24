<?php
/*
 * Created on Dec 22, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_Wrapper_Relays
{
	public function addCategory($name)
	{
		$categoryId = $this->getCategory($name);
		if($categoryId) {
			return $categoryId;
		}
		$xml = "<string>$name</string>";
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("relays","","",$xml,false);
		if(isset($rc->RelaySendCategory->RelaySendCategoryID)) {
			return $rc->RelaySendCategory->RelaySendCategoryID;
		}
		else {
			return 0;
		}
	}
	public function getCategory($name)
	{
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("relays","","",null);
		foreach($rc->Category as $category) {
			if($category->Name == $name) {
				return $category->RelaySendCategoryID;
			}
		}
		return false;
	}

	public function sendMail($senderName,$senderEmail,$toEmail,$toName,$subject,$text)
	{
		$relayId = Mage::Helper('emaildirect')->config('relayid');
//		$text = htmlentities($text);

		if(is_array($toEmail)) {
			$auxemail = implode(",",$toEmail);
		}
		else {
			$auxemail = $toEmail;
		}
		if(is_array($toName)) {
			$auxname = implode(",",$toName);
		}
		else {
			$auxname = $toName;
		}
		
//		$xml = "<RelaySend><ToEmail>$toEmail</ToEmail><ToName>$toName</ToName><FromName>$senderName</FromName><FromEmail>$senderEmail</FromEmail><Subject>$subject</Subject><HTML>$text</HTML><Text>$text</Text></RelaySend>";
////		$xml = "<RelaySend><ToEmail><![CDATA[$auxemail]]></ToEmail><ToName><![CDATA[$auxname]]></ToName><FromName><![CDATA[$senderName]]></FromName><FromEmail><![CDATA[$senderEmail]]></FromEmail><Subject><![CDATA[$subject]]></Subject><HTML><![CDATA[$text]]></HTML></RelaySend>";
////		if(Mage::registry('track')==1) {
			$replace = '<br><img src="[Link_Impression]" width="5" height="5" \></body>';
			$textmail = preg_replace("/\<\/body\>/",$replace,$text);
			$xml = "<RelaySend><ToEmail><![CDATA[$auxemail]]></ToEmail><TrackLinks>true</TrackLinks><Force>true</Force><ToName><![CDATA[$auxname]]></ToName><FromName><![CDATA[$senderName]]></FromName><FromEmail><![CDATA[$senderEmail]]></FromEmail><Subject><![CDATA[$subject]]></Subject><HTML><![CDATA[$textmail]]></HTML></RelaySend>";
////		}
////		else {
////			$xml = "<RelaySend><ToEmail><![CDATA[$auxemail]]></ToEmail><ToName><![CDATA[$auxname]]></ToName><FromName><![CDATA[$senderName]]></FromName><FromEmail><![CDATA[$senderEmail]]></FromEmail><Subject><![CDATA[$subject]]></Subject><HTML><![CDATA[$text]]></HTML></RelaySend>";
////		}
		$rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("relays","",$relayId,$xml);
	}
}
