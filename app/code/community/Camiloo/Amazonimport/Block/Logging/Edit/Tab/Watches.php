<?php

class Camiloo_Amazonimport_Block_Logging_Edit_Tab_Watches extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/Loggings/watches.phtml');
  }
}

?>