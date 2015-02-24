<?php
/*
 * Created on Oct 26, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class EmailDirect_Integration_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function debugLog($data)
	{
		if ($this->config('debug'))
		{
			Mage::log($data,null,'emaildirect.log');
		}
	}

	public function getExportFileName($name,$full = true)
	{
		$filename = "{$name}.csv";
		// get name from config page?
		if ($full)
			return Mage::getBaseDir('export').'/' . $filename;
		
		return $filename;
	}

	public function getApiKey($store = null)
	{
		if(is_null($store)){
			$key = $this->config('apikey');
		}else{
			$curstore = Mage::app()->getStore();
			Mage::app()->setCurrentStore($store);
			$key = $this->config('apikey', $store);
			Mage::app()->setCurrentStore($curstore);
		}

		return $key;
	}
	
	private function _config($value,$section, $store = null)
	{
		if (is_null($store))
		{
			$store = Mage::app()->getStore();

			$configscope = Mage::app()->getRequest()->getParam('store');
			
			if ($configscope)
				$store = $configscope;
		}
		
		$realvalue = Mage::getStoreConfig("emaildirect/{$section}/{$value}", $store);

		return $realvalue;
	}

	public function config($value, $store = null)
	{
		return $this->_config($value,'general',$store);
	}
	
	public function exportConfig($value, $store = null)
	{
		return $this->_config($value,'export',$store);
	}
   
   public function getEmailDirectColumnOptions()
   {
      $columns = Mage::getSingleton('emaildirect/wrapper_database')->getAllColumns();
      
      $options = array();
       
      foreach ($columns as $column)
      {
         if ($column->IsCustom == 'true')
         {
            $key = (string)$column->ColumnName;
             
            $options[$key] = $key;
         }
      }

      return $options;
   }
	
	public function getShippingColumnOptions()
   {
      $options = array('shipping_code' => 'Shipping Code', 'shipping_description' => 'Shipping Description', 'carrier_code' => 'Tracking Carrier Code', 'title' => 'Tracking Title', 'number' => 'Tracking Number');
      
      return $options;
   }

	public function getDefaultList($storeId)
	{
		$curstore = Mage::app()->getStore();
		Mage::app()->setCurrentStore($storeId);
		$list = $this->config('list', $storeId);
		Mage::app()->setCurrentStore($curstore);
		return $list;
	}
	public function getDefaultPublication($storeId)
	{
		$curstore = Mage::app()->getStore();
		Mage::app()->setCurrentStore($storeId);
		$publication = $this->config('publication', $storeId);
		Mage::app()->setCurrentStore($curstore);
		return $publication;
	}

	public function getMergeVars($customer, $includeEmail = FALSE)
	{
		$merge_vars = array();
      $maps = unserialize( $this->config('map_fields', $customer->getStoreId()) );
      
		if($maps)
		{
         $this->processMap($merge_vars, $maps, $customer);
      }

      $address_maps = unserialize( $this->config('address_fields', $customer->getStoreId()) );
      
      // Process address
      if ($address_maps)
      {
         $address = $customer->getBillingAddress();
         if ($address)
         {
            $this->processMap($merge_vars, $address_maps, $address);
         }
      }

		return $merge_vars;
	}
	
	private function getTrackingData($order)
	{
		$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                    ->setOrderFilter($order)
                    ->load();
		foreach($shipmentCollection as $_shipment)
		{
			foreach($_shipment->getAllTracks() as $tracknum)
			{
            return $tracknum->getData();
			}
		}
		
		return null;
	}
	
	private function getShippingData($order)
	{
		$data = array();
		
		$data['shipping_code'] = $order->getData('shipping_method');
		$data['shipping_description'] = $order->getData('shipping_description');
		
		$track_data = $this->getTrackingData($order);
		
		if ($track_data != null)
		{
			$data['carrier_code'] = $track_data['carrier_code'];
			$data['title'] = $track_data['title'];
			$data['number'] = $track_data['number'];
		}
		
		$shipping_data = new Varien_Object();
		
		$shipping_data->setData($data);
		
		return $shipping_data;
	}
	
	public function getOrderMergeVars(&$merge_vars, $order)
	{
		$this->debugLog('getOrderMergeVars');
      $maps = unserialize( $this->config('shipping_fields', $order->getStoreId()) );
      
		if($maps)
		{
			$this->debugLog('MAPS:');
			$this->debugLog($maps);
			$shipping_data = $this->getShippingData($order);
			
			$this->debugLog('Shipping Data');
			$this->debugLog($shipping_data);
			
         $this->processMap($merge_vars, $maps, $shipping_data);
      }

		return $merge_vars;
	}
	
	public function getTrackingMergeVars($track, $order)
	{
		$merge_vars = array();
		$this->debugLog('getTrackMergeVars');
      $maps = unserialize( $this->config('shipping_fields', $order->getStoreId()) );
      
		if($maps)
		{
			$this->debugLog('MAPS:');
			$this->debugLog($maps);
			
         $this->processMap($merge_vars, $maps, $track);
      }

		return $merge_vars;
	}

   private function processMap(&$merge_vars,$maps, $data)
   {
      $request = Mage::app()->getRequest();
      
      foreach($maps as $map)
      {
         $customAtt = $map['magento'];
         $emaildirectTag  = $key = $map['emaildirect'];

         if($emaildirectTag && $customAtt)
         {
            switch ($customAtt)
            {
               case 'state_code':
               {
                  $region_id = $data->getData('region_id');
                  
                  $region = Mage::getModel('directory/region')->load($region_id);
                  
                  if (!$region)
                     continue;
                  
                  $state_code = $region->getCode();
                  
                  $merge_vars[$key] = $state_code;
               }
               break;
               case 'date_of_purchase':
                  $last_order = Mage::getResourceModel('sales/order_collection')
                           ->addFieldToFilter('customer_id', $data->getId())
                           ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                           ->setOrder('created_at', 'desc')
                           ->getFirstItem();
                           
                       if ( $last_order->getId() )
                       {
                        $merge_vars[$key] = Mage::helper('core')->formatDate($last_order->getCreatedAt());
                       }

                  break;
               default:

                  if( ($value = (string)$data->getData(strtolower($customAtt)))
                     OR ($value = (string)$request->getPost(strtolower($customAtt))) )
                     {
                     $merge_vars[$key] = $value;
                  }

                  break;
            }

         }
      }
   }

	public function canCheckoutSubscribe()
	{
		return (bool)($this->config('checkout_subscribe') != 0);
//		return Mage::getStoreConfigFlag('emaildirect/general/checkout_subscribe');
	}
	public function canEdirect()
	{
		return (bool)((int)$this->config('active') !== 0);
	}

	public function registerGuestCustomer($order)
	{
		if( Mage::registry('ed_guest_customer') ){
			return;
		}

		$customer = new Varien_Object;

		$customer->setId(time());
		$customer->setEmail($order->getBillingAddress()->getEmail());
		$customer->setStoreId($order->getStoreId());
		$customer->setFirstname($order->getBillingAddress()->getFirstname());
		$customer->setLastname($order->getBillingAddress()->getLastname());
		$customer->setPrimaryBillingAddress($order->getBillingAddress());
		$customer->setPrimaryShippingAddress($order->getShippingAddress());
		Mage::register('ed_guest_customer', $customer, TRUE);

	}

}
