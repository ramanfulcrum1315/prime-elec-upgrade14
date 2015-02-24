<?php

class EmailDirect_Integration_Block_Adminhtml_System_Config_Form_Field_Mapfields extends EmailDirect_Integration_Block_Adminhtml_System_Config_Form_Field_Common
{

    public function __construct()
    {
        $this->addColumn('magento', array(
            'label' => Mage::helper('emaildirect')->__('Customer'),
            'style' => 'width:120px',
        ));
        $this->addColumn('emaildirect', array(
            'label' => Mage::helper('emaildirect')->__('Emaildirect'),
            'style' => 'width:120px',
            'type'  => 'options',
            'options' => Mage::helper('emaildirect')->getEmailDirectColumnOptions(),
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('emaildirect')->__('Add field');
        parent::__construct();
    }
}