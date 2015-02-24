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

class Camiloo_Amazonimport_Block_Manualsetup_Bulk_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('manualsetup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('amazonimport')->__('Manualsetup Product'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('download', array(
          'label'     => Mage::helper('amazonimport')->__('Download Bulk CSV'),
          'title'     => Mage::helper('amazonimport')->__('Download Bulk CSV'),
          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_bulk_edit_tab_download')->toHtml(),
      ));
   
      $this->addTab('help', array(
          'label'     => Mage::helper('amazonimport')->__('Help Completing CSV'),
          'title'     => Mage::helper('amazonimport')->__('Help Completing CSV'),
          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_bulk_edit_tab_help')->toHtml(),
      ));
   
      
      $this->addTab('upload', array(
          'label'     => Mage::helper('amazonimport')->__('Upload Bulk CSV'),
          'title'     => Mage::helper('amazonimport')->__('Upload Bulk CSV'),
          'content'   => $this->getLayout()->createBlock('amazonimport/manualsetup_bulk_edit_tab_upload')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}