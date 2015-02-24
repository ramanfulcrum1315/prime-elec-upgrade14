<?php

class OCM_Brands_Model_Resource_Brands extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the brands_id refers to the key field in your database table.
        $this->_init('brands/brands', 'brands_id');
    }
}