<?php

class OCM_Callouts_Helper_Data extends Mage_Core_Helper_Abstract
{
 public function getIsNew($product)
    {
        
        $date = Mage::helper('core')->formatDate();
        
        if(!$product->getData('news_from_date')) {
            return false;
        }
        
        $current_date = new DateTime($date); // compare date
        $from_date = new DateTime($product->getData('news_from_date')); // begin date
        $to_date = new DateTime($product->getData('news_to_date')); // end date
        
        $return = ($current_date >= $from_date && $current_date <= $to_date);
        
        return $return;
    } 
}