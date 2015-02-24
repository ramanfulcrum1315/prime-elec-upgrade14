<?php

class Camiloo_Amazonimport_Block_Categorise_Edit_Tab_Further extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/categorise/further.phtml');
  }
  
  public function getCategoryVariationTheme($categoryid, $country){
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "VT");  
	  $output = "";
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  
	  if(is_object($cm)){
		  
				if($cm->getData('inherited')){
					if($cm->getData('itemtype') != ""){
					  	if($cm->getData('variation_theme') != ""){
							$output = " <b>(Inherited from parent category)</b> ".$amazoncore->FormatCamelCase($cm->getData('variation_theme'));
						}else{
							$output = " <b>(Inherited from parent category)</b> None selected yet. <br /><br /> Please visit the parent category to set the variation theme, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
						}
					}else{
						$output = " <b>(Inherited from parent category)</b> None selected yet. <br /><br />Please visit the parent category to select an item type before continuing, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
					}
				}else{
					if($cm->getData('itemtype') != ""){
						
						if($cm->getData('variation_theme') != ""){
							$output = ' '.$amazoncore->FormatCamelCase($cm->getData('variation_theme'))."<br /><br />";
						}else{
							$output = ' None selected yet.'."<br /><br />";
						}
						$possibles = $amazoncore->getPossibleVariationThemesSelect($cm->getData('itemtype'));
						
						
						if($possibles == false){
							$output .= "There are no Variation Themes available for this type of Product.";
						}else{
							$output .= "<select name='variationtheme' onchange='updateVariationTheme(this.options[this.selectedIndex].value);'>";
							$output .= $possibles;
							$output .= "</select>";
						}
					}else{
						$output .= "Please select an item type before continuing.";
					}
				}
	  }else{
		$output = "Please select a primary Amazon category before continuing.";		
	  }
  	
	  echo $output;
  
  }
  
  public function getCategoryItemType($categoryid, $country, $inherited=false){
	
	  list($cm, $inherited) = $this->getVTITMappings($categoryid, $country, "IT");  
	  $output = "";	  
	  $amazoncore = Mage::getModel('amazonimport/amazonimport');
	  
	  if(is_object($cm)){
	  
			if($cm->getData('inherited')){
				if($cm->getData('itemtype') != ""){
						$output = ' '."<b>(Inherited from parent category)</b> ".$amazoncore->computeHumanReadibleItemtype($cm, $country);
				}else{
					$output = ' '."<b>(Inherited from parent category)</b> None selected yet.<br /><br />Please visit the parent category to select an item type before continuing, or alternatively please select a primary category under 'Category On Amazon' on this screen to override the inheritance.";
				}
			}else{
				if($cm->getData('browsenode1') != 0){
					if($cm->getData('itemtype') != ""){
						$output = ' '.$amazoncore->computeHumanReadibleItemtype($cm, $country)."<br /><br />";
					}else{
						$output = ' '."None selected<br /><br />";
					}
				$output .= "<select name='itemtype' onchange='updateItemType(this.options[this.selectedIndex].value);'>";
				$amazoncore = Mage::getModel('amazonimport/amazonimport');
                $bn = Mage::getModel('amazonimport/amazonimportbrowsenodes')
                    ->load($cm->getData('browsenode1'));
                
				if (isset($bn)) {
				
					$output .= $amazoncore->getPossibleItemtypesSelect($bn->getData('category_name'), $country, $cm->getItemtype());

				}
				$output .= "</select>";
				}else{
					$output .= "Please select a primary Amazon category before continuing.";
				}
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
			if($type == "VT"){
				if($cm->getData('browsenode1') == 0){
						return $this->checkforVTITparents($categoryid,$country, $type);
				}else{
						return array($cm, $inherited);
				}
			}else{
				if($cm->getData('browsenode1') == 0){
						return $this->checkforVTITparents($categoryid,$country, $type);
				}else{
						return array($cm, $inherited);
				}
			}
		
		}else{
				return $this->checkforVTITparents($categoryid, $country, $type);
		}
	
	}
  
  
}

?>