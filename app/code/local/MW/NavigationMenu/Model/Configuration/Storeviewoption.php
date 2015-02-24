<?php
class MW_Navigationmenu_Model_Configuration_Storeviewoption
{
    public function toOptionArray()
    {
        return Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);      
    }

}