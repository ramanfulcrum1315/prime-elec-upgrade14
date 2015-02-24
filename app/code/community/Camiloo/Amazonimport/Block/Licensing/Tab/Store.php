<?php

class Camiloo_Amazonimport_Block_Licensing_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/licensing/store.phtml');
  }
}

?>