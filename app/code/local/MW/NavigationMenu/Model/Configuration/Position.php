<?php
class MW_Navigationmenu_Model_Configuration_Position
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('navigationmenu')->__('Top menu')),
            array('value' => 2, 'label'=>Mage::helper('navigationmenu')->__('Vertical Menu')),
            );        
    }
}