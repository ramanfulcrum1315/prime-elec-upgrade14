<?php
class MW_NavigationMenu_Block_Adminhtml_Contents extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_contents';
    $this->_blockGroup = 'navigationmenu';
    $this->_headerText = Mage::helper('navigationmenu')->__('Menu Contents Manager');
    $this->_addButtonLabel = Mage::helper('navigationmenu')->__('Add Content');
    parent::__construct();
  }
}