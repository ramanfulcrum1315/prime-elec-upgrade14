<?php

class OCM_Brands_Model_Adminhtml_System_Config_Source_Attribute extends Mage_Core_Model_Abstract
{
    public function toOptionArray() {
    
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
            ->addFieldToFilter('frontend_input',array('in',array('multiselect','select')));
        
        $return_array = array();           

        foreach($collection as $attr) {
            $return_array[] = array(
                'value'=>$attr->getAttributeCode(),
                'label'=>$attr->getFrontendLabel(),
            );
                
        }
    
        return $return_array;
    }

}