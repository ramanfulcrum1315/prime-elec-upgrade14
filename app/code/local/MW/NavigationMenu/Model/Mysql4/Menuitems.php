<?php

class MW_NavigationMenu_Model_Mysql4_Menuitems extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('navigationmenu/menuitems', 'item_id');
    }
}