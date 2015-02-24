<?php
error_reporting(E_ALL);
    
    class Terapeak_SalesAnalytics_Helper_Channel extends Mage_Core_Helper_Abstract
    {
        
        public function _construct()
        {
            parent::_construct();
        }
        
        /**
         * This method will be used to load products historical data to sa at first login
         */
        public function loadHistoricalData()
        {
            
            $allProds = Mage::getResourceModel('catalog/product_collection')->getData();
            $prodList = Mage::getModel('terapeak_salesanalytics/request_prodlist');
            $prodSell = Mage::getModel('terapeak_salesanalytics/request_prodsell');
            $session = Mage::helper('salesanalytics/transport_user')->getAdminSession(NULL);
            if (!is_null($session) && !empty($session))
            {
                $channelInfo = Mage::helper('salesanalytics/transport_channel')->getLinkedMagentoChannelInfo(Mage::app()->getStore()->getStoreId());
                $channelId = $channelInfo['magentoChannelId'];
                $sellerName = $channelInfo['magentoSellerName'];
                $prodList->setSellerHandle($sellerName);
                $prodSell->setSellerHandle($sellerName);
                $transport = Mage::helper('salesanalytics/transport_product');
                $orderCollections = Mage::getResourceModel('sales/order_collection')->addAttributeToSelect('*');
                $transactions = array();
                
                foreach ($orderCollections as $order) {
                	try
                	{
                		$txData = $prodSell->setHistoricalTransactions($order);
                    	array_push($transactions, $txData->getData());
                    } catch (Exception $ex) {
                		Mage::log('Exception when trying to get historical data:');
                		Mage::log($ex);
                	}
                }
                
                $collection = array('transactions' => $transactions);
                $transport->callToCustomNotificationEndpoint($session, $collection, $channelId);
                $data = $prodList->setHistoricalData($allProds);
                $transport->callToListingNotificationEndpoint($session, $data, $channelId);
            }
        }
        
    }
    
    ?>