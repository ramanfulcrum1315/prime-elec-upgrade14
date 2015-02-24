<?php

class Camiloo_Kitcreator_Block_Logging_Edit_Tab_Defaults extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/Loggings/defaults.phtml');
  }
}

?>