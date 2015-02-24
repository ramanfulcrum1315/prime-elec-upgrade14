<?php
class EmailDirect_Integration_Model_Wrapper_Abandoned
{
   public function sendSubscribers($xml)
   {
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("abandoned","","",$xml,false);
		return $rc;
   }
   
   public function getOneSubscriber($mail,$mergeVars)
   {
      $sourceid = Mage::helper('emaildirect')->config('sourceid');
      $publicationid = Mage::helper('emaildirect')->config('abandonedpublication');
      $listid = Mage::helper('emaildirect')->config('abandonedlist');
      
      $data = "<CustomFields>";
      foreach($mergeVars as $key => $value)
      {
         $data .= "<CustomField><FieldName>{$key}</FieldName><Value><![CDATA[{$value}]]></Value></CustomField>";
      }
      $data .= "</CustomFields>";
      
      $list_data = "";
      
      if ($listid != -1)
         $list_data = "<Lists><int>$listid</int></Lists>";
      
      $xml = "<Subscriber><EmailAddress>{$mail}</EmailAddress>{$data}<SourceID>{$sourceid}</SourceID>";
      $xml .= "<Publications><int>{$publicationid}</int></Publications>{$list_data}<Force>true</Force></Subscriber>";
      
      //Mage::log($xml,null,"1_quote.log");
      
      return $xml;
   }
}
