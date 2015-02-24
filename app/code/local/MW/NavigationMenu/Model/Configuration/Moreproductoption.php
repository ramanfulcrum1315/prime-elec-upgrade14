<?php
class MW_Navigationmenu_Model_Configuration_Moreproductoption
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('navigationmenu')->__('show more products')),
            array('value' => 2, 'label'=>Mage::helper('navigationmenu')->__('link to category page')),
            );        
    }

}