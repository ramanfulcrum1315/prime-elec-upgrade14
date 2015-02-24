<?php

class MW_NavigationMenu_Model_Mysql4_Contents_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('navigationmenu/contents');
    }
}