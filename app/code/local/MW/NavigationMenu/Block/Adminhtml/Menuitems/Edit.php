<?php

class MW_NavigationMenu_Block_Adminhtml_Menuitems_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'navigationmenu';
        $this->_controller = 'adminhtml_menuitems';
        
        $this->_updateButton('save', 'label', Mage::helper('navigationmenu')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('navigationmenu')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('navigationmenu_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'navigationmenu_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'navigationmenu_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('navigationmenu_data') && Mage::registry('navigationmenu_data')->getId() ) {
            return Mage::helper('navigationmenu')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('navigationmenu_data')->getTitle()));
        } else {
            return Mage::helper('navigationmenu')->__('Add Item');
        }
    }
}