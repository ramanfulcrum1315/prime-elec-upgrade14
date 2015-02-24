<?php

class OCM_Brands_Block_Adminhtml_Brands_Updateposition_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('updateposition_tabs');
      $this->setDestElementId('updateposition_form');
      $this->setTitle(Mage::helper('brands')->__('Brand Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('general', array(
          'label'     => Mage::helper('brands')->__('General'),
          'title'     => Mage::helper('brands')->__('General'),
          'content'   => $this->getLayout()->createBlock('brands/adminhtml_brands_updateposition_tab_general')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}