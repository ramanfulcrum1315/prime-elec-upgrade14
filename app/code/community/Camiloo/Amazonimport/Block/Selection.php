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

class Camiloo_Amazonimport_Block_Selection extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
  	
        $this->_addButton('findonamazon', array(
            'label'     => Mage::helper('amazonimport')->__('Find Products on Amazon'),
            'onclick'   => "location.href='".$this->getUrl('*/automatch')."'",
            'class'     => '',
        ));
  	
    $this->_controller = 'selection';
    $this->_blockGroup = 'amazonimport';
    
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace') == "com"){
    	$country = " to Amazon.com";
    }
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace') == "uk"){
    	$country = " to Amazon.co.uk";
    }
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace') == "fr"){
    	$country = " to Amazon.fr";
    }
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_selection_marketplace') == "de"){
       	$country = " to Amazon.de";	
    }
    
    
    $this->_headerText = Mage::helper('amazonimport')->__('Select Products to be published').$country;
 
    parent::__construct();
    
    $this->_removeButton('add');
  }
}