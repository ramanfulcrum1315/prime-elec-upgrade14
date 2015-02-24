<?php

class OCM_Brands_Block_Adminhtml_Brands_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('brands_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('brands')->__('Brand Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('general', array(
          'label'     => Mage::helper('brands')->__('General'),
          'title'     => Mage::helper('brands')->__('General'),
          'content'   => $this->getLayout()->createBlock('brands/adminhtml_brands_edit_tab_general')->toHtml(),
      ));

      $this->addTab('meta', array(
          'label'     => Mage::helper('brands')->__('Meta Data'),
          'title'     => Mage::helper('brands')->__('Meta Data'),
          'content'   => $this->getLayout()->createBlock('brands/adminhtml_brands_edit_tab_meta')->toHtml(),
      ));

     
      return parent::_beforeToHtml();
  }
}