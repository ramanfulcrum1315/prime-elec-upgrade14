<?php

class MW_NavigationMenu_Model_Contents extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('navigationmenu/contents');
    }
    
    public function isDisplay(){
    	$status = $this->getStatus();
    	if ($status==1){
    		$storeIds = explode(',', $this->getStoreIds());
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