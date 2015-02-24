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

class Camiloo_Amazonimport_Block_Categorise extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
  	
  	$this->_addButton('bulk_process', array(
            'label'     => Mage::helper('amazonimport')->__('Bulk Process'),
            'onclick'   => "location.href='".$this->getUrl('*/*/bulkprocess')."'",
            'class'     => '',
        ));
        
    $this->_controller = 'categorise';
    $this->_blockGroup = 'amazonimport';
    
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace') == "com"){
    	$country = " for Amazon.com";
    }
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace') == "uk"){
    	$country = " for Amazon.co.uk";
    }
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace') == "fr"){
    	$country = " for Amazon.fr";
    }
    if(Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace') == "de"){
       	$country = " for Amazon.de";	
    }
    
    
    $this->_headerText = Mage::helper('amazonimport')->__('Categorise Products').$country;
 
    parent::__construct();
    
    $this->_removeButton('add');
  }
}