<?php

class OCM_Brands_Model_Resource_Brands_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
//Mage/Eav/Model/Entity/Collection/Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brands/brands');
    }
	
	public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (!is_string($attribute)) {
            return $this;
        }
        $this->setOrder($attribute, $dir);
        return $this;
    }

}