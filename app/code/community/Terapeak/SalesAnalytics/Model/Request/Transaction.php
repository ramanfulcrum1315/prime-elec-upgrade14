<?php
    
    class Terapeak_SalesAnalytics_Model_Request_Transaction extends Terapeak_SalesAnalytics_Model_Request_Abstract
    {
        
        public function setModelData($order)
        {
            $quoteData = $order->getQuote()->getData();
            $paymentData = $order->getPayment()->getData();
            $this->setId($order->getIncrementId());
            $timeNow = Mage::getModel('core/date')->timestamp(time());
            $this->setTime($timeNow);
            $this->setShipCost($paymentData['shipping_amount']);
            $this->setCurrencyISOCode($quoteData['global_currency_code']);
            $this->setType("FixedPrice");
            return $this;
        }
        
    }
    
    ?>
