<?php

class OCM_Brands_Block_Adminhtml_Brands_Updateposition extends Mage_Adminhtml_Block_Widget_Form_Container
{

	protected function _prepareLayout()
    {
        parent::_prepareLayout();
    } 
	
	public function __construct()
    {
        parent::__construct();
                 
        //$this->_objectId = 'id';
        $this->_blockGroup = 'brands';
        $this->_mode = 'updateposition';
        $this->_controller = 'adminhtml_brands';
        
        $this->_updateButton('save', 'onclick', 'updatePositionForm.submit();');
        $this->_removeButton('delete');
        $this->_removeButton('reset');

        $this->_formScripts[] = "
            updatePositionForm = new varienForm('updateposition_form', '');
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('brands_data') && Mage::registry('brands_data')->getId() ) {
            return Mage::helper('brands')->__("Edit Brand '%s'", $this->htmlEscape(Mage::registry('brands_data')->getTitle()));
        } else {
            return Mage::helper('brands')->__('Add Brand');
        }
    }
}