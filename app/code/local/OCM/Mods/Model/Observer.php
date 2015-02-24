<?php


class OCM_Mods_Model_Observer
{

    public function checkPaymentMethod($observer) {
        
        if ($observer->getData('method_instance') instanceof Mage_Payment_Model_Method_Purchaseorder) {
            $observer->getData('result')->isAvailable = 0;
        }
        
    }

}