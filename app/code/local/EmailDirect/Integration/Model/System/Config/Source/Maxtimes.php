<?php

class EmailDirect_Integration_Model_System_Config_Source_Maxtimes
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('emaildirect')->__('1 Times')),
            array('value' => 2, 'label' => Mage::helper('emaildirect')->__('2 Times')),
            array('value' => 3, 'label' => Mage::helper('emaildirect')->__('3 Times')),
            array('value' => 4, 'label' => Mage::helper('emaildirect')->__('4 Times')),
            array('value' => 5, 'label' => Mage::helper('emaildirect')->__('5 Times')),
        );
    }
}
