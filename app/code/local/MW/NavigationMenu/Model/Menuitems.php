<?php

class MW_NavigationMenu_Model_Menuitems extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('navigationmenu/menuitems');
    }
    
    public function isDisplay(){
    	$status = $this->getStatus();
    	if ($status==1){
    		$storeIds = $this->getStoreIds();
    		$id = Mage::app()->getStore()->getId();
    		foreach ($storeIds as $storeId){
    			if($storeId == 0)
    				return true;
    			if($storeId == $id){
    				return true;
    			}
    		}
    	}
    	return false;
    }
}