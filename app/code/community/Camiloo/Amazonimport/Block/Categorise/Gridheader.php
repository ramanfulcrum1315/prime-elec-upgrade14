<?php

class Camiloo_Amazonimport_Block_Categorise_Gridheader extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/categorisegridhead.phtml');
  }
}