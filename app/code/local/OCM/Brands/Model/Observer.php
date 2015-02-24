<?php

class OCM_Brands_Model_Observer
{
    const BRAND_INDEX_HANDLE = 'brands_index_index';
    const BRAND_INDEX_ICONS_HANDLE = 'brands_index_icons';
    const BRAND_INDEX_TEXT_HANDLE = 'brands_index_text';
    const BRAND_USE_ICONS_XML_PATH = 'brands_options/data/use_icons';

    public function addBrandHandle(Varien_Event_Observer $observer) {
        $update = $observer->getEvent()->getLayout()->getUpdate();
        
        if(!in_array(self::BRAND_INDEX_HANDLE, $update->getHandles())) return;
        
        if(Mage::getStoreConfig(self::BRAND_USE_ICONS_XML_PATH)) {
            $handle = self::BRAND_INDEX_ICONS_HANDLE;
        } else {
            $handle = self::BRAND_INDEX_TEXT_HANDLE;
        }
        
        $update->addHandle($handle);
        
        return;
    }

}