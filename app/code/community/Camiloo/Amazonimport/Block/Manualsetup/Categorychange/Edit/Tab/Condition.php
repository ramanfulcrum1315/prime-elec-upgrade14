<?php

class Camiloo_Amazonimport_Block_Manualsetup_Categorychange_Edit_Tab_Condition extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/manualsetup/categorychange/condition.phtml');
  }
  
  public function getCategoryCondition($country,$returntheoutput=false){
	
	  $output = "";	
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  $possibles = $amazoncore->getConditionSelectOptions('');
						
		$output .= "<select name='condition' onchange='selectedItemCondition(this.options[this.selectedIndex].value);'>";
		$output .= $possibles;
		$output .= "</select>";
	
	
	  if($returntheoutput == false){
			echo $output;
	  }else{
			return $output;  
	  }
  
  }
    
}

?>