<?php

class MW_NavigationMenu_Block_Adminhtml_Contents_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'navigationmenu';
        $this->_controller = 'adminhtml_contents';
        
        $this->_updateButton('save', 'label', Mage::helper('navigationmenu')->__('Save Content'));
        $this->_updateButton('delete', 'label', Mage::helper('navigationmenu')->__('Delete Content'));
		
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
            return Mage::helper('navigationmenu')->__("Edit Content '%s'", $this->htmlEscape(Mage::registry('navigationmenu_data')->getName()));
        } else {
            return Mage::helper('navigationmenu')->__('Add Content');
        }
    }
}