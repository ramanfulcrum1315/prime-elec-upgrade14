<?php

class Camiloo_Amazonimport_Block_Massupdate_Edit_Tab_Download extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/massupdate/download.phtml');
  }
}

?>