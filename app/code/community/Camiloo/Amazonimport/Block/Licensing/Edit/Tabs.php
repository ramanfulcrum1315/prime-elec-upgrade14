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

class Camiloo_Amazonimport_Block_Licensing_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('licensing_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('amazonimport')->__('View / Update License'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('current', array(
          'label'     => Mage::helper('amazonimport')->__('Current License'),
          'title'     => Mage::helper('amazonimport')->__('Current License'),
          'content'   => $this->getLayout()->createBlock('amazonimport/licensing_tab_current')->toHtml(),
      ));
   
      $this->addTab('store', array(
          'label'     => Mage::helper('amazonimport')->__('License Store'),
          'title'     => Mage::helper('amazonimport')->__('License Store'),
          'content'   => $this->getLayout()->createBlock('amazonimport/licensing_tab_store')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}