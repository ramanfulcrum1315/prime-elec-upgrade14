<?php

class Camiloo_Amazonimport_Block_Manualsetup_Categorychange_Edit_Tab_Conditionnote extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/manualsetup/categorychange/conditionnote.phtml');
  }
  
  public function getCategoryConditionNote($country,$returntheoutput=false){
	
	  $output = "";	
		
	  $output .= "<textarea name='theconditionnote' id='theconditionnote'></textarea><br />";
	  $output .= "<button class='scalable save' onclick='selectedItemConditionNote($(\"theconditionnote\").value);'>Save</button>";
						
	  if($returntheoutput == false){
			echo $output;
	  }else{
			return $output;  
	  }
  
  }
  
}

?>