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

class Camiloo_Amazonimport_Block_Logging extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'logging';
    $this->_blockGroup = 'amazonimport';
    $this->_headerText = Mage::helper('amazonimport')->__('Amazon Module Communication Log');
    parent::__construct();
    
    $this->_removeButton('add');
    
         $this->_addButton('manualcron', array(
            'label'     => Mage::helper('adminhtml')->__('Run Timed Job Manually'),
            'onclick'   => "window.location.href='".$this->getUrl('*/logging/manualcron')."';",
            'class'     => 'scalable',
        ), -100);
	
	
         $this->_addButton('datacaches', array(
            'label'     => Mage::helper('adminhtml')->__('Refresh Data Caches'),
            'onclick'   => "window.location.href='".$this->getUrl('*/licensing/index')."?refreshcaches=true';",
            'class'     => 'scalable',
        ), -100);
	
         $this->_addButton('savecomplete', array(
            'label'     => Mage::helper('adminhtml')->__('Clear Log'),
            'onclick'   => "window.location.href='".$this->getUrl('*/*/flushlog')."';",
            'class'     => 'scalable delete',
        ), -100);
		 
		 
         $this->_addButton('resetss', array(
            'label'     => Mage::helper('adminhtml')->__('Restart Surestream System'),
            'onclick'   => "window.location.href='".$this->getUrl('*/*/resetsurestream')."';",
            'class'     => 'scalable delete',
        ), -100);
		 
  }
}