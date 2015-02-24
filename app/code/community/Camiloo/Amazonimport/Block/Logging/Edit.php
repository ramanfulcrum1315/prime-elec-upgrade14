<?php
/**
 * Camiloo Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.camiloo.co.uk/license.txt
 *
 * @category   Camiloo
 * @package    Camiloo_Amazonimport
 * @copyright  Copyright (c) 2011 Camiloo Limited (http://www.camiloo.co.uk)
 * @license    http://www.camiloo.co.uk/license.txt
 */

class Camiloo_Amazonimport_Block_Logging_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amazonimport';
        $this->_controller = 'Logging';
        
        $this->_updateButton('save', 'label', Mage::helper('amazonimport')->__('Send Request'));
		$this->_removeButton('delete');
        
		$this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('amazonimport_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'amazonimport_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'amazonimport_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('amazonimport_data') && Mage::registry('amazonimport_data')->getId() ) {
            return Mage::helper('amazonimport')->__("Send a support request to Camiloo Support");
        } else {
            return Mage::helper('amazonimport')->__('Send a support request to Camiloo Support');
        }
    }
}