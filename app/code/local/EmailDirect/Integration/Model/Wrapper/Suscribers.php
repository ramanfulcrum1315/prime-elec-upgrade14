<?php
/*
 * Created on Nov 29, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Model_Wrapper_Suscribers
{
   public function suscriberModify($mail,$mergeVars)
   {
      $sourceid = Mage::helper('emaildirect')->config('sourceid'); //Mage::getStoreConfig('emaildirect/general/sourceid');
      $publicationid = Mage::helper('emaildirect')->config('publication'); //Mage::getStoreConfig('emaildirect/general/publication');
      $listid = Mage::helper('emaildirect')->config('list');
      $data = "<CustomFields>";
      foreach($mergeVars as $key => $value)
      {
         $data .= "<CustomField><FieldName>$key</FieldName><Value><![CDATA[$value]]></Value></CustomField>";
      }
      $data .= "</CustomFields>";
      if($listid != -1) {
      $xml = "<Subscriber><EmailAddress>$mail</EmailAddress>$data<SourceID>$sourceid</SourceID><Publications><int>$publicationid</int></Publications><Lists><int>$listid</int></Lists></Subscriber>";
      }
      else {
         $xml = "<Subscriber><EmailAddress>$mail</EmailAddress>$data<SourceID>$sourceid</SourceID><Publications><int>$publicationid</int></Publications></Subscriber>";
      }
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","",$mail,$xml,true);
      if(isset($rc->ErrorCode)) {
         return FALSE;
      }
      return TRUE;
   }
   public function suscriberAdd($mail,$mergeVars,$uselist = TRUE)
   {
      $store = Mage::app()->getStore();
      $sourceid = Mage::helper('emaildirect')->config('sourceid',$store->getId());
      $publicationid = Mage::helper('emaildirect')->config('publication',$store->getId());
      if($uselist) {
         $listid = Mage::helper('emaildirect')->config('list',$store->getId());
      }
      else {
         $listid = -1;
      }
      $data = "<CustomFields>";
      foreach($mergeVars as $key => $value)
      {
         $data .= "<CustomField><FieldName>$key</FieldName><Value><![CDATA[$value]]></Value></CustomField>";
      }
      $data .= "</CustomFields>";
      if($listid != -1) {
         $xml = "<Subscriber><EmailAddress>$mail</EmailAddress>$data<SourceID>$sourceid</SourceID><Publications><int>$publicationid</int></Publications><Lists><int>$listid</int></Lists><Force>true</Force></Subscriber>";
      }
      else {
         $xml = "<Subscriber><EmailAddress>$mail</EmailAddress>$data<SourceID>$sourceid</SourceID><Publications><int>$publicationid</int></Publications><Force>true</Force></Subscriber>";
      }
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","","",$xml,false);
   }
   
   public function modifyAddress($mail,$address)
   {
//    Mage::log($address);
      $streets = $address->getStreet();
      $street = $streets[0];
      if(isset($streets[1])) {
         $street .= " ".$streets[1];
      }
      $zip = $address->getPostcode();
      $city = $address->getCity();
      $state = $address->getRegion();
      $phone = $address->getTelephone();
      $data = "<CustomFields>";
      $data .= "<CustomField><FieldName>Address</FieldName><Value><![CDATA[$street]]></Value></CustomField>";
      $data .= "<CustomField><FieldName>Zip</FieldName><Value><![CDATA[$zip]]></Value></CustomField>";
      $data .= "<CustomField><FieldName>City</FieldName><Value><![CDATA[$city]]></Value></CustomField>";
      $data .= "<CustomField><FieldName>State</FieldName><Value><![CDATA[$state]]></Value></CustomField>";
      $data .= "<CustomField><FieldName>Phone</FieldName><Value><![CDATA[$phone]]></Value></CustomField>";
      $data .= "</CustomFields>";
      $xml = "<Subscriber><EmailAddress>$mail</EmailAddress>$data<Force>true</Force></Subscriber>";
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","","",$xml,false);
   }
   
   // Not used
   public function abandonedAdd($mail,$mergeVars)
   {
      $sourceid = Mage::helper('emaildirect')->config('sourceid');
      $publicationid = Mage::helper('emaildirect')->config('abandonedpublication');
      $listid = Mage::helper('emaildirect')->config('abandonedlist');
      $data = "<CustomFields>";
      foreach($mergeVars as $key => $value)
      {
         $data .= "<CustomField><FieldName>$key</FieldName><Value><![CDATA[$value]]></Value></CustomField>";
      }
      $data .= "</CustomFields>";
      if($listid != -1) {
         $xml = "<Subscriber><EmailAddress>$mail</EmailAddress>$data<SourceID>$sourceid</SourceID><Publications><int>$publicationid</int></Publications><Lists><int>$listid</int></Lists><Force>true</Force></Subscriber>";
      }
      else {
         $xml = "<Subscriber><EmailAddress>$mail</EmailAddress>$data<SourceID>$sourceid</SourceID><Publications><int>$publicationid</int></Publications><Force>true</Force></Subscriber>";
      }
      
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","","",$xml,false);
   }
   
   
   private function fixBouncedMail($oldmail,$newmail)
   {
      $xml = "<Subscriber><EmailAddress>$oldmail</EmailAddress><Force>true</Force></Subscriber>";
      
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","","",$xml, false);
      
      if(isset($rc->ErrorCode))
      {
         Mage::getSingleton('customer/session')->addError((string)$rc->Message);
         Mage::throwException((string)$rc->Message);
      }
      
      $xml = "<Subscriber><EmailAddress>$newmail</EmailAddress></Subscriber>";
      
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","ChangeEmail",$oldmail,$xml);
      
      if(isset($rc->ErrorCode))
      {
         Mage::getSingleton('customer/session')->addError((string)$rc->Message);
         Mage::throwException((string)$rc->Message);
      }
   }
   
   public function mailModify($oldmail,$newmail)
   {
      $xml = "<Subscriber><EmailAddress>$newmail</EmailAddress></Subscriber>";
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","ChangeEmail",$oldmail,$xml);
      if(isset($rc->ErrorCode))
      {
         if ($rc->ErrorCode == 202)
         {
            $this->fixBouncedMail($oldmail, $newmail);
         }
         else 
         {
            Mage::getSingleton('customer/session')->addError((string)$rc->Message);
            Mage::throwException((string)$rc->Message);
         }
      }
   }
   public function listsForEmail($email)
   {
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","Properties",$email);
//    Mage::log($rc);
//    foreach($rc->Lists->List as $list)
//    {
//
//    }
      return array();
   }
   public function suscriberHistory($mail)
   {
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","History",$mail);
      return $rc;
   }
   public function getProperties($mail)
   {
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","Properties",$mail);
      return $rc;
   }
}
