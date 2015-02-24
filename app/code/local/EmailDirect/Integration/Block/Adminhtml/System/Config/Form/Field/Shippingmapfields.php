<?php

class EmailDirect_Integration_Block_Adminhtml_System_Config_Form_Field_Shippingmapfields extends EmailDirect_Integration_Block_Adminhtml_System_Config_Form_Field_Common
{

    public function __construct()
    {
        $this->addColumn('magento', array(
            'label' => Mage::helper('emaildirect')->__('Shipping Field'),
            'type'  => 'options',
            'options' => Mage::helper('emaildirect')->getShippingColumnOptions(),
            'style' => 'width:120px',
        ));
        $this->addColumn('emaildirect', array(
            'label' => Mage::helper('emaildirect')->__('Emaildirect'),
            'type'  => 'options',
            'options' => Mage::helper('emaildirect')->getEmailDirectColumnOptions(),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('emaildirect')->__('Add field');
        parent::__construct();
    }
}