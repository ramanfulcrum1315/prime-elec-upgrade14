<?php
class MW_Navigationmenu_Model_Configuration_Topspeedoption
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('navigationmenu')->__('fast')),
            array('value' => 2, 'label'=>Mage::helper('navigationmenu')->__('slow')),
            );        
    }

}