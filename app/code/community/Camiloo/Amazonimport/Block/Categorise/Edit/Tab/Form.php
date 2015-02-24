<?php

class Camiloo_Amazonimport_Block_Categorise_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
  {
	parent::__construct();  
	$this->setTemplate('amazonimport/categorise/form.phtml');
  }
  public function getCurrentCategoryMapping($categoryid, $inherited = false){
		
		$country = Mage::getModel('amazonimport/amazonimport')->loadSessionValue('camiloo_amazon_categorise_marketplace');
		
		$cat = Mage::getModel('amazonimport/amazonimportcategorymapping')->getCollection()
			->addFieldToFilter('category_id',$categoryid)
			->addFieldToFilter('country_id',$country);
		$cm = "";
		
		foreach($cat as $cm){
			break;	
		}
		
		if(is_object($cm)){	
			
			if(($cm->getData('browsenode1') == 0)&&($cm->getData('browsenode2') == 0)){
					$output = $this->checkforparents($categoryid, $country);
			}else{
					if(($cm->getData('browsenode1') != 0)&&($cm->getData('browsenode2') != 0)){
						// get the browsenode from the database.
						$bn1 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode1'));
						$bn2 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode2'));
						if($cm->getData('inherited') == 0){
							$output = "<center><b>Primary Category:</b> ".$bn1->getData('category_tree_location')." [ <a href='#' onclick='unsetPrimaryCategory();'>Remove</a> ] </center><br />";
							$output .= "<center><b>Secondary Category:</b> ".$bn2->getData('category_tree_location')." [ <a href='#' onclick='unsetSecondaryCategory();'>Remove</a> ] </center>";
						}else{
							$output = "<center><b>Inherited Primary Category:</b> ".$bn1->getData('category_tree_location')."<br />";
							$output .= "<center><b>Inherited Secondary Category:</b> ".$bn2->getData('category_tree_location')."</center>";
						}
						
					}else if($cm->getData('browsenode1') != 0){
						$bn1 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode1'));
						if($cm->getData('inherited') == false){
							$output = "<center><b>Primary Category:</b> ".$bn1->getData('category_tree_location')." [ <a href='#' onclick='unsetPrimaryCategory();'>Remove</a> ] </center>";
						}else{
							$output = "<center><b>Inherited Primary Category:</b> ".$bn1->getData('category_tree_location')."</center>";
						}
						
					}else if($cm->getData('browsenode2') != 0){
						$bn1 = Mage::getModel('amazonimport/amazonimportbrowsenodes')->load($cm->getData('browsenode2'));
						if($cm->getData('inherited') == false){
							$output = "<center><b>Secondary Category:</b> ".$bn1->getData('category_tree_location')." [ <a href='#' onclick='unsetSecondaryCategory();'>Remove</a> ] </center>";
						}else{
							$output = "<center><b>Inherited Secondary Category:</b> ".$bn1->getData('category_tree_location')."</center>";
						}
					}
			}
		}else{
			$output = $this->checkforparents($categoryid, $country);
		}
	
		return $output;
				
  }
  
	public function checkforparents($categoryid, $country){
	 
	 	// check for parent categories, and check their mapping settings too.	 
	 	$cat = Mage::getModel('catalog/category')->load($categoryid);
	 	
	 
	 	if($cat->getParentId()){
	 		return $this->getCurrentCategoryMapping($cat->getParentId(), $country, true);
		}else{
			return "<center><b>No category or categories selected</b></center>";	
		}
		
	}

}

?>