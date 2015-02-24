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
 
class Camiloo_Amazonimport_Block_Categorise_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId    = 'entity_id';
        $this->_controller  = 'categorise';
        $this->_mode        = 'edit';

        parent::__construct();
        $this->setTemplate('amazonimport/categorise/edit.phtml');
    }

    protected function _prepareLayout()
    {
		 $this->setChild('form',
            $this->getLayout()->createBlock('amazonimport/categorise_edit_form', 'form')
        );
        $category = Mage::registry('current_category');
        if (Mage::app()->getConfig()->getModuleConfig('Mage_GoogleOptimizer')->is('active', true)
            && Mage::helper('googleoptimizer')->isOptimizerActive($category->getStoreId())) {
            $this->setChild('googleoptimizer_js',
                $this->getLayout()->createBlock('googleoptimizer/js')->setTemplate('googleoptimizer/js.phtml')
            );
        }
        return parent::_prepareLayout();
    }
}

/*
class Camiloo_Amazonimport_Block_Categorise_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amazonimport';
        $this->_controller = 'categorise';
        
        $this->_updateButton('save', 'label', Mage::helper('amazonimport')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('amazonimport')->__('Delete'));
		
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

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
            return Mage::helper('amazonimport')->__("Categorise product '%s'", $this->htmlEscape(Mage::registry('amazonimport_data')->getTitle()));
        } else {
            return Mage::helper('amazonimport')->__('Categorise product');
        }
    }
}
*/