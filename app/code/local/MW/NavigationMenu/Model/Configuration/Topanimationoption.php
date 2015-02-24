<?php
class MW_Navigationmenu_Model_Configuration_Topanimationoption
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('navigationmenu')->__('fade')),
            array('value' => 2, 'label'=>Mage::helper('navigationmenu')->__('slide')),
            );        
    }

}