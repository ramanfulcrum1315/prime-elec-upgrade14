<?php

class EmailDirect_Integration_Model_Wrapper_Orders
{
   
   public function getOrderXml($order, $email = null)
   {
      $prefix = Mage::helper('emaildirect')->config('prefix');
		
      $date = $order->getCreatedAt();
      $orderNum = $prefix.$order->getIncrementId();
      
      $items = $order->getAllItems();
      
      $xml = "<Order>";
      if ($email != null)
         $xml .= "<EmailAddress><![CDATA[{$email}]]></EmailAddress>";
      $xml .= "<PurchaseDate>{$date}</PurchaseDate>";
      $xml .= "<OrderNumber>{$orderNum}</OrderNumber>";
      if(is_array($items))
      {
         $xml .= "<Items>";
         foreach($items as $item)
         {
         	if ($item->getParentItemId() != null)
					continue;
         	
				$qty = (int)$item->getQtyOrdered();
            $xml .= "<OrderItem>";
            $name = $item->getName();
            $xml .= "<ProductName><![CDATA[$name]]></ProductName>";
            $sku = $item->getSku();
            $xml .= "<SKU><![CDATA[{$sku}]]></SKU>";
            
            $xml .= "<Quantity>{$qty}</Quantity>";
            $price = $item->getPrice();
            $xml .= "<UnitPrice>{$price}</UnitPrice>";
            $weight = $item->getWeight();
            $xml .= "<Weight>{$weight}</Weight>";
            $status = 'Completed';
            $xml .= "<Status>{$status}</Status>";
            $xml .= "</OrderItem>";
			
         }
         $xml .= "</Items>";
      }
      $xml .= "</Order>";
      
      return $xml;
   }
   
   public function addSubscriberOrder($mail,$order, $mergeVars,$uselist = TRUE)
   {
      $store = Mage::app()->getStore();
      $sourceid = Mage::helper('emaildirect')->config('sourceid',$store->getId());
      $publicationid = Mage::helper('emaildirect')->config('publication',$store->getId());
      
      if($uselist)
         $listid = Mage::helper('emaildirect')->config('list',$store->getId());
      else
         $listid = -1;
      
      $data = "<CustomFields>";
      foreach($mergeVars as $key => $value)
      {
         $data .= "<CustomField><FieldName>{$key}</FieldName><Value><![CDATA[{$value}]]></Value></CustomField>";
      }
      $data .= "</CustomFields>";
      
      $list_data = "";
      
      if ($listid != -1)
         $list_data = "<Lists><int>{$listid}</int></Lists>";
      
      $order_data = "<Orders>" . $this->getOrderXml($order) . "</Orders>";
      
      $xml = "<Subscriber><EmailAddress>{$mail}</EmailAddress>{$data}<SourceID>{$sourceid}</SourceID>{$order_data}<Publications><int>{$publicationid}</int></Publications>{$list_data}<Force>true</Force></Subscriber>";
      
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","","",$xml,false);
      
      return $rc;
   }

	public function addSubscriberTracking($mail,$mergeVars,$uselist = TRUE)
   {
      $store = Mage::app()->getStore();
      $sourceid = Mage::helper('emaildirect')->config('sourceid',$store->getId());
      $publicationid = Mage::helper('emaildirect')->config('publication',$store->getId());
      
      if($uselist)
         $listid = Mage::helper('emaildirect')->config('list',$store->getId());
      else
         $listid = -1;
      
      $data = "<CustomFields>";
      foreach($mergeVars as $key => $value)
      {
         $data .= "<CustomField><FieldName>{$key}</FieldName><Value><![CDATA[{$value}]]></Value></CustomField>";
      }
      $data .= "</CustomFields>";
      
      $list_data = "";
      
      if ($listid != -1)
         $list_data = "<Lists><int>{$listid}</int></Lists>";
      
      $xml = "<Subscriber><EmailAddress>{$mail}</EmailAddress>{$data}<SourceID>{$sourceid}</SourceID><Publications><int>{$publicationid}</int></Publications>{$list_data}<Force>true</Force></Subscriber>";
      
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("subscribers","","",$xml,false);
      
      return $rc;
   }

   public function addOrder($order)
   {
      $xml = $this->getOrderXml($order, $order->getCustomerEmail());
      
      $rc = Mage::getSingleton('emaildirect/wrapper_execute')->sendCommand("orders","","",$xml,false);
      return $rc;
   }
}
