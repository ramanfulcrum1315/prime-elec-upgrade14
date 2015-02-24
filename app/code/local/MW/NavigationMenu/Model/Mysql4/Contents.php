<?php

class MW_NavigationMenu_Model_Mysql4_Contents extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('navigationmenu/contents', 'content_id');
    }
}