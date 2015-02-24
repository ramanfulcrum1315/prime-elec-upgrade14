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

class Camiloo_Amazonimport_Block_Massupdate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('manualsetup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('amazonimport')->__('Mass Update'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('download', array(
          'label'     => Mage::helper('amazonimport')->__('Download XLS Template'),
          'title'     => Mage::helper('amazonimport')->__('Download XLS Template'),
          'content'   => $this->getLayout()->createBlock('amazonimport/massupdate_edit_tab_download')->toHtml(),
      ));
   
      $this->addTab('upload', array(
          'label'     => Mage::helper('amazonimport')->__('Upload Completed CSV'),
          'title'     => Mage::helper('amazonimport')->__('Upload Completed CSV'),
          'content'   => $this->getLayout()->createBlock('amazonimport/massupdate_edit_tab_upload')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}