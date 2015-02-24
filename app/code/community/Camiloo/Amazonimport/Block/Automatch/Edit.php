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

class Camiloo_Amazonimport_Block_Automatch_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'amazonimport';
        $this->_controller = 'automatch';
        
      	$this->_removeButton('reset');
      	$this->_removeButton('save');
      	$this->_removeButton('delete');
		$this->_removeButton('back');
			$this->_addButton('back', array(
            'label'     => Mage::helper('amazonimport')->__('Back'),
            'onclick'   => "window.location.href='".$this->getUrl('*/selection/*')."';",
            'class'     => 'scalable back',
        ));
    }

    public function getHeaderText()
    {
        if( Mage::registry('amazonimport_data') && Mage::registry('amazonimport_data')->getId() ) {
            return Mage::helper('amazonimport')->__("Clone settings from another marketplace'%s'", $this->htmlEscape(Mage::registry('amazonimport_data')->getTitle()));
        } else {
            return Mage::helper('amazonimport')->__('Find products on Amazon');
        }
    }
}