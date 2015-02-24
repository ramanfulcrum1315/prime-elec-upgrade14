<?php

class Camiloo_Amazonimport_Block_Automatch_Edit_Tab_Review extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/automatch/review.phtml');
  }
}

?>