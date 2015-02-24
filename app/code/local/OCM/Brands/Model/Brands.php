<?php

class OCM_Brands_Model_Brands extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brands/brands');
    }
    public function loadByAttribute($attribute, $value, $additionalAttributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($additionalAttributes)
            ->addFieldToFilter($attribute, $value);
            //->setPage(1,1);

        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }
}