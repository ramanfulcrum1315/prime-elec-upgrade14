<?php

class Camiloo_Amazonimport_Block_Manualsetup_Bulk_Edit_Tab_Download extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/manualsetup/bulk/download.phtml');
  }
}

?>