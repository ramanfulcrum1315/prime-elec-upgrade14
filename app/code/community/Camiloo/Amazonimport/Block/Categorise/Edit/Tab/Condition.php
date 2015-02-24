<?php

class Camiloo_Amazonimport_Block_Categorise_Edit_Tab_Condition extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/categorise/condition.phtml');
  }
  
  public function getCategoryCondition($categoryid, $country){
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "COND");  
	  $output = "";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  
	  if(is_object($cm)){
		  
				if($cm->getData('inherited')){
					  	if($cm->getData('condition') != ""){
							$output = " <b>(Inherited from parent category)</b> ".$amazoncore->FormatCamelCase($cm->getData('condition'));
						}else{
							$output = " <b>(Inherited from parent category)</b> None selected yet. <br /><br />Please visit the parent category to set the item condition for products within this category, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
						}
				}else{
						if($cm->getData('condition') != ""){
							$output = ' '.$amazoncore->FormatCamelCase($cm->getData('condition'))."<br /><br />";
						}else{
							$output = ' None selected yet.'."<br /><br />";
						}
						$possibles = $amazoncore->getConditionSelectOptions($cm->getData('condition'));
						
						$output .= "<select name='condition' onchange='updateCondition(this.options[this.selectedIndex].value);'>";
						$output .= $possibles;
						$output .= "</select>";
				
				}
	  }else{
		$output = "Please select a primary Amazon category before continuing.";		
	  }
  	
	  echo $output;
  
  }
  
    public function getCategoryConditionNote($categoryid, $country){
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "CONDNOTE");  
	  $output = "";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  
	  if(is_object($cm)){
		  
				if($cm->getData('inherited')){
					  	if($cm->getData('condition_note') != ""){
							$output = " <b>(Inherited from parent category)</b> ".$amazoncore->FormatCamelCase($cm->getData('condition_note'));
						}else{
							$output = " <b>(Inherited from parent category)</b> None selected yet. <br /><br />Please visit the parent category to enter a condition note, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
						}
				}else{
						if($cm->getData('condition') != ""){
							$output = ' '.$amazoncore->FormatCamelCase($cm->getData('condition_note'))."<br /><br />";
						}else{
							$output = ' None selected yet.'."<br /><br />";
						}
						$output .= "<textarea name='theconditionnote' id='theconditionnote'></textarea><br />";
						$output .= "<button class='scalable save' onclick='updateConditionNote($(\"theconditionnote\").value);'>Save</button>";
				
				}
	  }else{
		$output = "Please select a primary Amazon category before continuing.";		
	  }
  	
	  echo $output;
  
  }

	
	public function checkforVTITparents($categoryid, $country,$type){
	 
	 	// check for parent categories, and check their mapping settings too.	 
	 	$cat = Mage::getModel('catalog/category')->load($categoryid);
		
	 	if($cat->getParentId()){
	 		return $this->getVTITMappings($cat->getParentId(), $country, $type, true);
		}else{
			return array(0,0);	
		}
		
	}

	public function getVTITMappings($categoryid, $country, $type, $inherited=false){
		
		$cat = Mage::getModel('amazonimport/amazonimportcategorymapping')->getCollection()
			->addFieldToFilter('category_id',$categoryid)
			->addFieldToFilter('country_id',$country);
		$cm = "";
		
		foreach($cat as $cm){
			break;	
		}
			
		if(is_object($cm)){	
			
				if($cm->getData('browsenode1') == 0){
						return $this->checkforVTITparents($categoryid,$country, $type);
				}else{
						return array($cm, $inherited);
				}
				
		}else{
				return $this->checkforVTITparents($categoryid, $country, $type);
		}
	
	}
  
  
}

?>