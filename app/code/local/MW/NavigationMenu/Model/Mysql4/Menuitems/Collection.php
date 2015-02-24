<?php

class MW_NavigationMenu_Model_Mysql4_Menuitems_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('navigationmenu/menuitems');
    }
    
    protected function _afterLoad()
    {
    	foreach ($this as $item) {
    		$str_storeids = $item ->getStoreIds();
    		$store = explode(",", $str_storeids);// 1,2 => array(1,2)
    		$item->setData('store_ids',$store);
    	}
    
    	parent::_afterLoad();
    }
}