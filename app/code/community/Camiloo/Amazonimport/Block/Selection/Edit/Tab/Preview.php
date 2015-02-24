<?php

class Camiloo_Amazonimport_Block_Selection_Edit_Tab_Preview extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/selection/preview.phtml');
  }
}

?>