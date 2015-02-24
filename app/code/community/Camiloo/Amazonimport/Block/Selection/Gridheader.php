<?php

class Camiloo_Amazonimport_Block_Selection_Gridheader extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/selectiongridhead.phtml');
  }
}