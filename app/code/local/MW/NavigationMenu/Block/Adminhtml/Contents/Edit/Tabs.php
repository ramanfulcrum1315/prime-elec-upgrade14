<?php

class MW_NavigationMenu_Block_Adminhtml_Contents_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('navigationmenu_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('navigationmenu')->__('Content Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('navigationmenu')->__('Content Information'),
          'title'     => Mage::helper('navigationmenu')->__('Content Information'),
          'content'   => $this->getLayout()->createBlock('navigationmenu/adminhtml_contents_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}