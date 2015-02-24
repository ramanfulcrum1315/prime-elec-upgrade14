<?php
class EmailDirect_Integration_AbandonedController extends Mage_Core_Controller_Front_Action
{
	public function testAction()
	{
		$order = Mage::getModel('sales/order')->load(56);
		
		Zend_debug::dump($order->getData());
		
		 $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($order)
                    ->load();
foreach($shipmentCollection as $_shipment)
{
       Zend_debug::dump($_shipment->getData());
			foreach($_shipment->getAllTracks() as $tracknum)
            {
                Zend_debug::dump($tracknum->getData());
            }
} 

	//$method = $order->getPayment()->getMethodInstance();
	
	//Zend_debug::dump($order->getPayment()->getData());
	//Zend_debug::dump($order->getPayment()->getAdditionalInformation());
	//Zend_debug::dump($order->getPayment()->getMethodInstance()->getPaymentInfo());
	

	}
	
   public function restoreAction()
   {
      $coded_cart = Mage::app()->getRequest()->getParam('cart');
      
      $cart_data = unserialize(base64_decode($coded_cart));
      
      $this->clearCart($cart_data['quote']);
      $this->restoreCartItems($cart_data);
      
      $this->_redirect("checkout/cart");
   }
   
   private function restoreCartItems($cart_data)
   {
      $id_list = explode(",",$cart_data['id']);
      $qty_list = explode(",",$cart_data['qty']);
      
      $quote = $this->getQuote();
      
      foreach ($id_list as $key => $item_id)
      {
         $qty = $qty_list[$key];
         
         $this->addItemToCart($item_id, $qty);
      }
      
      // update our totals, save.
      $quote->getBillingAddress();
      $quote->collectTotals();
      $quote->save();
        
      $this->getCheckout()->setQuoteId($quote->getId());
   }
   
   private function addItemToCart($id, $qty)
   {
      $product = Mage::getModel('catalog/product')->load($id);

      if (!$product->getId())
      {
         //echo "Failed to load product";
         return false;
      }
        
      $data = array(
            'qty' => $qty,
            'options' => array()
         );
      
      $quote = $this->getQuote();
        
      try
      {
         // add the product to our quote
         $quote->addProductAdvanced($product , new Varien_Object($data));

         return true;
      } 
      catch (Exception $e)
      {
         //Zend_Debug::dump($e->getMessage());
         Mage::logException($e);
         return false;
      }
   }
   
   private function removeOldQuote($original_quote_id)
   {
      $quote = Mage::getModel('sales/quote')->load($original_quote_id);
      
      if ($quote->getId() == $original_quote_id)
      {
         $quote->delete();
      }
   }
   
   private function clearCart($original_quote_id)
   {
      $customer_session = Mage::getSingleton('customer/session');
      
      $checkout = $this->getCheckout();
      
      $checkout->clear(); // clear donations
  
      $quote = $this->getQuote();
      
      // Check to see if we need to remove the quote
      // if they aren't logged in then we will need to otherwise when they login the items won't match
      if ($quote->getId() != $original_quote_id)
      {
         $this->removeOldQuote($original_quote_id);
      }

      foreach ($quote->getItemsCollection() as $item) 
      {
         $item->isDeleted(true);
      }
      
      $quote->getBillingAddress();
      $quote->getShippingAddress();
      $quote->getPayment();
      $quote->setEmailDirectAbandonedDate(null);
      
      $customer = $customer_session->getCustomer();
      if ($customer)
         $quote->assignCustomer($customer);
      
      $quote->save();
   }
   
   private function getQuote()
   {
      return Mage::getSingleton('checkout/session')->getQuote();
   }
   
   private function getCheckout()
   {
      return Mage::getSingleton('checkout/session');
   }
}