<?php
class MW_NavigationMenu_Block_Adminhtml_Menuitems extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_menuitems';
    $this->_blockGroup = 'navigationmenu';
    $this->_headerText = Mage::helper('navigationmenu')->__('Menu Category Manager');
    $this->_addButtonLabel = Mage::helper('navigationmenu')->__('Add Item');
    parent::__construct();
  }
}