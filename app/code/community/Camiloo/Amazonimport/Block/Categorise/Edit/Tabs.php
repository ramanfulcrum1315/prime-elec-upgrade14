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

class Camiloo_Amazonimport_Block_Categorise_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(Mage::helper('catalog')->__('Category Data'));
        $this->setTemplate('widget/tabshoriz.phtml');
    }

    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    protected function _prepareLayout()
    {
   
      $this->addTab('browsenodes', array(
          'label'     => Mage::helper('amazonimport')->__('Category on Amazon'),
          'title'     => Mage::helper('amazonimport')->__('Category on Amazon'),
          'content'   => $this->getLayout()->createBlock('amazonimport/categorise_edit_tab_form')->toHtml()
		  				.$this->getLayout()->createBlock('amazonimport/categorise_edit_tab_browsenodegrid')->toHtml(),
      ));
     
      $this->addTab('extrainfo', array(
          'label'     => Mage::helper('amazonimport')->__('Extra Information'),
          'title'     => Mage::helper('amazonimport')->__('Extra Information'),
          'content'   => $this->getLayout()->createBlock('amazonimport/categorise_edit_tab_further')->toHtml()
      ));
	  	  
      $this->addTab('condition', array(
          'label'     => Mage::helper('amazonimport')->__('Item Condtion'),
          'title'     => Mage::helper('amazonimport')->__('Item Condtion'),
          'content'   => $this->getLayout()->createBlock('amazonimport/categorise_edit_tab_condition')->toHtml()
      ));
     
      return parent::_prepareLayout();
    }
}

