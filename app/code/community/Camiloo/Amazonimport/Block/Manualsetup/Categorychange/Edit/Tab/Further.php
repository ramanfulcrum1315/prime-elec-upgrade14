<?php

class Camiloo_Amazonimport_Block_Manualsetup_Categorychange_Edit_Tab_Further extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/manualsetup/categorychange/further.phtml');
  }
  
  public function getCategoryVariationTheme($country,$returntheoutput=false){
	
	  $output = "";	
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  $possibles = $amazoncore->getPossibleVariationThemesSelect($this->getData('itemtype'));
		
		if($possibles == false){
			$output .= "<script type='text/javascript'>selectedVariationTheme('');</script>";
		}else{
			$output .= "<select name='variationtheme' onchange='selectedVariationTheme(this.options[this.selectedIndex].value);'>";
			$output .= $possibles;
			$output .= "</select>";
		}
	  if($returntheoutput == false){
			echo $output;
	  }else{
			return $output;  
	  }
  
  }
  
  public function getCategoryItemType($country){
	
	  $browsenode1id = $this->getData('primarycat');
	  $output = "";	  
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  
	  $output .= "<select name='itemtype' onchange='selectedItemtype(this.options[this.selectedIndex].value);'>";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  $browsenode = Mage::getModel('amazonimport/amazonimportbrowsenodes')->getCollection()
							->addFieldToFilter('browsenode_id',$browsenode1id)
							->addFieldToFilter('country_id',$country);
		foreach($browsenode as $bn){
			break;	
		}
		
   //   $bn = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($browsenode1id);
		
		$output .= $amazoncore->getPossibleItemtypesSelect($bn->getData('category_name'), $country, "");
		$output .= "</select>";
		
	  echo $output;
		
  }
    
}

?>